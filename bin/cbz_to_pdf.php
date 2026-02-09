<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Core\Database;
use App\Models\Series;

$options = getopt('', ['limit::', 'series::', 'content::', 'dry-run', 'force', 'magick::', 'max-width::', 'quality::', 'limit-memory::', 'limit-map::', 'limit-disk::', 'help']);
if (isset($options['help'])) {
    echo "Usage: php bin/cbz_to_pdf.php [--series=ID] [--content=ID] [--limit=50] [--dry-run] [--force] [--magick=PATH]" .
        " [--max-width=1600] [--quality=85] [--limit-memory=256MiB] [--limit-map=512MiB] [--limit-disk=2GiB]\n";
    echo "\nDefaults:\n";
    echo "  --max-width=1600\n";
    echo "  --quality=85\n";
    echo "  --limit-memory=256MiB\n";
    echo "  --limit-map=512MiB\n";
    echo "  --limit-disk=2GiB\n";
    echo "\nNotas:\n";
    echo "  - Gera somente o arquivo PDF ao lado do CBZ.\n";
    echo "  - Nao cria registro de PDF no banco.\n";
    echo "\nCron example:\n";
    echo "  */30 * * * * cd /srv/web/www/mugiwaras.www && /usr/bin/php bin/cbz_to_pdf.php --magick=/usr/bin/convert >> storage/logs/cbz_to_pdf.log 2>&1\n";
    exit(0);
}

$seriesFilter = isset($options['series']) ? (int)$options['series'] : 0;
$contentFilter = isset($options['content']) ? (int)$options['content'] : 0;
$limit = isset($options['limit']) ? max(0, (int)$options['limit']) : 0;
$dryRun = array_key_exists('dry-run', $options);
$force = array_key_exists('force', $options);
$magickOverride = (string)($options['magick'] ?? '');
$maxWidth = isset($options['max-width']) ? (int)$options['max-width'] : 1600;
$quality = isset($options['quality']) ? (int)$options['quality'] : 85;
$limitMemory = (string)($options['limit-memory'] ?? '256MiB');
$limitMap = (string)($options['limit-map'] ?? '512MiB');
$limitDisk = (string)($options['limit-disk'] ?? '2GiB');
$convertOptions = [
    'max_width' => $maxWidth,
    'quality' => $quality,
    'limit_memory' => $limitMemory,
    'limit_map' => $limitMap,
    'limit_disk' => $limitDisk,
];

$sql = "SELECT * FROM content_items WHERE cbz_path IS NOT NULL AND LOWER(cbz_path) NOT LIKE '%.pdf' AND LOWER(cbz_path) NOT LIKE '%.epub'";
$params = [];
if ($seriesFilter > 0) {
    $sql .= ' AND series_id = :s';
    $params['s'] = $seriesFilter;
}
if ($contentFilter > 0) {
    $sql .= ' AND id = :id';
    $params['id'] = $contentFilter;
}
$sql .= ' ORDER BY id ASC';
if ($limit > 0 && $contentFilter === 0) {
    $sql .= ' LIMIT :l';
}

$stmt = Database::connection()->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val, \PDO::PARAM_INT);
}
if ($limit > 0 && $contentFilter === 0) {
    $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
}
$stmt->execute();
$items = $stmt->fetchAll();

if (!$items) {
    echo "No CBZ items found.\n";
    exit(0);
}

echo 'Found ' . count($items) . " item(s).\n";

foreach ($items as $item) {
    $id = (int)($item['id'] ?? 0);
    $cbzPath = (string)($item['cbz_path'] ?? '');
    $abs = resolveCbzPath($cbzPath);
    if ($abs === null || !file_exists($abs)) {
        echo "#{$id} skip: file not found ({$cbzPath})\n";
        continue;
    }

    $root = resolveRootForPath($abs);
    if ($root === null) {
        echo "#{$id} skip: root not resolved ({$cbzPath})\n";
        continue;
    }

    $seriesName = 'Serie';
    if (!empty($item['series_id'])) {
        $series = Series::findById((int)$item['series_id']);
        if (!empty($series['name'])) {
            $seriesName = (string)$series['name'];
        }
    }
    $chapterName = (string)($item['title'] ?? 'Capitulo');
    if (trim($chapterName) === '') {
        $chapterName = 'Capitulo';
    }
    $siteName = (string)config('app.name', 'Site');

    $fileName = sanitizeFilename($seriesName . ' - ' . $chapterName . ' [' . $siteName . '].pdf');
    $pdfAbs = rtrim(dirname($abs), '/') . '/' . $fileName;
    $pdfRelative = ltrim(str_replace($root . '/', '', $pdfAbs), '/');

    $pdfExists = file_exists($pdfAbs);

    if ($pdfExists && !$force) {
        echo "#{$id} skip: pdf already exists ({$pdfRelative})\n";
        continue;
    }

    if (!$pdfExists || $force) {
        echo "#{$id} converting to pdf...\n";
        if ($dryRun) {
            echo "  dry-run: {$pdfRelative}\n";
        } else {
            $result = convertCbzToPdf($abs, $pdfAbs, $magickOverride, $convertOptions);
            if (!$result['ok']) {
                $reason = $result['error'] !== '' ? $result['error'] : 'conversion error';
                echo "  failed: {$reason}\n";
                continue;
            }
        }
    } else {
        echo "#{$id} pdf exists, registering...\n";
    }

    if ($dryRun) {
        continue;
    }

    if (!file_exists($pdfAbs)) {
        echo "  failed: pdf not found after conversion\n";
        continue;
    }

    echo "  created: {$pdfRelative}\n";
}

