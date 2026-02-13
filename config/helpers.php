<?php

declare(strict_types=1);

use App\Core\Config;

if (!function_exists('env')) {
    function env(string $key, ?string $default = null): ?string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($value === false || $value === null) {
            return $default;
        }
        return (string)$value;
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $base = rtrim((string)config('app.base_path', ''), '/');
        if ($path === '') {
            return $base === '' ? '/' : $base;
        }
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $baseUrl = rtrim((string)config('app.url', ''), '/');
        $basePath = rtrim((string)config('app.base_path', ''), '/');
        $path = '/' . ltrim($path, '/');
        return $baseUrl . $basePath . $path;
    }
}

if (!function_exists('upload_url')) {
    function upload_url(string $path = ''): string
    {
        $uploadBaseUrl = rtrim((string)config('app.upload_url', ''), '/');
        // Only use a separate upload base URL for the actual upload page (/upload).
        // For any other path, always use the regular `url()` which is based on APP_URL.
        $path = '/' . ltrim($path ?? '', '/');
        $isUploadPage = preg_match('#^/upload($|/|\?)#', $path) === 1;
        if ($isUploadPage && $uploadBaseUrl !== '') {
            $basePath = rtrim((string)config('app.base_path', ''), '/');
            return $uploadBaseUrl . $basePath . $path;
        }
        return url($path);
    }
}

if (!function_exists('format_brl')) {
    function format_brl(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}

if (!function_exists('phone_mask')) {
    function phone_mask(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';
        if ($digits === '') {
            return '';
        }
        if (strlen($digits) === 11) {
            return sprintf('%s %s %s-%s', substr($digits, 0, 2), substr($digits, 2, 1), substr($digits, 3, 4), substr($digits, 7, 4));
        }
        if (strlen($digits) === 10) {
            return sprintf('%s %s-%s', substr($digits, 0, 2), substr($digits, 2, 4), substr($digits, 6, 4));
        }
        return $digits;
    }
}
