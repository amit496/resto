<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesImageUploads
{
    protected function uploadCompressedImage(
        UploadedFile $file,
        string $folder,
        int $maxWidth = 1600,
        int $quality = 82,
        int $thumbWidth = 360
    ): array {
        $baseDir = public_path('uploads/'.trim($folder, '/'));
        $thumbDir = $baseDir.'/thumbs';

        if (! is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        if (! is_dir($thumbDir)) {
            mkdir($thumbDir, 0777, true);
        }

        $filename = Str::uuid()->toString().'.jpg';
        $relativePath = 'uploads/'.trim($folder, '/').'/'.$filename;
        $relativeThumbPath = 'uploads/'.trim($folder, '/').'/thumbs/'.$filename;
        $absolutePath = public_path($relativePath);
        $absoluteThumbPath = public_path($relativeThumbPath);

        $rawContents = file_get_contents($file->getRealPath());
        $sourceImage = @imagecreatefromstring($rawContents ?: '');

        if (! $sourceImage) {
            $file->move($baseDir, $filename);

            return [
                'path' => $relativePath,
                'thumb' => $relativePath,
            ];
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        [$newWidth, $newHeight] = $this->getResizedDimensions($sourceWidth, $sourceHeight, $maxWidth);
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($resizedImage, 255, 255, 255);
        imagefill($resizedImage, 0, 0, $white);
        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
        imagejpeg($resizedImage, $absolutePath, $quality);

        [$thumbW, $thumbH] = $this->getResizedDimensions($sourceWidth, $sourceHeight, $thumbWidth);
        $thumbImage = imagecreatetruecolor($thumbW, $thumbH);
        $thumbWhite = imagecolorallocate($thumbImage, 255, 255, 255);
        imagefill($thumbImage, 0, 0, $thumbWhite);
        imagecopyresampled($thumbImage, $sourceImage, 0, 0, 0, 0, $thumbW, $thumbH, $sourceWidth, $sourceHeight);
        imagejpeg($thumbImage, $absoluteThumbPath, $quality);

        imagedestroy($sourceImage);
        imagedestroy($resizedImage);
        imagedestroy($thumbImage);

        return [
            'path' => $relativePath,
            'thumb' => $relativeThumbPath,
        ];
    }

    protected function deleteImageAndThumb(?string $path): void
    {
        if (! $path) {
            return;
        }

        if (Str::startsWith($path, 'backend/')) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return;
        }

        $absolutePath = public_path($path);
        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }

        $thumbAbsolutePath = public_path($this->getThumbPath($path));
        if (is_file($thumbAbsolutePath)) {
            @unlink($thumbAbsolutePath);
        }
    }

    protected function getThumbPath(string $path): string
    {
        if (Str::contains($path, '/thumbs/')) {
            return $path;
        }

        $directory = trim(pathinfo($path, PATHINFO_DIRNAME), '.');
        $filename = pathinfo($path, PATHINFO_BASENAME);

        return trim($directory, '/').'/thumbs/'.$filename;
    }

    private function getResizedDimensions(int $width, int $height, int $maxWidth): array
    {
        if ($width <= $maxWidth) {
            return [$width, $height];
        }

        $ratio = $maxWidth / $width;

        return [$maxWidth, max(1, (int) round($height * $ratio))];
    }
}