function resolveCbzPath(string $cbzPath): ?string
{
    $clean = str_replace(['..', '\\'], ['', '/'], $cbzPath);
    $storageRoot = dirname(__DIR__) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
    $storageFull = rtrim($storageRoot, '/') . '/' . ltrim($clean, '/');
    $storageReal = realpath($storageFull);
    $storageRootReal = realpath($storageRoot);
    if ($storageReal && $storageRootReal && str_starts_with($storageReal, $storageRootReal)) {
        return $storageReal;
    }

    $libraryRoot = dirname(__DIR__) . '/' . trim((string)config('library.path', 'storage/library'), '/');
    $libraryFull = rtrim($libraryRoot, '/') . '/' . ltrim($clean, '/');
    $libraryReal = realpath($libraryFull);
    $libraryRootReal = realpath($libraryRoot);
    if ($libraryReal && $libraryRootReal && str_starts_with($libraryReal, $libraryRootReal)) {
        return $libraryReal;
    }
    return null;
}

function resolveRootForPath(string $absPath): ?string
{
    $storageRoot = realpath(dirname(__DIR__) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/'));
    if ($storageRoot && str_starts_with($absPath, $storageRoot)) {
        return rtrim(str_replace('\\', '/', $storageRoot), '/');
    }
    $libraryRoot = realpath(dirname(__DIR__) . '/' . trim((string)config('library.path', 'storage/library'), '/'));
    if ($libraryRoot && str_starts_with($absPath, $libraryRoot)) {
        return rtrim(str_replace('\\', '/', $libraryRoot), '/');
    }
    return null;
}

function sanitizeFilename(string $name): string
{
    $clean = preg_replace('/[\x00-\x1F\x7F"\\\\\/<>:\\|?*]+/', ' ', $name) ?? $name;
    $clean = preg_replace('/\s+/', ' ', $clean) ?? $clean;
    $clean = trim($clean);
    if ($clean === '' || $clean === '.pdf') {
        return 'arquivo.pdf';
    }
    if (!str_ends_with(strtolower($clean), '.pdf')) {
        $clean .= '.pdf';
    }
    return $clean;
}

function convertCbzToPdf(string $cbzAbs, string $pdfAbs, string $magickOverride, array $options): array
{
    if (!class_exists('ZipArchive')) {
        return ['ok' => false, 'error' => 'php-zip missing'];
    }
    $tmpDir = buildTempDir();
    if (!is_dir($tmpDir)) {
        return ['ok' => false, 'error' => 'tmp dir not created'];
    }
    $images = extractCbzImages($cbzAbs, $tmpDir);
    if (!$images) {
        cleanupDir($tmpDir);
        return ['ok' => false, 'error' => 'no images extracted'];
    }

    $ok = false;
    $error = '';
    $magickBin = resolveMagickBinary($magickOverride);
    if ($magickBin !== '') {
        [$ok, $error] = convertWithMagick($images, $pdfAbs, $magickBin, $options);
    } elseif (extension_loaded('imagick')) {
        [$ok, $error] = convertWithImagick($images, $pdfAbs, $options);
    } else {
        $error = 'ImageMagick not found and imagick extension not loaded';
    }

    cleanupDir($tmpDir);
    return ['ok' => $ok, 'error' => $ok ? '' : ($error !== '' ? $error : 'conversion failed')];
}

function extractCbzImages(string $cbzAbs, string $tmpDir): array
{
    if (!class_exists('ZipArchive')) {
        return [];
    }
    $zip = new ZipArchive();
    if ($zip->open($cbzAbs) !== true) {
        return [];
    }
    $images = [];
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (!$name) {
            continue;
        }
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tif', 'tiff', 'jfif'], true)) {
            $images[] = $name;
        }
    }
    $zip->close();
    sort($images, SORT_NATURAL | SORT_FLAG_CASE);

    $output = [];
    $zip = new ZipArchive();
    if ($zip->open($cbzAbs) !== true) {
        return [];
    }
    $index = 1;
    foreach ($images as $name) {
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $stream = $zip->getStream($name);
        if (!$stream) {
            continue;
        }
        $target = $tmpDir . '/' . str_pad((string)$index, 3, '0', STR_PAD_LEFT) . '.' . $ext;
        $out = fopen($target, 'wb');
        if (!$out) {
            fclose($stream);
            continue;
        }
        stream_copy_to_stream($stream, $out);
        fclose($stream);
        fclose($out);
        $output[] = $target;
        $index++;
    }
    $zip->close();

    return $output;
}

