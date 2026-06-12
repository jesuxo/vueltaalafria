<?php
// app/Http/Controllers/Admin/PhotoUploadController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Stage;
use Illuminate\Http\Request;

class PhotoUploadController extends Controller
{
    public function index()
    {
        $stages = Stage::where('is_active', true)
            ->orderBy('type', 'desc')
            ->orderBy('stage_number', 'asc')
            ->get();

        $regularStages = $stages->where('type', 'stage');
        $specials      = $stages->where('type', 'special');

        $recentPhotos  = Photo::with('stage')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        return view('photos.index', compact('stages', 'regularStages', 'specials', 'recentPhotos'));
    }

    /**
     * Redimensionar imagen thumbnail (sin marca de agua, baja calidad)
     */
    private function resizeThumbnail($sourcePath, $destPath)
    {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) return false;

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];

        $targetWidth = 300;
        $targetHeight = 200;

        $ratio = max($targetWidth / $width, $targetHeight / $height);
        $newWidth = intval($width * $ratio);
        $newHeight = intval($height * $ratio);

        $dst = imagecreatetruecolor($targetWidth, $targetHeight);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $targetWidth, $targetHeight, $white);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($sourcePath);
                break;
            default:
                return false;
        }

        $x = intval(($targetWidth - $newWidth) / 2);
        $y = intval(($targetHeight - $newHeight) / 2);
        imagecopyresampled($dst, $src, $x, $y, 0, 0, $newWidth, $newHeight, $width, $height);

        // Calidad MUY BAJA para thumbnail (30%)
        imagejpeg($dst, $destPath, 30);

        imagedestroy($src);
        imagedestroy($dst);

        return true;
    }

    private function createWatermarkedPreview($sourcePath, $destPath)
    {
        try {
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) return false;

            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $type = $imageInfo[2];

            // Redimensionar a tamaño pequeño (max 800px - calidad baja)
            $maxDimension = 800;
            if ($width > $maxDimension || $height > $maxDimension) {
                $ratio = min($maxDimension / $width, $maxDimension / $height);
                $newWidth = intval($width * $ratio);
                $newHeight = intval($height * $ratio);
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }

            $dst = imagecreatetruecolor($newWidth, $newHeight);

            switch ($type) {
                case IMAGETYPE_JPEG:
                    $src = imagecreatefromjpeg($sourcePath);
                    break;
                case IMAGETYPE_PNG:
                    $src = imagecreatefrompng($sourcePath);
                    break;
                default:
                    return false;
            }

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($src);

            // === CAPA 1: MARCA DE AGUA GRANDE EN EL CENTRO ===
            $logoPath = public_path('img/lo222go.png');
            if (file_exists($logoPath)) {
                $logo = imagecreatefrompng($logoPath);
                if ($logo) {
                    $logoWidth = imagesx($logo);
                    $logoHeight = imagesy($logo);

                    // Calcular posición CENTRO
                    $posX = ($newWidth - $logoWidth) / 2;
                    $posY = ($newHeight - $logoHeight) / 2;

                    if ($posX < 0) $posX = 10;
                    if ($posY < 0) $posY = 10;

                    // Aplicar logo con transparencia
                    imagecopy($dst, $logo, $posX, $posY, 0, 0, $logoWidth, $logoHeight);
                    imagedestroy($logo);
                }
            }

            // === CAPA 2: TEXTO DE COPYRIGHT REPETIDO ===
            $textColor = imagecolorallocate($dst, 255, 255, 255);
            $shadowColor = imagecolorallocate($dst, 0, 0, 0);
            $fontSize = 5; // Tamaño fijo de fuente integrada

            // Textos de copyright
            $copyrights = [
                " VUELTA A LA FRIA 2026",
                "PROHIBIDA SU REPRODUCCION",
                "VENTA AUTORIZADA - COMPRA EN VUELTALAFRIA.COM"
            ];

            $yPositions = [10, $newHeight - 30, $newHeight - 50];
            $xCenter = $newWidth / 2;

            foreach ($copyrights as $index => $text) {
                $textWidth = imagefontwidth($fontSize) * strlen($text);
                $x = $xCenter - ($textWidth / 2);
                $y = $yPositions[$index] ?? ($newHeight - 20);

                // Sombra
                imagestring($dst, $fontSize, $x + 1, $y + 1, $text, $shadowColor);
                // Texto
                imagestring($dst, $fontSize, $x, $y, $text, $textColor);
            }

            // === CAPA 3: PATRÓN SEMITRANSPARENTE (opcional) ===
            // Crear un patrón de puntos en toda la imagen
            $patternColor = imagecolorallocatealpha($dst, 255, 255, 255, 70); // Blanco semitransparente
            for ($i = 0; $i < $newWidth; $i += 30) {
                for ($j = 0; $j < $newHeight; $j += 30) {
                    imagefilledellipse($dst, $i, $j, 4, 4, $patternColor);
                }
            }

            // Guardar con CALIDAD MUY BAJA (30%)
            imagejpeg($dst, $destPath, 30);
            imagedestroy($dst);

            return true;

        } catch (\Exception $e) {
            \Log::error('Error creando preview: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Guardar imagen FULL original (en carpeta privada)
     */
    private function saveOriginalImage($sourcePath, $destPath)
    {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) return false;

        $type = $imageInfo[2];

        switch ($type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($sourcePath);
                imagejpeg($src, $destPath, 90); // Alta calidad
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($sourcePath);
                imagepng($src, $destPath, 9); // Máxima calidad PNG
                break;
            default:
                return false;
        }

        imagedestroy($src);
        return true;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:10240', // Aumentado a 10MB
            'stage_id' => 'required|integer|exists:stages,id',
            'price' => 'required|numeric|min:0'
        ]);

        $uploaded = [];
        $errors = [];

        // Directorios
        $thumbDir = public_path('img/galeria/thumbs');
        $previewDir = public_path('img/galeria/previews');
        $originalDir = storage_path('app/private/fotos_originales'); // Carpeta PRIVADA

        // Crear directorios si no existen
        if (!file_exists($thumbDir)) mkdir($thumbDir, 0777, true);
        if (!file_exists($previewDir)) mkdir($previewDir, 0777, true);
        if (!file_exists($originalDir)) mkdir($originalDir, 0777, true);

        foreach ($request->file('photos') as $file) {
            try {
                $originalName = $file->getClientOriginalName();
                $baseName = date('Ymd_His') . '_' . uniqid();

                // Nombres de archivos
                $thumbFilename = $baseName . '_thumb.jpg';
                $previewFilename = $baseName . '_preview.jpg';
                $originalFilename = $baseName . '_original.' . $file->getClientOriginalExtension();

                // Rutas
                $thumbPath = $thumbDir . '/' . $thumbFilename;
                $previewPath = $previewDir . '/' . $previewFilename;
                $originalPath = $originalDir . '/' . $originalFilename;

                // 1. Crear thumbnail (baja calidad, sin marca de agua)
                $this->resizeThumbnail($file->getPathname(), $thumbPath);

                // 2. Crear preview con marca de agua (para mostrar en el modal)
                $this->createWatermarkedPreview($file->getPathname(), $previewPath);

                // 3. Guardar imagen original en carpeta PRIVADA (solo para descarga después de compra)
                $this->saveOriginalImage($file->getPathname(), $originalPath);

                // Guardar en BD
                $photo = Photo::create([
                    'stage_id' => $request->stage_id,
                    'filename' => $originalFilename,
                    'original_name' => $originalName,
                    'thumbnail_path' => 'img/galeria/thumbs/' . $thumbFilename,
                    'preview_path' => 'img/galeria/previews/' . $previewFilename,
                    'original_path' => 'fotos_originales/' . $originalFilename, // Ruta relativa para storage
                    'price' => $request->price,
                    'is_active' => true
                ]);

                $uploaded[] = $photo;

            } catch (\Exception $e) {
                $errors[] = $originalName . ': ' . $e->getMessage();
                \Log::error('Error subiendo foto: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => count($uploaded),
            'errors' => $errors,
            'photos' => $uploaded
        ]);
    }

    public function tag(Request $request, $id)
    {
        $photo = Photo::findOrFail($id);

        $tags = $photo->tags ?? [];

        if ($request->dorsal) {
            $tags['dorsal'] = $request->dorsal;
        }
        if ($request->name) {
            $tags['name'] = $request->name;
        }
        if ($request->team) {
            $tags['team'] = $request->team;
        }

        $photo->tags = $tags;
        $photo->description = $request->description ?? $photo->description;
        $photo->save();

        return response()->json(['success' => true, 'photo' => $photo]);
    }

    public function destroy($id)
    {
        $photo = Photo::findOrFail($id);

        // Eliminar archivos físicos
        $thumbPath = public_path($photo->thumbnail_path);
        $previewPath = public_path($photo->preview_path);
        $originalPath = storage_path('app/private/' . $photo->original_path);

        if (file_exists($thumbPath)) unlink($thumbPath);
        if (file_exists($previewPath)) unlink($previewPath);
        if (file_exists($originalPath)) unlink($originalPath);

        $photo->delete();

        return response()->json(['success' => true]);
    }
}
