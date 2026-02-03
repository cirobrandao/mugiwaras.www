<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/bootstrap.php';

$checks = [];

function add_check(array &$checks, string $label, bool $ok, string $detail = ''): void
{
    $checks[] = ['label' => $label, 'ok' => $ok, 'detail' => $detail];
}

$requiredExtensions = ['pdo_mysql', 'json', 'mbstring', 'openssl', 'zip'];
foreach ($requiredExtensions as $ext) {
    add_check($checks, 'ext:' . $ext, extension_loaded($ext), extension_loaded($ext) ? 'loaded' : 'missing');
}

$bins = [
    'PDFTOPPM_BIN' => (string)config('converters.pdftoppm_bin', ''),
    'UNRAR_BIN' => (string)config('converters.unrar_bin', ''),
    'SEVENZIP_BIN' => (string)config('converters.sevenzip_bin', ''),
];
foreach ($bins as $name => $path) {
    if ($path === '') {
        add_check($checks, 'bin:' . $name, false, 'not configured');
        continue;
    }
    add_check($checks, 'bin:' . $name, is_executable($path), is_executable($path) ? 'ok' : 'not executable');
}

$storageRoot = dirname(__DIR__) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
$libraryRoot = dirname(__DIR__) . '/' . trim((string)config('library.path', 'storage/library'), '/');

add_check($checks, 'storage:path', is_dir($storageRoot), $storageRoot);
add_check($checks, 'storage:writable', is_writable($storageRoot), $storageRoot);
add_check($checks, 'library:path', is_dir($libraryRoot), $libraryRoot);
add_check($checks, 'library:writable', is_writable($libraryRoot), $libraryRoot);

$envRequired = ['APP_URL', 'APP_BASE_PATH', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME'];
foreach ($envRequired as $key) {
    add_check($checks, 'env:' . $key, env($key) !== null && env($key) !== '', env($key) ?? '');
}

$okCount = 0;
$failCount = 0;
foreach ($checks as $c) {
    if ($c['ok']) {
        $okCount++;
    } else {
        $failCount++;
    }
}

foreach ($checks as $c) {
    $status = $c['ok'] ? '[OK] ' : '[FAIL] ';
    $detail = $c['detail'] !== '' ? ' - ' . $c['detail'] : '';
    echo $status . $c['label'] . $detail . PHP_EOL;
}

echo PHP_EOL . 'Summary: ' . $okCount . ' ok, ' . $failCount . ' fail' . PHP_EOL;
exit($failCount > 0 ? 1 : 0);