function convertWithImagick(array $images, string $pdfAbs, array $options): array
{
    try {
        $imagick = new Imagick();
        if (!empty($options['limit_memory'])) {
            $imagick->setResourceLimit(Imagick::RESOURCETYPE_MEMORY, parseSizeToBytes((string)$options['limit_memory']));
        }
        if (!empty($options['limit_map'])) {
            $imagick->setResourceLimit(Imagick::RESOURCETYPE_MAP, parseSizeToBytes((string)$options['limit_map']));
        }
        if (!empty($options['limit_disk'])) {
            $imagick->setResourceLimit(Imagick::RESOURCETYPE_DISK, parseSizeToBytes((string)$options['limit_disk']));
        }
        foreach ($images as $img) {
            $imagick->readImage($img);
            if (!empty($options['max_width']) && (int)$options['max_width'] > 0) {
                $imagick->resizeImage((int)$options['max_width'], 0, Imagick::FILTER_LANCZOS, 1, true);
            }
        }
        $imagick->setImageFormat('pdf');
        if (!empty($options['quality']) && (int)$options['quality'] > 0) {
            $imagick->setImageCompressionQuality((int)$options['quality']);
        } else {
            $imagick->setImageCompressionQuality(90);
        }
        $ok = $imagick->writeImages($pdfAbs, true);
        $imagick->clear();
        $imagick->destroy();
        return [(bool)$ok, $ok ? '' : 'imagick failed to write pdf'];
    } catch (Throwable $e) {
        return [false, 'imagick error: ' . $e->getMessage()];
    }
}

function convertWithMagick(array $images, string $pdfAbs, string $magickBin, array $options): array
{
    $args = [];
    foreach ($images as $img) {
        $args[] = escapeshellarg($img);
    }
    if (empty($args)) {
        return [false, 'no images to convert'];
    }
    $limits = '';
    if (!empty($options['limit_memory'])) {
        $limits .= ' -limit memory ' . escapeshellarg((string)$options['limit_memory']);
    }
    if (!empty($options['limit_map'])) {
        $limits .= ' -limit map ' . escapeshellarg((string)$options['limit_map']);
    }
    if (!empty($options['limit_disk'])) {
        $limits .= ' -limit disk ' . escapeshellarg((string)$options['limit_disk']);
    }
    $resize = '';
    if (!empty($options['max_width']) && (int)$options['max_width'] > 0) {
        $resize = ' -resize ' . escapeshellarg((int)$options['max_width'] . 'x');
    }
    $quality = !empty($options['quality']) && (int)$options['quality'] > 0 ? (int)$options['quality'] : 90;
    $cmd = '"' . $magickBin . '"' . $limits . ' -quality ' . $quality . $resize . ' ' . implode(' ', $args) . ' ' . escapeshellarg($pdfAbs);
    $output = [];
    $code = 0;
    exec($cmd . ' 2>&1', $output, $code);

    if ($code !== 0) {
        return [false, 'magick failed: ' . implode(' ', $output)];
    }
    if (!file_exists($pdfAbs)) {
        return [false, 'magick ok but pdf not created'];
    }
    return [true, ''];
}

function resolveMagickBinary(string $override): string
{
    if ($override !== '') {
        return $override;
    }

    $candidates = [];
    $env = (string)env('MAGICK_BIN', '');
    if ($env !== '') {
        $candidates[] = $env;
    }
    $env = (string)env('IMAGEMAGICK_BIN', '');
    if ($env !== '') {
        $candidates[] = $env;
    }
    $candidates[] = 'magick';
    $candidates[] = 'convert';
    $candidates[] = 'magick.exe';

    foreach ($candidates as $candidate) {
        if ($candidate === '') {
            continue;
        }
        if (is_executable($candidate) || $candidate === 'magick' || $candidate === 'magick.exe') {
            return $candidate;
        }
    }

    $windowsGlob = glob('C:\\Program Files\\ImageMagick-*\\magick.exe') ?: [];
    foreach ($windowsGlob as $path) {
        if (is_executable($path)) {
            return $path;
        }
    }

    $linuxConvert = '/usr/bin/convert';
    if (is_executable($linuxConvert)) {
        return $linuxConvert;
    }

    return '';
}

function buildTempDir(): string
{
    $root = dirname(__DIR__) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
    $dir = rtrim($root, '/') . '/tmp/cbzpdf_' . bin2hex(random_bytes(6));
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    return $dir;
}

function cleanupDir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
    }
    @rmdir($dir);
}

function parseSizeToBytes(string $value): int
{
    $clean = trim($value);
    if ($clean === '') {
        return 0;
    }
    if (ctype_digit($clean)) {
        return (int)$clean;
    }
    if (!preg_match('/^(\d+)(kib|kb|mib|mb|gib|gb)$/i', $clean, $matches)) {
        return 0;
    }
    $num = (int)$matches[1];
    $unit = strtolower($matches[2]);
    return match ($unit) {
        'kb' => $num * 1000,
        'kib' => $num * 1024,
        'mb' => $num * 1000 * 1000,
        'mib' => $num * 1024 * 1024,
        'gb' => $num * 1000 * 1000 * 1000,
        'gib' => $num * 1024 * 1024 * 1024,
        default => 0,
    };
}
