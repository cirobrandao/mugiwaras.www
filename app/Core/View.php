<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $name, array $data = []): string
    {
        $path = dirname(__DIR__) . '/Views/' . $name . '.php';
        if (!file_exists($path)) {
            http_response_code(500);
            return 'View not found.';
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $path;
        return (string)ob_get_clean();
    }

    public static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
