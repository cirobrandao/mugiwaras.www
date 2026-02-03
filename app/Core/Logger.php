<?php

declare(strict_types=1);

namespace App\Core;

final class Logger
{
    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    private static function write(string $level, string $message, array $context): void
    {
        $path = dirname(__DIR__, 2) . '/storage/logs/app.log';
        $line = sprintf(
            "%s [%s] %s %s\n",
            date('c'),
            $level,
            $message,
            $context ? json_encode($context) : ''
        );
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        file_put_contents($path, $line, FILE_APPEND);
    }
}
