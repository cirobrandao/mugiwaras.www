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
    'asset_version' => env('ASSET_VERSION', '1.0.0'),
    'limits' => [
        'trial_chapters' => 5,
        'subscription_warning_hours' => 48,
        'avatar_max_size' => 2 * 1024 * 1024, // 2MB
        'avatar_max_dimension' => 500,
        'news_image_max_size' => 5 * 1024 * 1024, // 5MB
        'upload_chunk_size' => 5 * 1024 * 1024, // 5MB
    ],
];
