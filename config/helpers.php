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
        if ($uploadBaseUrl === '') {
            return url($path);
        }
        $basePath = rtrim((string)config('app.base_path', ''), '/');
        $path = '/' . ltrim($path, '/');
        return $uploadBaseUrl . $basePath . $path;
    }
}

if (!function_exists('format_brl')) {
    function format_brl(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}
