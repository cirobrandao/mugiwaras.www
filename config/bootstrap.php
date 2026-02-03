<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use App\Core\Config;

require_once __DIR__ . '/helpers.php';

$root = dirname(__DIR__);

if (file_exists($root . '/.env')) {
    $dotenv = Dotenv::createImmutable($root);
    $dotenv->safeLoad();
}

Config::load([
    'app' => require __DIR__ . '/app.php',
    'database' => require __DIR__ . '/database.php',
    'security' => require __DIR__ . '/security.php',
    'storage' => require __DIR__ . '/storage.php',
    'converters' => require __DIR__ . '/converters.php',
    'library' => require __DIR__ . '/library.php',
]);

date_default_timezone_set((string)config('app.timezone', 'UTC'));
