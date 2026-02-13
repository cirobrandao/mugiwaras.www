<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'MWS'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', 'false') === 'true',
    'url' => rtrim(env('APP_URL', ''), '/'),
    'upload_url' => rtrim(env('APP_UPLOAD_URL', ''), '/'),
    'base_path' => rtrim(env('APP_BASE_PATH', ''), '/'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
];
