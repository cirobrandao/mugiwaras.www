<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Models\Job;
use App\Models\Upload;
use App\Core\JobProcessor;
use App\Core\Audit;
use App\Models\ContentItem;

$jobs = Job::pending(5);
$startedAt = date('Y-m-d H:i:s');
echo "Worker start: {$startedAt}\n";
echo 'Jobs pending (batch): ' . count($jobs) . "\n";
$processor = new JobProcessor();

if (empty($jobs)) {
    echo "No pending jobs.\n";
}

foreach ($jobs as $job) {
    $jobId = (int)($job['id'] ?? 0);
    $jobType = (string)($job['job_type'] ?? '');
    echo "Processing job #{$jobId} ({$jobType})...\n";
    try {
        Job::markRunning($jobId);
        $processor->process($job);
        $payload = json_decode((string)$job['payload'], true) ?: [];
        $target = (string)($payload['target'] ?? '');
        $source = (string)($payload['source'] ?? '');
        if ($source !== '') {
            echo "  source: {$source}\n";
        }
        if ($target !== '') {
            echo "  target: {$target}\n";
        }
        if ($target !== '' && file_exists($target)) {
            if (!isValidCbz($target)) {
                @unlink($target);
                throw new RuntimeException('cbz_invalid');
            }
            $hash = hash_file('sha256', $target);
            if (ContentItem::findByHash($hash)) {
                @unlink($target);
                throw new RuntimeException('duplicate');
            }
            $libraryRoot = dirname(__DIR__, 1) . '/' . trim((string)config('library.path', 'storage/library'), '/');
            $libraryRoot = rtrim(str_replace('\\', '/', $libraryRoot), '/');
            $targetNorm = str_replace('\\', '/', $target);
            $relative = str_starts_with($targetNorm, $libraryRoot . '/') ? substr($targetNorm, strlen($libraryRoot) + 1) : basename($targetNorm);
            ContentItem::create([
                'l' => null,
                'c' => (int)($payload['category_id'] ?? 0),
                's' => (int)($payload['series_id'] ?? 0),
                't' => (string)($payload['title'] ?? pathinfo($target, PATHINFO_FILENAME)),
                'p' => $relative,
                'h' => $hash,
                'sz' => (int)($payload['file_size'] ?? filesize($target)),
                'o' => (string)($payload['original_name'] ?? ''),
            ]);
            echo "  converted: {$relative}\n";
        }
        $payload = json_decode((string)$job['payload'], true) ?: [];
        if (!empty($payload['cleanup_source']) && !empty($payload['source']) && file_exists((string)$payload['source'])) {
            @unlink((string)$payload['source']);
        }
        Job::markDone($jobId);
        Upload::setStatusByJob($jobId, 'done');
        Audit::log('convert_job', null, ['job_id' => $jobId]);
        echo "  status: done\n";
    } catch (Throwable $e) {
        Job::markFailed($jobId, $e->getMessage());
        Upload::setStatusByJob($jobId, 'failed');
        echo "  status: failed ({$e->getMessage()})\n";
    }
}

echo "Worker finished.\n";

function isValidCbz(string $path): bool
{
    if (!class_exists('ZipArchive')) {
        return false;
    }
    $fh = @fopen($path, 'rb');
    if (!$fh) {
        return false;
    }
    $sig = fread($fh, 2);
    fclose($fh);
    if ($sig === false || bin2hex($sig) !== '504b') {
        return false;
    }
    $zip = new ZipArchive();
    $open = $zip->open($path);
    if ($open !== true) {
        return false;
    }
    $hasImage = false;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (!$name) {
            continue;
        }
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp','bmp','tif','tiff','jfif'], true)) {
            $hasImage = true;
            break;
        }
    }
    $zip->close();
    return $hasImage;
}
