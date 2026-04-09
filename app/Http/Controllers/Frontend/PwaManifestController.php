<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PwaManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $setting = AppSetting::query()->whereNull('restaurant_id')->first()
            ?? AppSetting::query()->first();
        $name = $setting?->app_name ?: config('app.name', 'Foodhub');
        $short = Str::limit($name, 14, '');

        $iconUrl = asset('icons/pwa-icon.svg');
        $origin = rtrim((string) url('/'), '/').'/';

        return response()->json([
            'name' => $name,
            'short_name' => $short,
            'description' => 'Order food online — fast menu, cart & checkout.',
            'start_url' => $origin,
            'scope' => $origin,
            'id' => $origin,
            'display' => 'standalone',
            'display_override' => ['standalone', 'minimal-ui', 'browser'],
            'orientation' => 'any',
            'background_color' => '#000022',
            'theme_color' => '#ff8c00',
            'categories' => ['food', 'shopping'],
            'icons' => [
                [
                    'src' => $iconUrl,
                    'sizes' => '512x512',
                    'type' => 'image/svg+xml',
                    'purpose' => 'any',
                ],
                [
                    'src' => $iconUrl,
                    'sizes' => '512x512',
                    'type' => 'image/svg+xml',
                    'purpose' => 'maskable',
                ],
            ],
        ], 200, [
            'Content-Type' => 'application/manifest+json',
            'Cache-Control' => 'public, max-age=3600',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
