<?php

namespace App\Support;

use Illuminate\Support\Str;

class ImagePath
{
    public static function url(?string $path, string $fallback): string
    {
        if (! $path) {
            return self::normalizeFallback($fallback);
        }

        if (Str::contains($path, ['loremflickr.com', 'placehold.co', 'picsum.photos', 'source.unsplash.com'])) {
            return self::normalizeFallback($fallback);
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, 'backend/')) {
            return asset('storage/'.$path);
        }

        return asset($path);
    }

    public static function thumbUrl(?string $path, string $fallback): string
    {
        if (! $path) {
            return self::normalizeFallback($fallback);
        }

        if (Str::contains($path, ['loremflickr.com', 'placehold.co', 'picsum.photos', 'source.unsplash.com'])) {
            return self::normalizeFallback($fallback);
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, 'backend/')) {
            return asset('storage/'.$path);
        }

        $thumbPath = self::thumbPath($path);
        if (is_file(public_path($thumbPath))) {
            return asset($thumbPath);
        }

        return asset($path);
    }

    private static function normalizeFallback(string $fallback): string
    {
        if (Str::startsWith($fallback, ['http://', 'https://', '//'])) {
            return $fallback;
        }

        return asset($fallback);
    }

    public static function thumbPath(string $path): string
    {
        if (Str::contains($path, '/thumbs/')) {
            return $path;
        }

        $directory = trim(pathinfo($path, PATHINFO_DIRNAME), '.');
        $filename = pathinfo($path, PATHINFO_BASENAME);

        return trim($directory, '/').'/thumbs/'.$filename;
    }
}

