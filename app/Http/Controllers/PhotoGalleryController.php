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
        // Obtener etapas normales
        $stages = Stage::where('is_active', true)
            ->where('type', 'stage')
            ->orderBy('stage_number', 'asc')
            ->get();

        // Obtener categorías especiales
        $specials = Stage::where('is_active', true)
            ->where('type', 'special')
            ->orderBy('id', 'asc')
            ->get();

        return view('home.gallery.index', compact('stages', 'specials'));
    }



    public function serveProtectedImage($id)
    {
        $photo = Photo::findOrFail($id);

        // Usar preview_path si existe, sino thumbnail
        $sourcePath = public_path($photo->preview_path ?? $photo->thumbnail_path);

        if (!file_exists($sourcePath)) {
            abort(404);
        }

        // Crear una imagen protegida en tiempo real
        return $this->createProtectedImage($sourcePath, $photo);
    }

    private function createProtectedImage($sourcePath, $photo)
    {
        try {
            // Cargar la imagen original
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                return response()->file($sourcePath);
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $type = $imageInfo[2];

            // REDUCIR TAMAÑO SIGNIFICATIVAMENTE (máx 600px - calidad baja)
            $maxDimension = 600;
            if ($width > $maxDimension || $height > $maxDimension) {
                $ratio = min($maxDimension / $width, $maxDimension / $height);
                $newWidth = intval($width * $ratio);
                $newHeight = intval($height * $ratio);
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }

            // Crear lienzo
            $dst = imagecreatetruecolor($newWidth, $newHeight);

            // Cargar imagen original
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $src = imagecreatefromjpeg($sourcePath);
                    break;
                case IMAGETYPE_PNG:
                    $src = imagecreatefrompng($sourcePath);
                    break;
                default:
                    return response()->file($sourcePath);
            }

            // Redimensionar
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($src);

            // === MARCA DE AGUA (logo grande y semi-transparente) ===
            $logoPath = public_path('img/lo222go.png');
            if (file_exists($logoPath)) {
                $logo = imagecreatefrompng($logoPath);
                if ($logo) {
                    // Redimensionar logo proporcionalmente (30% del ancho de la imagen)
                    $logoWidth = imagesx($logo);
                    $logoHeight = imagesy($logo);
                    $newLogoWidth = intval($newWidth * 0.3);
                    $newLogoHeight = intval($logoHeight * ($newLogoWidth / $logoWidth));

                    $tempLogo = imagecreatetruecolor($newLogoWidth, $newLogoHeight);
                    imagecopyresampled($tempLogo, $logo, 0, 0, 0, 0, $newLogoWidth, $newLogoHeight, $logoWidth, $logoHeight);

                    // Posición CENTRO
                    $posX = ($newWidth - $newLogoWidth) / 2;
                    $posY = ($newHeight - $newLogoHeight) / 2;

                    imagecopy($dst, $tempLogo, $posX, $posY, 0, 0, $newLogoWidth, $newLogoHeight);

                    imagedestroy($logo);
                    imagedestroy($tempLogo);
                }
            }

            // === TEXTO "PROHIBIDA SU DISTRIBUCIÓN" GRANDE EN EL CENTRO ===
            $textoCentral = "PROHIBIDA SU DISTRIBUCION";
            $textoCentral2 = "© VUELTA A LA FRIA 2026";

            // Colores para el texto central (blanco con borde negro)
            $white = imagecolorallocate($dst, 255, 255, 255);
            $black = imagecolorallocate($dst, 0, 0, 0);
            $red = imagecolorallocate($dst, 255, 0, 0);

            // Calcular posición central para texto grande
            $fontSizeGrande = 5; // Tamaño máximo de fuente integrada (5 = 5px por carácter)
            $textWidth1 = imagefontwidth($fontSizeGrande) * strlen($textoCentral);
            $textHeight1 = imagefontheight($fontSizeGrande);

            $posX1 = ($newWidth - $textWidth1) / 2;
            $posY1 = ($newHeight / 2) - 20;

            // Texto con borde negro (sombra)
            imagestring($dst, $fontSizeGrande, $posX1 - 1, $posY1 - 1, $textoCentral, $black);
            imagestring($dst, $fontSizeGrande, $posX1 + 1, $posY1 - 1, $textoCentral, $black);
            imagestring($dst, $fontSizeGrande, $posX1 - 1, $posY1 + 1, $textoCentral, $black);
            imagestring($dst, $fontSizeGrande, $posX1 + 1, $posY1 + 1, $textoCentral, $black);
            imagestring($dst, $fontSizeGrande, $posX1, $posY1, $textoCentral, $red);

            // Segundo texto (copyright) debajo
            $textWidth2 = imagefontwidth($fontSizeGrande) * strlen($textoCentral2);
            $posX2 = ($newWidth - $textWidth2) / 2;
            $posY2 = $posY1 + $textHeight1 + 10;

            imagestring($dst, $fontSizeGrande, $posX2 - 1, $posY2 - 1, $textoCentral2, $black);
            imagestring($dst, $fontSizeGrande, $posX2 + 1, $posY2 - 1, $textoCentral2, $black);
            imagestring($dst, $fontSizeGrande, $posX2 - 1, $posY2 + 1, $textoCentral2, $black);
            imagestring($dst, $fontSizeGrande, $posX2 + 1, $posY2 + 1, $textoCentral2, $black);
            imagestring($dst, $fontSizeGrande, $posX2, $posY2, $textoCentral2, $white);

            // === TEXTO DE COPYRIGHT REPETIDO EN MOSAICO (más pequeño) ===
            $textColorMosaico = imagecolorallocatealpha($dst, 255, 255, 255, 50);
            $fontSizeMosaico = 2;
            $textMosaico = "© VUELTA A LA FRIA 2026 - PROHIBIDA SU DISTRIBUCION";

            $textWidthMosaico = imagefontwidth($fontSizeMosaico) * strlen($textMosaico);
            $textHeightMosaico = imagefontheight($fontSizeMosaico);

            // Repetir texto en mosaico por toda la imagen (fondo)
            for ($x = -$textWidthMosaico; $x < $newWidth + $textWidthMosaico; $x += $textWidthMosaico + 20) {
                for ($y = -$textHeightMosaico; $y < $newHeight + $textHeightMosaico; $y += $textHeightMosaico + 40) {
                    imagestring($dst, $fontSizeMosaico, $x, $y, $textMosaico, $textColorMosaico);
                }
            }

            // === LÍNEAS DIAGONALES DE PROTECCIÓN ===
            $lineColor = imagecolorallocatealpha($dst, 255, 0, 0, 40);
            for ($i = -$newHeight; $i < $newWidth + $newHeight; $i += 30) {
                imageline($dst, $i, 0, $i + $newHeight, $newHeight, $lineColor);
                imageline($dst, 0, $i, $newWidth, $i + $newWidth, $lineColor);
            }

            // === PATRÓN DE PUNTOS (dificulta restauración por IA) ===
            $dotColor = imagecolorallocatealpha($dst, 0, 0, 0, 70);
            for ($i = 0; $i < ($newWidth * $newHeight) / 100; $i++) {
                imagesetpixel($dst, rand(0, $newWidth - 1), rand(0, $newHeight - 1), $dotColor);
            }

            // Puntos blancos también
            $whiteDotColor = imagecolorallocatealpha($dst, 255, 255, 255, 70);
            for ($i = 0; $i < ($newWidth * $newHeight) / 150; $i++) {
                imagesetpixel($dst, rand(0, $newWidth - 1), rand(0, $newHeight - 1), $whiteDotColor);
            }

            // === GUARDAR CON CALIDAD EXTREMADAMENTE BAJA (15%) ===
            ob_start();
            imagejpeg($dst, null, 15);
            $imageData = ob_get_clean();
            imagedestroy($dst);

            // Devolver la imagen con headers de protección
            return response($imageData)
                ->header('Content-Type', 'image/jpeg')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate, private')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0')
                ->header('X-Content-Type-Options', 'nosniff')
                ->header('Content-Disposition', 'inline');

        } catch (\Exception $e) {
            \Log::error('Error creando imagen protegida: ' . $e->getMessage());
            return response()->file($sourcePath);
        }
    }

    // Obtener fotos de una etapa o especial
    public function getStagePhotos($stageId)
    {
        $photos = Photo::where('stage_id', $stageId)
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->paginate(20);

        return response()->json($photos->items());
    }

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

        foreach ($order->items as $item) {
            $item->photo->display_path = $item->photo->preview_path;
        }

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
