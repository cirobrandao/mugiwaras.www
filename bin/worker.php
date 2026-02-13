<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Models\Job;
use App\Models\Upload;
use App\Core\JobProcessor;
use App\Core\Audit;
use App\Models\ContentItem;
use App\Core\Database;

$lockDir = dirname(__DIR__) . '/storage/locks';
if (!is_dir($lockDir)) {
    @mkdir($lockDir, 0777, true);
}
$lockPath = $lockDir . '/worker.lock';
$lockHandle = @fopen($lockPath, 'c');
if (!$lockHandle || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
    echo "Worker already running.\n";
    exit(0);
}
register_shutdown_function(static function () use ($lockHandle): void {
    if (is_resource($lockHandle)) {
        @flock($lockHandle, LOCK_UN);
        @fclose($lockHandle);
    }
});

$db = Database::connection();
$recoveredRunning = recoverStuckRunningJobs($db, 4, 50);
$requeuedUploads = requeueOrphanUploads($db, 50);

$jobs = Job::pending(5);
$startedAt = date('Y-m-d H:i:s');
echo "Worker start: {$startedAt}\n";
if ($recoveredRunning > 0) {
    echo "Recovered running jobs: {$recoveredRunning}\n";
}
if ($requeuedUploads > 0) {
    echo "Requeued orphan uploads: {$requeuedUploads}\n";
}
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
        // Source cleanup disabled to avoid removing originals.
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

function recoverStuckRunningJobs(\PDO $db, int $hours = 4, int $limit = 50): int
{
    $hours = max(1, $hours);
    $limit = max(1, min(500, $limit));

    $sql = "SELECT id
            FROM jobs
            WHERE status = 'running'
              AND started_at IS NOT NULL
              AND started_at <= DATE_SUB(NOW(), INTERVAL :h HOUR)
            ORDER BY started_at ASC
            LIMIT :l";
    $stmt = $db->prepare($sql);
    $stmt->bindValue('h', $hours, \PDO::PARAM_INT);
    $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    if (empty($rows)) {
        return 0;
    }

    $count = 0;
    foreach ($rows as $row) {
        $jobId = (int)($row['id'] ?? 0);
        if ($jobId <= 0) {
            continue;
        }
        $up = $db->prepare("UPDATE jobs SET status = 'pending', started_at = NULL WHERE id = :id AND status = 'running'");
        $up->execute(['id' => $jobId]);
        if ($up->rowCount() > 0) {
            Upload::setStatusByJob($jobId, 'queued');
            Audit::log('worker_recover_running_job', null, ['job_id' => $jobId]);
            $count++;
        }
    }

    return $count;
}

function requeueOrphanUploads(\PDO $db, int $limit = 50): int
{
    $limit = max(1, min(500, $limit));

    $sql = "SELECT u.*, j.status AS job_status
            FROM uploads u
            LEFT JOIN jobs j ON j.id = u.job_id
            WHERE u.status IN ('queued','pending','processing')
              AND (
                u.job_id IS NULL
                OR j.id IS NULL
                OR j.status IN ('failed','done')
              )
            ORDER BY u.created_at ASC
            LIMIT :l";
    $stmt = $db->prepare($sql);
    $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
    $stmt->execute();
    $uploads = $stmt->fetchAll();
    if (empty($uploads)) {
        return 0;
    }

    $storageRoot = dirname(__DIR__) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
    $libraryRoot = dirname(__DIR__) . '/' . trim((string)config('library.path', 'storage/library'), '/');
    $count = 0;

    foreach ($uploads as $upload) {
        $uploadId = (int)($upload['id'] ?? 0);
        if ($uploadId <= 0) {
            continue;
        }

        $sourceRel = ltrim((string)($upload['source_path'] ?? ''), '/');
        $targetRel = ltrim((string)($upload['target_path'] ?? ''), '/');
        if ($sourceRel === '' || $targetRel === '') {
            Upload::setStatus($uploadId, 'failed');
            Audit::log('worker_requeue_orphan_failed', null, ['upload_id' => $uploadId, 'reason' => 'missing_paths']);
            continue;
        }

        $sourcePath = $storageRoot . '/' . $sourceRel;
        $targetPath = $libraryRoot . '/' . $targetRel;
        if (!file_exists($sourcePath)) {
            if (file_exists($targetPath)) {
                Upload::setStatus($uploadId, 'done');
                Audit::log('worker_requeue_orphan_done', null, ['upload_id' => $uploadId, 'reason' => 'target_exists']);
            } else {
                Upload::setStatus($uploadId, 'failed');
                Audit::log('worker_requeue_orphan_failed', null, ['upload_id' => $uploadId, 'reason' => 'source_missing']);
            }
            continue;
        }

        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        if ($ext === '') {
            $ext = strtolower(pathinfo((string)($upload['original_name'] ?? ''), PATHINFO_EXTENSION));
        }

        $jobType = match ($ext) {
            'zip' => 'zip_to_cbz',
            'cbr' => 'cbr_to_cbz',
            'epub' => 'epub_to_cbz',
            default => '',
        };
        if ($jobType === '') {
            Upload::setStatus($uploadId, 'failed');
            Audit::log('worker_requeue_orphan_failed', null, ['upload_id' => $uploadId, 'reason' => 'unsupported_extension', 'ext' => $ext]);
            continue;
        }

        $jobId = Job::create($jobType, [
            'source' => $sourcePath,
            'target' => $targetPath,
            'user_id' => (int)($upload['user_id'] ?? 0),
            'upload_id' => $uploadId,
            'cleanup_source' => true,
            'category_id' => (int)($upload['category_id'] ?? 0),
            'series_id' => (int)($upload['series_id'] ?? 0),
            'title' => (string)($upload['title'] ?? pathinfo($sourcePath, PATHINFO_FILENAME)),
            'original_name' => (string)($upload['original_name'] ?? ''),
            'file_size' => (int)($upload['file_size'] ?? 0),
        ]);
        Upload::setJobId($uploadId, $jobId);
        Upload::setStatus($uploadId, 'queued');
        Audit::log('worker_requeue_orphan', null, ['upload_id' => $uploadId, 'job_id' => $jobId]);
        $count++;
    }

    return $count;
}
