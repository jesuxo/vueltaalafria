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
        // Obtener todas las etapas (incluyendo especiales)
        $stages = Stage::where('is_active', true)
            ->orderBy('type', 'desc')
            ->orderBy('stage_number', 'asc')
            ->get();

        // Separar por tipo
        $regularStages = $stages->where('type', 'stage');
        $specials      = $stages->where('type', 'special');

        $recentPhotos  = Photo::with('stage')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        return view('photos.index', compact('stages', 'regularStages', 'specials', 'recentPhotos'));
    }

    /**
     * Aplicar marca de agua a una imagen
     */
    private function applyWatermark($imagePath, $destPath)
    {
        try {
            // Cargar la imagen original
            $imageInfo = getimagesize($imagePath);
            if (!$imageInfo) return false;

            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $type = $imageInfo[2];

            // Crear la imagen según el tipo
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($imagePath);
                    break;
                default:
                    return false;
            }

            // Cargar el logo (marca de agua) - ajusta la ruta según tu estructura
            $logoPath = public_path('img/logopng.png');
            if (!file_exists($logoPath)) {
                // Intentar otras posibles rutas
                $logoPath = public_path('img/logo.png');
                if (!file_exists($logoPath)) {
                    $logoPath = public_path('build/images/logo.png');
                }
            }

            if (file_exists($logoPath)) {
                $logo = imagecreatefrompng($logoPath);

                if ($logo) {
                    // Obtener dimensiones del logo
                    $logoWidth = imagesx($logo);
                    $logoHeight = imagesy($logo);

                    // Calcular posición (esquina inferior derecha con margen)
                    $margin = 15;
                    $posX = $width - $logoWidth - $margin;
                    $posY = $height - $logoHeight - $margin;

                    // Asegurar que no quede fuera de la imagen
                    if ($posX < 0) $posX = $margin;
                    if ($posY < 0) $posY = $margin;

                    // Fusionar el logo con la imagen (manteniendo transparencia)
                    imagecopy($image, $logo, $posX, $posY, 0, 0, $logoWidth, $logoHeight);
                    imagedestroy($logo);
                }
            }

            // Guardar la imagen con marca de agua
            imagejpeg($image, $destPath, 60); // Calidad 60% para thumbnail

            imagedestroy($image);
            return true;

        } catch (\Exception $e) {
            \Log::error('Error aplicando marca de agua: ' . $e->getMessage());
            // Si falla la marca de agua, al menos guardar la imagen sin ella
            return $this->resizeImageDirect($imagePath, $destPath, 300, 200, true);
        }
    }

    /**
     * Redimensionar imagen sin marca de agua (fallback)
     */
    private function resizeImageDirect($sourcePath, $destPath, $maxWidth, $maxHeight = null, $isThumbnail = false)
    {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) return false;

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];

        if ($isThumbnail && $maxHeight) {
            $targetWidth = $maxWidth;
            $targetHeight = $maxHeight;

            $ratio = max($targetWidth / $width, $targetHeight / $height);
            $newWidth = intval($width * $ratio);
            $newHeight = intval($height * $ratio);

            $dst = imagecreatetruecolor($targetWidth, $targetHeight);
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefilledrectangle($dst, 0, 0, $targetWidth, $targetHeight, $white);
        } else {
            if ($width > $maxWidth) {
                $ratio = $maxWidth / $width;
                $newWidth = $maxWidth;
                $newHeight = intval($height * $ratio);
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }
            $dst = imagecreatetruecolor($newWidth, $newHeight);
        }

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

        if ($isThumbnail && isset($targetWidth)) {
            $x = intval(($targetWidth - $newWidth) / 2);
            $y = intval(($targetHeight - $newHeight) / 2);
            imagecopyresampled($dst, $src, $x, $y, 0, 0, $newWidth, $newHeight, $width, $height);
        } else {
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        }

        imagejpeg($dst, $destPath, 75);

        imagedestroy($src);
        imagedestroy($dst);

        return true;
    }

    /**
     * Redimensionar imagen para FULL (sin marca de agua)
     */
    private function resizeImageFull($sourcePath, $destPath, $maxWidth = 1200)
    {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) return false;

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];

        if ($width > $maxWidth) {
            $ratio = $maxWidth / $width;
            $newWidth = $maxWidth;
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

        // Calidad 80% para imágenes completas
        imagejpeg($dst, $destPath, 80);

        imagedestroy($src);
        imagedestroy($dst);

        return true;
    }

    public function upload(Request $request)
    {
        // Validar request
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            'stage_id' => 'required|integer|exists:stages,id',
            'price' => 'required|numeric|min:0'
        ]);

        $uploaded = [];
        $errors = [];

        // Crear directorios si no existen
        $thumbDir = public_path('img/galeria/thumbs');
        $fullDir = public_path('img/galeria/full');

        if (!file_exists($thumbDir)) mkdir($thumbDir, 0777, true);
        if (!file_exists($fullDir)) mkdir($fullDir, 0777, true);

        foreach ($request->file('photos') as $file) {
            try {
                $originalName = $file->getClientOriginalName();
                $baseName = time() . '_' . uniqid();

                // Nombres diferentes para thumb y full
                $thumbFilename = $baseName . '_thumb.jpg';
                $fullFilename = $baseName . '_full.jpg';

                // Rutas completas
                $thumbPath = $thumbDir . '/' . $thumbFilename;
                $fullPath = $fullDir . '/' . $fullFilename;

                // Guardar temporalmente la imagen redimensionada para thumbnail
                $tempThumbPath = $thumbDir . '/temp_' . $thumbFilename;
                $this->resizeImageDirect($file->getPathname(), $tempThumbPath, 300, 200, true);

                // Aplicar marca de agua al thumbnail (calidad baja)
                $this->applyWatermark($tempThumbPath, $thumbPath);

                // Eliminar archivo temporal
                if (file_exists($tempThumbPath)) unlink($tempThumbPath);

                // Procesar imagen completa (sin marca de agua, calidad media-alta)
                $this->resizeImageFull($file->getPathname(), $fullPath, 1200);

                // Guardar en BD
                $photo = Photo::create([
                    'stage_id' => $request->stage_id,
                    'filename' => $fullFilename,
                    'original_name' => $originalName,
                    'thumbnail_path' => 'img/galeria/thumbs/' . $thumbFilename,
                    'full_path' => 'img/galeria/full/' . $fullFilename,
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

        $thumbPath = public_path($photo->thumbnail_path);
        $fullPath = public_path($photo->full_path);

        if (file_exists($thumbPath)) unlink($thumbPath);
        if (file_exists($fullPath)) unlink($fullPath);

        $photo->delete();

        return response()->json(['success' => true]);
    }
}
