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
        return \App\Core\CrossDomainAuth::buildUploadUrl($path);
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

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $v = config('app.asset_version', '1.0.0');
        return url($path) . '?v=' . $v;
    }
}

if (!function_exists('time_ago')) {
    function time_ago(?string $datetime): string
    {
        if (empty($datetime)) {
            return 'nunca';
        }
        try {
            $dt = new DateTimeImmutable($datetime);
        } catch (Exception $e) {
            return 'nunca';
        }
        $now = new DateTimeImmutable('now');
        $diff = $now->getTimestamp() - $dt->getTimestamp();
        if ($diff < 60) {
            return 'agora';
        }
        if ($diff < 3600) {
            return 'há ' . (int)floor($diff / 60) . ' min';
        }
        if ($diff < 86400) {
            return 'há ' . (int)floor($diff / 3600) . ' h';
        }
        if ($diff < 2592000) {
            return 'há ' . (int)floor($diff / 86400) . ' d';
        }
        if ($diff < 31536000) {
            return 'há ' . (int)floor($diff / 2592000) . ' meses';
        }
        return 'há ' . (int)floor($diff / 31536000) . ' anos';
    }
}

if (!function_exists('time_ago_compact')) {
    function time_ago_compact(?string $datetime): string
    {
        if (empty($datetime)) {
            return '-';
        }
        try {
            $dt = new DateTimeImmutable($datetime);
        } catch (Exception $e) {
            return '-';
        }
        $now = new DateTimeImmutable('now');
        $diff = $now->getTimestamp() - $dt->getTimestamp();
        if ($diff < 0) {
            $diff = 0;
        }
        $days = (int)floor($diff / 86400);
        $hours = (int)floor(($diff % 86400) / 3600);
        $mins = (int)floor(($diff % 3600) / 60);
        if ($days > 0) {
            return $days . 'd ' . $hours . 'h';
        }
        if ($hours > 0) {
            return $hours . 'h ' . $mins . 'm';
        }
        return $mins . 'm';
    }
}

if (!function_exists('mid_ellipsis')) {
    function mid_ellipsis(string $text, int $max = 32, int $tail = 7): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }
        if (mb_strlen($text) <= $max) {
            return $text;
        }
        $head = max(0, $max - $tail - 3);
        if ($head <= 0) {
            return mb_substr($text, 0, $max - 3) . '...';
        }
        return mb_substr($text, 0, $head) . '...' . mb_substr($text, -$tail);
    }
}

if (!function_exists('truncate')) {
    function truncate(string $text, int $max = 100): string
    {
        if (mb_strlen($text) <= $max) {
            return $text;
        }
        return mb_strimwidth($text, 0, $max, '...');
    }
}
