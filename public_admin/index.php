<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/bootstrap.php';

$debug = (bool)config('app.debug', false);
if ($debug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

$security = config('security');

session_name((string)$security['session_cookie']);
$sessionDomain = trim((string)($security['session_domain'] ?? ''));
$sessionParams = [
    'path' => '/',
    'secure' => (bool)$security['session_secure'],
    'httponly' => true,
    'samesite' => (string)$security['session_samesite'],
];
if ($sessionDomain !== '') {
    $sessionParams['domain'] = $sessionDomain;
}
session_set_cookie_params($sessionParams);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

foreach ($security['headers'] as $k => $v) {
    header($k . ': ' . $v);
}

$request = new \App\Core\Request();
$router = new \App\Core\Router();

$router->get('/assets/bootstrap.min.css', function (): void {
    $path = dirname(__DIR__) . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: text/css; charset=utf-8');
    readfile($path);
});

$router->get('/assets/bootstrap.min.css.map', function (): void {
    $path = dirname(__DIR__) . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css.map';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: application/json; charset=utf-8');
    readfile($path);
});

$router->get('/assets/bootstrap.bundle.min.js', function (): void {
    $path = dirname(__DIR__) . '/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: application/javascript; charset=utf-8');
    readfile($path);
});

$router->get('/assets/bootstrap.bundle.min.js.map', function (): void {
    $path = dirname(__DIR__) . '/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js.map';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: application/json; charset=utf-8');
    readfile($path);
});

$router->get('/assets/css/theme.css', function (): void {
    $path = dirname(__DIR__) . '/public/assets/css/theme.css';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: text/css; charset=utf-8');
    readfile($path);
});

$router->get('/assets/css/app.css', function (): void {
    $path = dirname(__DIR__) . '/public/assets/css/app.css';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: text/css; charset=utf-8');
    readfile($path);
});

$router->get('/assets/css/upload-admin.css', function (): void {
    $path = dirname(__DIR__) . '/public/assets/css/upload-admin.css';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: text/css; charset=utf-8');
    readfile($path);
});

$router->get('/assets/category-tags.css', function (): void {
    $path = dirname(__DIR__) . '/public/assets/category-tags.css';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: text/css; charset=utf-8');
    readfile($path);
});

$router->get('/assets/js/phone-mask.js', function (): void {
    $path = dirname(__DIR__) . '/public/assets/js/phone-mask.js';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: application/javascript; charset=utf-8');
    readfile($path);
});

$router->get('/assets/js/app.js', function (): void {
    $path = dirname(__DIR__) . '/public/assets/js/app.js';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: application/javascript; charset=utf-8');
    readfile($path);
});

$router->get('/assets/js/theme.js', function (): void {
    $path = dirname(__DIR__) . '/public/assets/js/theme.js';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: application/javascript; charset=utf-8');
    readfile($path);
});

$router->get('/assets/js/upload.js', function (): void {
    $path = dirname(__DIR__) . '/public/assets/js/upload.js';
    if (!is_file($path)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: application/javascript; charset=utf-8');
    readfile($path);
});

$router->get('/', function (): void {
    App\Core\Response::redirect(base_path('/login'));
});

$router->get('/login', [new App\Controllers\UploadAdminController(), 'loginForm']);
$router->post('/login', [new App\Controllers\UploadAdminController(), 'login']);
$router->get('/logout', [new App\Controllers\UploadAdminController(), 'logout']);
$router->get('/upload', [new App\Controllers\UploadAdminController(), 'form']);
$router->post('/upload', [new App\Controllers\UploadAdminController(), 'submit']);

$router->dispatch($request);
