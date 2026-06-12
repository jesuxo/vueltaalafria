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

    /**
     * Crear preview con protección extrema contra IA
     */
    private function createWatermarkedPreview($sourcePath, $destPath)
    {
        try {
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) return false;

            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $type = $imageInfo[2];

            // REDUCIR TAMAÑO A 600px (máximo) - inútil para impresión
            $maxDimension = 600;
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

            // === CAPA 1: MÚLTIPLES MARCAS DE AGUA ===
            $logoPath = public_path('img/logopng.png');
            if (file_exists($logoPath)) {
                $logo = imagecreatefrompng($logoPath);
                if ($logo) {
                    $logoWidth = imagesx($logo);
                    $logoHeight = imagesy($logo);

                    // Marca 1: Centro
                    $posX = ($newWidth - $logoWidth) / 2;
                    $posY = ($newHeight - $logoHeight) / 2;
                    if ($posX < 0) $posX = 10;
                    if ($posY < 0) $posY = 10;
                    imagecopy($dst, $logo, $posX, $posY, 0, 0, $logoWidth, $logoHeight);

                    // Marca 2: Esquina superior izquierda
                    imagecopy($dst, $logo, 10, 10, 0, 0, $logoWidth, $logoHeight);

                    // Marca 3: Esquina superior derecha
                    imagecopy($dst, $logo, $newWidth - $logoWidth - 10, 10, 0, 0, $logoWidth, $logoHeight);

                    // Marca 4: Esquina inferior izquierda
                    imagecopy($dst, $logo, 10, $newHeight - $logoHeight - 10, 0, 0, $logoWidth, $logoHeight);

                    // Marca 5: Esquina inferior derecha
                    imagecopy($dst, $logo, $newWidth - $logoWidth - 10, $newHeight - $logoHeight - 10, 0, 0, $logoWidth, $logoHeight);

                    imagedestroy($logo);
                }
            }

            // === CAPA 2: TEXTO DE COPYRIGHT EN TODA LA IMAGEN ===
            $textColor = imagecolorallocate($dst, 200, 200, 200);
            $fontSize = 3;
            $text = "© VUELTA A LA FRÍA 2026 - PROHIBIDA SU REPRODUCCIÓN";

            // Texto repetido en mosaico
            $textWidth = imagefontwidth($fontSize) * strlen($text);
            $textHeight = imagefontheight($fontSize);

            for ($x = -$textWidth; $x < $newWidth + $textWidth; $x += $textWidth + 20) {
                for ($y = -$textHeight; $y < $newHeight + $textHeight; $y += $textHeight + 30) {
                    imagestring($dst, $fontSize, $x, $y, $text, $textColor);
                }
            }

            // === CAPA 3: DAÑO DE PÍXELES (PIXELACIÓN INTENCIONAL) ===
            // Crear efecto de pixeleación para dificultar restauración por IA
            $pixelSize = 4; // Tamaño del pixel
            for ($y = 0; $y < $newHeight; $y += $pixelSize) {
                for ($x = 0; $x < $newWidth; $x += $pixelSize) {
                    $rgb = imagecolorat($dst, $x, $y);
                    imagefilledrectangle($dst, $x, $y, $x + $pixelSize - 1, $y + $pixelSize - 1, $rgb);
                }
            }

            // === CAPA 4: RUIDO (NOISE) ===
            $noiseColor = imagecolorallocate($dst, 255, 255, 255);
            for ($i = 0; $i < ($newWidth * $newHeight) / 100; $i++) {
                imagesetpixel($dst, rand(0, $newWidth - 1), rand(0, $newHeight - 1), $noiseColor);
            }

            // === CAPA 5: BANDA DE COLOR DISTORSIONADA ===
            $bandColor = imagecolorallocate($dst, 0, 150, 200);
            for ($i = 0; $i < $newHeight; $i += 50) {
                imagefilledrectangle($dst, 0, $i, $newWidth, $i + 2, $bandColor);
            }

            // Guardar con CALIDAD EXTREMADAMENTE BAJA (15%)
            imagejpeg($dst, $destPath, 15);
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
