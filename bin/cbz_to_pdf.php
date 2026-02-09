<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Core\Database;
use App\Models\ContentItem;
use App\Models\Series;

$options = getopt('', ['limit::', 'series::', 'content::', 'dry-run', 'force', 'magick::', 'help']);
if (isset($options['help'])) {
    echo "Usage: php bin/cbz_to_pdf.php [--series=ID] [--content=ID] [--limit=50] [--dry-run] [--force] [--magick=PATH]\n";
    exit(0);
}

$seriesFilter = isset($options['series']) ? (int)$options['series'] : 0;
$contentFilter = isset($options['content']) ? (int)$options['content'] : 0;
$limit = isset($options['limit']) ? max(0, (int)$options['limit']) : 0;
$dryRun = array_key_exists('dry-run', $options);
$force = array_key_exists('force', $options);
$magickOverride = (string)($options['magick'] ?? '');

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

    $existingPdfRow = findContentByPath($pdfRelative);
    $pdfExists = file_exists($pdfAbs);

    if ($pdfExists && $existingPdfRow && !$force) {
        echo "#{$id} skip: pdf already registered ({$pdfRelative})\n";
        continue;
    }

    if (!$pdfExists || $force) {
        echo "#{$id} converting to pdf...\n";
        if ($dryRun) {
            echo "  dry-run: {$pdfRelative}\n";
        } else {
            $result = convertCbzToPdf($abs, $pdfAbs, $magickOverride);
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

    $hash = hash_file('sha256', $pdfAbs);
    $size = (int)filesize($pdfAbs);
    $originalName = basename($pdfAbs);

    if ($existingPdfRow) {
        updateContentPath((int)$existingPdfRow['id'], $hash, $size, $pdfRelative, $originalName);
        echo "  updated: content #" . (int)$existingPdfRow['id'] . "\n";
        continue;
    }

    if (ContentItem::findByHash($hash)) {
        echo "  skip: duplicate hash detected\n";
        continue;
    }

    ContentItem::create([
        'l' => $item['library_id'] ?? null,
        'c' => (int)($item['category_id'] ?? 0),
        's' => (int)($item['series_id'] ?? 0),
        't' => (string)($item['title'] ?? $chapterName),
        'p' => $pdfRelative,
        'h' => $hash,
        'sz' => $size,
        'o' => $originalName,
        'co' => (int)($item['content_order'] ?? 0),
    ]);

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

function findContentByPath(string $path): ?array
{
    $stmt = Database::connection()->prepare('SELECT * FROM content_items WHERE cbz_path = :p LIMIT 1');
    $stmt->execute(['p' => $path]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function updateContentPath(int $id, string $hash, int $size, string $path, string $originalName): void
{
    $stmt = Database::connection()->prepare('UPDATE content_items SET file_hash = :h, file_size = :sz, cbz_path = :p, original_name = :o WHERE id = :id');
    $stmt->execute([
        'h' => $hash,
        'sz' => $size,
        'p' => $path,
        'o' => $originalName,
        'id' => $id,
    ]);
}

function convertCbzToPdf(string $cbzAbs, string $pdfAbs, string $magickOverride): array
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
    if (extension_loaded('imagick')) {
        [$ok, $error] = convertWithImagick($images, $pdfAbs);
    } else {
        $magickBin = resolveMagickBinary($magickOverride);
        if ($magickBin !== '') {
            [$ok, $error] = convertWithMagick($images, $pdfAbs, $magickBin);
        } else {
            $error = 'ImageMagick not found and imagick extension not loaded';
        }
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

function convertWithImagick(array $images, string $pdfAbs): array
{
    try {
        $imagick = new Imagick();
        foreach ($images as $img) {
            $imagick->readImage($img);
        }
        $imagick->setImageFormat('pdf');
        $imagick->setImageCompressionQuality(90);
        $ok = $imagick->writeImages($pdfAbs, true);
        $imagick->clear();
        $imagick->destroy();
        return ['ok' => (bool)$ok, 'error' => $ok ? '' : 'imagick failed to write pdf'];
    } catch (Throwable $e) {
        return ['ok' => false, 'error' => 'imagick error: ' . $e->getMessage()];
    }
}

function convertWithMagick(array $images, string $pdfAbs, string $magickBin): array
{
    $listFile = dirname($pdfAbs) . '/.cbz_pdf_list_' . bin2hex(random_bytes(4)) . '.txt';
    $lines = [];
    foreach ($images as $img) {
        $lines[] = str_replace('"', '\\"', $img);
    }
    file_put_contents($listFile, implode(PHP_EOL, $lines));

    $listArg = '@' . $listFile;
    $cmd = '"' . $magickBin . '" -quality 90 ' . escapeshellarg($listArg) . ' ' . escapeshellarg($pdfAbs);
    $output = [];
    $code = 0;
    exec($cmd . ' 2>&1', $output, $code);
    @unlink($listFile);

    if ($code !== 0) {
        return ['ok' => false, 'error' => 'magick failed: ' . implode(' ', $output)];
    }
    if (!file_exists($pdfAbs)) {
        return ['ok' => false, 'error' => 'magick ok but pdf not created'];
    }
    return ['ok' => true, 'error' => ''];
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
