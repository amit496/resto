<?php

namespace App\Support;

class CountryCatalog
{
    public static function options(?string $locale = null): array
    {
        $locale ??= self::locale();
        $bundle = self::bundle($locale);
        $countries = [];
        if ($bundle instanceof \ResourceBundle) {
            $countries = $bundle->get('Countries');
        }
        $options = [];

        foreach ($countries as $code => $name) {
            $code = strtoupper((string) $code);
            if (! preg_match('/^[A-Z]{2}$/', $code)) {
                continue;
            }

            $options[] = [
                'code' => $code,
                'name' => is_string($name) && $name !== '' ? $name : $code,
            ];
        }

        usort($options, fn (array $left, array $right) => strcmp($left['name'], $right['name']));

        return $options;
    }

    public static function displayName(?string $code, ?string $locale = null): string
    {
        $code = self::normalize($code);
        if (! $code) {
            return '-';
        }

        $locale ??= self::locale();
        $bundle = self::bundle($locale);
        $name = null;
        if ($bundle instanceof \ResourceBundle) {
            $countries = $bundle->get('Countries');
            if ($countries instanceof \ResourceBundle) {
                $name = $countries->get($code);
            }
        }
        if (! is_string($name) || $name === '') {
            $name = \Locale::getDisplayRegion('-'.$code, $locale);
        }

        return $name && $name !== $code ? $name : $code;
    }

    public static function normalize(?string $code): ?string
    {
        $code = strtoupper(trim((string) $code));

        return $code !== '' ? substr($code, 0, 2) : null;
    }

    private static function locale(): string
    {
        try {
            if (function_exists('app')) {
                $app = app();
                if (method_exists($app, 'getLocale')) {
                    return $app->getLocale() ?: 'en';
                }
                if ($app->bound('translator')) {
                    return app('translator')->getLocale() ?: 'en';
                }
            }
        } catch (\Throwable) {
            // Fall back to English when the container is not fully booted.
        }

        return 'en';
    }

    private static function bundle(string $locale): ?\ResourceBundle
    {
        try {
            return new \ResourceBundle($locale, 'ICUDATA-region');
        } catch (\Throwable) {
            return null;
        }
    }
}
