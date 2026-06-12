<?php
// app/Http/Controllers/PhotoDownloadController.php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoDownloadController extends Controller
{
    /**
     * Descargar foto original después de compra verificada
     */
    public function download($photoId, $code)
    {
        // Verificar que el código sea válido (puede ser el código del pedido o un token)
        $order = PhotoOrder::where('public_code', $code)
            ->orWhere('order_number', $code)
            ->where('status', 'completed') // Solo si el pedido está completado
            ->first();

        if (!$order) {
            abort(403, 'No tienes permiso para descargar esta foto. El pedido no existe o no está completado.');
        }

        // Verificar que la foto pertenezca al pedido
        $orderItem = $order->items()->where('photo_id', $photoId)->first();

        if (!$orderItem) {
            abort(403, 'Esta foto no pertenece a tu pedido.');
        }

        // Obtener la foto
        $photo = Photo::findOrFail($photoId);

        // Verificar que el archivo original existe
        $originalPath = storage_path('app/private/' . $photo->original_path);

        if (!file_exists($originalPath)) {
            abort(404, 'El archivo de la foto no se encuentra disponible.');
        }

        // Registrar la descarga
        $photo->increment('downloads');

        // Obtener la extensión del archivo
        $extension = pathinfo($photo->original_path, PATHINFO_EXTENSION);
        $filename = 'foto_' . $photo->id . '_' . time() . '.' . $extension;

        // Retornar el archivo para descarga
        return response()->download($originalPath, $filename, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Descargar todas las fotos de un pedido (como ZIP)
     */
    public function downloadAll($code)
    {
        $order = PhotoOrder::where('public_code', $code)
            ->orWhere('order_number', $code)
            ->where('status', 'completed')
            ->first();

        if (!$order) {
            abort(403, 'No tienes permiso para descargar estas fotos.');
        }

        // Crear archivo ZIP temporal
        $zipFileName = 'pedido_' . $order->order_number . '_' . time() . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Crear directorio temporal si no existe
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            abort(500, 'No se pudo crear el archivo ZIP.');
        }

        // Agregar cada foto al ZIP
        foreach ($order->items as $item) {
            $photo = $item->photo;
            $originalPath = storage_path('app/private/' . $photo->original_path);

            if (file_exists($originalPath)) {
                $extension = pathinfo($photo->original_path, PATHINFO_EXTENSION);
                $filename = 'foto_' . $photo->id . '_' . $photo->filename;
                $zip->addFile($originalPath, $filename);

                // Incrementar contador de descargas
                $photo->increment('downloads');
            }
        }

        $zip->close();

        // Retornar el ZIP para descarga
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Verificar estado del pedido y obtener enlaces de descarga
     */
    public function checkAndGetLinks($code)
    {
        $order = PhotoOrder::where('public_code', $code)
            ->orWhere('order_number', $code)
            ->with(['items.photo'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        if ($order->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Las fotos aún no están disponibles. Estado actual: ' . $order->status,
                'status' => $order->status
            ]);
        }

        // Generar enlaces firmados para cada foto (opcional, más seguro)
        $photos = [];
        foreach ($order->items as $item) {
            $photos[] = [
                'id' => $item->photo->id,
                'download_url' => route('photo.download', [
                    'photoId' => $item->photo->id,
                    'code' => $order->public_code
                ]),
                'thumbnail' => $item->photo->thumbnail_path,
                'price' => $item->price
            ];
        }

        return response()->json([
            'success' => true,
            'order' => [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'status' => $order->status,
                'total' => $order->total,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'paid_at' => $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : null,
                'delivered_at' => $order->delivered_at ? $order->delivered_at->format('d/m/Y H:i') : null
            ],
            'photos' => $photos,
            'download_all_url' => route('photo.download.all', $order->public_code)
        ]);
    }
}
