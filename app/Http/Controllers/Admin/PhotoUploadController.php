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
            ->orderBy('stage_number', 'asc')
            ->get();

        $recentPhotos = Photo::with('stage')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        return view('photos.index', compact('stages', 'recentPhotos'));
    }

    private function resizeImage($sourcePath, $destPath, $maxWidth, $maxHeight = null, $isThumbnail = false)
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

    public function upload(Request $request)
    {
        // Validar request - ahora recibe stage_id
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            'stage_id' => 'required|integer|exists:stages,id',  // Cambiado: stage_id
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
                $filename = time() . '_' . uniqid() . '.jpg';

                // Procesar thumbnail (300x200)
                $thumbPath = $thumbDir . '/' . $filename;
                $this->resizeImage($file->getPathname(), $thumbPath, 300, 200, true);

                // Procesar imagen completa (max 1200px)
                $fullPath = $fullDir . '/' . $filename;
                $this->resizeImage($file->getPathname(), $fullPath, 1200, null, false);

                // Guardar en BD - usando stage_id
                $photo = Photo::create([
                    'stage_id' => $request->stage_id,  // Cambiado: stage_id en lugar de stage
                    'filename' => $filename,
                    'original_name' => $originalName,
                    'thumbnail_path' => 'img/galeria/thumbs/' . $filename,
                    'full_path' => 'img/galeria/full/' . $filename,
                    'price' => $request->price,
                    'is_active' => true
                ]);

                $uploaded[] = $photo;

            } catch (\Exception $e) {
                $errors[] = $originalName . ': ' . $e->getMessage();
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
