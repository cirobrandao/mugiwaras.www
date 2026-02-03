<?php

declare(strict_types=1);

return [
    'session_cookie' => env('SESSION_COOKIE_NAME', 'mws_session'),
    'session_secure' => env('SESSION_SECURE', 'true') === 'true',
    'session_samesite' => env('SESSION_SAMESITE', 'Lax'),
    'remember_days' => (int)env('REMEMBER_ME_DAYS', '30'),
    'rate_limit' => [
        'login' => (int)env('RATE_LIMIT_LOGIN', '10'),
        'support' => (int)env('RATE_LIMIT_SUPPORT', '5'),
        'window' => (int)env('RATE_LIMIT_WINDOW', '300'),
    ],
    'headers' => [
        'Content-Security-Policy' => env('CSP_DEFAULT', "default-src 'self'; frame-ancestors 'self'"),
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
    ],
    'download_secret' => env('DOWNLOAD_SECRET', ''),
];
