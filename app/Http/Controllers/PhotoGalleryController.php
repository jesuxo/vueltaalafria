<?php
// app/Http/Controllers/PhotoGalleryController.php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoOrder;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PhotoGalleryController extends Controller
{
    // Mostrar galería por etapas
    public function index()
    {
        $stages = Stage::where('is_active', true)
            ->orderBy('stage_number', 'asc')
            ->get();

        return view('home.gallery.index', compact('stages'));
    }

    // Obtener fotos de una etapa específica (para AJAX)
    public function getStagePhotos($stageId)
    {
        $photos = Photo::where('stage_id', $stageId)
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->paginate(20);

        return response()->json($photos->items());
    }

    // Buscar fotos por dorsal o nombre
    public function searchPhotos(Request $request)
    {
        $query = $request->get('q');

        $photos = Photo::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('tags', 'LIKE', '%' . $query . '%')
                    ->orWhere('description', 'LIKE', '%' . $query . '%')
                    ->orWhere('original_name', 'LIKE', '%' . $query . '%');
            })
            ->limit(50)
            ->get();

        return response()->json($photos);
    }

    // Crear pedido de fotos
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'photos' => 'required|json',
            'payment_method' => 'required|string|in:transferencia,bancolombia,usdt',
            'payment_reference' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Decodificar fotos del JSON
        $photosData = json_decode($request->photos, true);

        if (empty($photosData)) {
            return response()->json([
                'success' => false,
                'message' => 'No se seleccionaron fotos'
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Calcular total
            $subtotal = 0;
            $items = [];

            foreach ($photosData as $photoData) {
                $photo = Photo::find($photoData['id']);
                if ($photo) {
                    $subtotal += $photo->price;
                    $items[] = [
                        'photo' => $photo,
                        'price' => $photo->price
                    ];
                }
            }

            $total = $subtotal;

            // Crear el pedido
            $order = PhotoOrder::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'subtotal' => $subtotal,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'status' => 'pending'
            ]);

            // Agregar items
            foreach ($items as $item) {
                $order->items()->create([
                    'photo_id' => $item['photo']->id,
                    'price' => $item['price']
                ]);
            }

            // Guardar comprobante si se subió
            $paymentProofPath = null;
            if ($request->hasFile('payment_proof')) {
                $paymentProofPath = $this->savePaymentProof($request->file('payment_proof'), $order->id);
                $order->payment_proof = $paymentProofPath;
                $order->save();
            }

            DB::commit();

            // Generar URL pública del pedido
            $publicUrl = route('public.order.show', $order->public_code);

            // Generar QR (si tienes la librería)
            $qrCode = null;
            if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                $qrCode = base64_encode(QrCode::format('png')->size(200)->generate($publicUrl));
            }

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente',
                'order_number' => $order->order_number,
                'public_code' => $order->public_code,
                'public_url' => $publicUrl,
                'qr_code' => $qrCode
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creando pedido: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    // Ver pedido público (sin autenticación)
    public function showPublicOrder($publicCode)
    {
        $order = PhotoOrder::where('public_code', $publicCode)
            ->with(['items.photo'])
            ->firstOrFail();

        return view('home.gallery.order-status', compact('order'));
    }

    // Verificar estado del pedido (página pública simple)
    public function checkOrderStatus($publicCode)
    {
        $order = PhotoOrder::where('public_code', $publicCode)
            ->with(['items.photo'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        $statusLabels = [
            'pending' => 'Pendiente de pago',
            'paid' => 'Pago confirmado',
            'processing' => 'Procesando',
            'completed' => 'Completado - Fotos listas',
            'cancelled' => 'Cancelado'
        ];

        $statusColors = [
            'pending' => 'warning',
            'paid' => 'info',
            'processing' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];

        return response()->json([
            'success' => true,
            'order' => [
                'public_code' => $order->public_code,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'status' => $order->status,
                'status_label' => $statusLabels[$order->status],
                'status_color' => $statusColors[$order->status],
                'total' => $order->total,
                'photos_count' => $order->items->count(),
                'payment_method' => $order->payment_method,
                'payment_reference' => $order->payment_reference,
                'paid_at' => $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : null,
                'delivered_at' => $order->delivered_at ? $order->delivered_at->format('d/m/Y H:i') : null
            ]
        ]);
    }

    private function savePaymentProof($file, $orderId)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = 'foto_order_' . $orderId . '_' . time();

        $uploadDir = public_path('img/comprobantes_fotos');

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $finalFilename = $filename . '.jpg';
            $finalPath = $uploadDir . '/' . $finalFilename;

            $image = $extension == 'png' ? imagecreatefrompng($file->getPathname()) : imagecreatefromjpeg($file->getPathname());
            if ($image) {
                imagejpeg($image, $finalPath, 70);
                imagedestroy($image);
                return 'img/comprobantes_fotos/' . $finalFilename;
            }
        }

        $finalFilename = $filename . '.' . $extension;
        $file->move($uploadDir, $finalFilename);
        return 'img/comprobantes_fotos/' . $finalFilename;
    }
}
