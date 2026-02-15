<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Core\Audit;
use App\Core\Logger;
use App\Core\JobProcessor;
use App\Models\Job;
use App\Models\Upload;
use App\Core\Auth;
use App\Models\Category;
use App\Models\Series;
use App\Models\ContentItem;
use App\Core\Converters\SevenZipToCbzConverter;
use App\Core\Converters\CbrToCbzConverter;

final class UploadController extends Controller
{
    private ?string $lastCbzError = null;

    public function form(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        if (!Category::isReady()) {
            echo $this->view('upload/form', ['csrf' => Csrf::token(), 'categories' => [], 'setupError' => true]);
            return;
        }
        $categories = Category::all();
        $noCategories = empty($categories);
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $totalUploads = Upload::countByUser((int)$user['id']);
        $totalSize = Upload::totalSizeByUser((int)$user['id']);
        $pendingCount = Upload::countByUserStatus((int)$user['id'], 'pending');
        $pages = (int)ceil($totalUploads / $perPage);
        $offset = ($page - 1) * $perPage;
        $uploads = Upload::byUserPaged((int)$user['id'], $perPage, $offset);

        echo $this->view('upload/form', [
            'csrf' => Csrf::token(),
            'categories' => $categories,
            'noCategories' => $noCategories,
            'uploads' => $uploads,
            'page' => $page,
            'pages' => $pages,
            'totalUploads' => $totalUploads,
            'totalSize' => $totalSize,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function submit(Request $request): void
    {
        $isAjax = (($request->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest')
            || str_contains(strtolower((string)($request->server['HTTP_ACCEPT'] ?? '')), 'application/json');
        $user = Auth::user();
        if (!$user) {
            if ($isAjax) {
                Response::json(['error' => 'auth'], 401);
            }
            Response::redirect(base_path('/'));
        }
        $isBypassUpload = !empty($_SESSION['_upload_admin_auth']);
        $requiresApproval = !(Auth::isAdmin($user) || Auth::isModerator($user));
        $maxBytes = $isBypassUpload ? (5 * 1024 * 1024 * 1024) : (200 * 1024 * 1024);
        $maxFiles = 50;
        if (!Category::isReady()) {
            if ($isAjax) {
                Response::json(['error' => 'setup'], 500);
            }
            Response::redirect(base_path('/upload?error=setup'));
        }
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            if ($isAjax) {
                Response::json(['error' => 'csrf'], 422);
            }
            Response::redirect(base_path('/upload'));
        }
        if (empty($request->files['file'])) {
            $contentLength = (int)($request->server['CONTENT_LENGTH'] ?? 0);
            if ($contentLength > $maxBytes) {
                if ($isAjax) {
                    Response::json(['error' => 'limit', 'maxBytes' => $maxBytes], 413);
                }
                Response::redirect(base_path('/upload?error=limit'));
            }
            if ($isAjax) {
                Response::json(['error' => 'no_files'], 422);
            }
            Response::redirect(base_path('/upload?error=1'));
        }
        $files = $this->normalizeFiles($request->files['file']);
        if (empty($files)) {
            if ($isAjax) {
                Response::json(['error' => 'no_files'], 422);
            }
            Response::redirect(base_path('/upload?error=1'));
        }
        if (count($files) > $maxFiles) {
            if ($isAjax) {
                Response::json(['error' => 'max_files', 'maxFiles' => $maxFiles], 422);
            }
            Response::redirect(base_path('/upload?error=files'));
        }

        $rawCategory = (string)($request->post['category'] ?? '');
        $categoryName = trim($rawCategory);
        $seriesName = trim((string)($request->post['series'] ?? ''));
        if ($seriesName === '') {
            if ($isAjax) {
                Response::json(['error' => 'series_required'], 422);
            }
            Response::redirect(base_path('/upload?error=series'));
        }
        $cat = Category::findByName($categoryName);
        if (!$cat) {
            if ($isAjax) {
                Response::json(['error' => 'category_required'], 422);
            }
            Response::redirect(base_path('/upload?error=category'));
        }
        $ser = Series::findByName((int)$cat['id'], $seriesName);
        if (!$ser) {
            $serId = Series::create((int)$cat['id'], $seriesName);
            $ser = ['id' => $serId, 'name' => $seriesName];
        }
        $libraryRoot = dirname(__DIR__, 2) . '/' . trim((string)config('library.path', 'storage/library'), '/');
        if (!is_dir($libraryRoot)) {
            mkdir($libraryRoot, 0777, true);
        }

        $storageRoot = dirname(__DIR__, 2) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
        if (!is_dir($storageRoot)) {
            mkdir($storageRoot, 0777, true);
        }
        $incomingDir = $storageRoot . '/incoming';
        $convertedDir = $storageRoot . '/converted';
        if (!is_dir($incomingDir)) {
            mkdir($incomingDir, 0777, true);
        }
        if (!is_dir($convertedDir)) {
            mkdir($convertedDir, 0777, true);
        }

        $allowed = ['epub','zip','cbr','cbz','pdf'];
        $totalSize = 0;
        foreach ($files as $f) {
            $totalSize += (int)($f['size'] ?? 0);
        }
        if ($totalSize <= 0 || $totalSize > $maxBytes) {
            if ($isAjax) {
                Response::json(['error' => 'limit', 'maxBytes' => $maxBytes], 413);
            }
            Response::redirect(base_path('/upload?error=limit'));
        }
        $ok = 0;
        $queued = 0;
        $failed = 0;
        $errors = [];

        foreach ($files as $file) {
            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                $code = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
                $name = (string)($file['name'] ?? '');
                $errors[] = trim($name . ': error ' . $code);
                Logger::error('upload_file_error', ['name' => $name, 'error' => $code]);
                $failed++;
                continue;
            }
            $original = (string)($file['name'] ?? '');
            $size = (int)($file['size'] ?? 0);
            if ($size <= 0 || $size > $maxBytes) {
                $errors[] = trim($original . ': size');
                Logger::error('upload_size_error', ['name' => $original, 'size' => $size]);
                $failed++;
                continue;
            }
            $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true)) {
                $errors[] = trim($original . ': type');
                Logger::error('upload_type_error', ['name' => $original, 'ext' => $ext]);
                $failed++;
                continue;
            }

            $safeName = bin2hex(random_bytes(8)) . '.' . $ext;
            $targetPath = $incomingDir . '/' . $safeName;
            if (!move_uploaded_file((string)$file['tmp_name'], $targetPath)) {
                Logger::error('upload_move_failed', ['name' => $original]);
                $errors[] = trim($original . ': move');
                $failed++;
                continue;
            }

            $baseName = $this->sanitizeFileName(pathinfo($original, PATHINFO_FILENAME));
            if ($baseName === '') {
                $baseName = bin2hex(random_bytes(6));
            }
            $finalExt = in_array($ext, ['pdf', 'epub'], true) ? $ext : 'cbz';
            $finalRelPath = $baseName . '_' . bin2hex(random_bytes(4)) . '.' . $finalExt;
            $finalPath = rtrim($libraryRoot, '/') . '/' . $finalRelPath;

            if ($requiresApproval) {
                Upload::create([
                    'u' => $_SESSION['user_id'] ?? null,
                    'c' => (int)$cat['id'],
                    's' => (int)$ser['id'],
                    'o' => $original,
                    'ttl' => $baseName,
                    'sp' => 'incoming/' . $safeName,
                    'tp' => $finalRelPath,
                    'st' => 'pending',
                    'j' => null,
                    'fs' => $size,
                ]);
                Audit::log('upload_pending', $_SESSION['user_id'] ?? null, ['file' => $safeName]);
                $queued++;
                continue;
            }

            if ($ext === 'pdf' || $ext === 'epub') {
                if (!rename($targetPath, $finalPath)) {
                    Logger::error('upload_rename_failed', ['name' => $original, 'from' => $targetPath, 'to' => $finalPath]);
                    $errors[] = trim($original . ': move_final');
                    $failed++;
                    continue;
                }

                $hash = hash_file('sha256', $finalPath);
                if (ContentItem::findByHash($hash)) {
                    @unlink($finalPath);
                    $errors[] = trim($original . ': duplicate');
                    $failed++;
                    continue;
                }

                $size = filesize($finalPath) ?: $size;
                $contentOrder = $this->extractChapterOrder($baseName, $original);
                ContentItem::create([
                    'l' => null,
                    'c' => (int)$cat['id'],
                    's' => (int)$ser['id'],
                    't' => $baseName,
                    'p' => $finalRelPath,
                    'h' => $hash,
                    'sz' => $size,
                    'o' => $original,
                    'co' => $contentOrder,
                ]);
                Upload::create([
                    'u' => $_SESSION['user_id'] ?? null,
                    'c' => (int)$cat['id'],
                    's' => (int)$ser['id'],
                    'o' => $original,
                    'ttl' => $baseName,
                    'sp' => 'incoming/' . $safeName,
                    'tp' => $finalRelPath,
                    'st' => 'done',
                    'j' => null,
                    'fs' => $size,
                ]);
                Audit::log('upload', $_SESSION['user_id'] ?? null, ['file' => $safeName, 'type' => $ext]);
                $ok++;
                continue;
            }

            if ($ext === 'cbz') {
                $wasNormalized = false;
                if (!$this->isValidCbz($targetPath)) {
                    if (!$this->normalizeCbzWithSevenZip($targetPath, $finalPath, $original)) {
                        @unlink($targetPath);
                        $reason = $this->lastCbzError ? 'cbz_invalid (' . $this->lastCbzError . ')' : 'cbz_invalid';
                        $errors[] = trim($original . ': ' . $reason);
                        $failed++;
                        continue;
                    }
                    $wasNormalized = true;
                } else {
                    if (!rename($targetPath, $finalPath)) {
                        Logger::error('upload_rename_failed', ['name' => $original, 'from' => $targetPath, 'to' => $finalPath]);
                        $errors[] = trim($original . ': move_final');
                        $failed++;
                        continue;
                    }
                }

                $hash = hash_file('sha256', $finalPath);
                if (ContentItem::findByHash($hash)) {
                    @unlink($finalPath);
                    $errors[] = trim($original . ': duplicate');
                    $failed++;
                    continue;
                }
                $size = filesize($finalPath) ?: $size;
                $contentOrder = $this->extractChapterOrder($baseName, $original);
                ContentItem::create([
                    'l' => null,
                    'c' => (int)$cat['id'],
                    's' => (int)$ser['id'],
                    't' => $baseName,
                    'p' => $finalRelPath,
                    'h' => $hash,
                    'sz' => $size,
                    'o' => $original,
                    'co' => $contentOrder,
                ]);
                Upload::create([
                    'u' => $_SESSION['user_id'] ?? null,
                    'c' => (int)$cat['id'],
                    's' => (int)$ser['id'],
                    'o' => $original,
                    'ttl' => $baseName,
                    'sp' => 'incoming/' . $safeName,
                    'tp' => $finalRelPath,
                    'st' => 'done',
                    'j' => null,
                    'fs' => $size,
                ]);
                Audit::log('upload', $_SESSION['user_id'] ?? null, ['file' => $safeName, 'type' => 'cbz', 'normalized' => $wasNormalized]);
                $ok++;
                continue;
            }

            $jobType = match ($ext) {
                'zip' => 'zip_to_cbz',
                'cbr' => 'cbr_to_cbz',
                default => 'zip_to_cbz',
            };

            $uploadId = Upload::create([
                'u' => $_SESSION['user_id'] ?? null,
                'c' => (int)$cat['id'],
                's' => (int)$ser['id'],
                'o' => $original,
                'ttl' => $baseName,
                'sp' => 'incoming/' . $safeName,
                'tp' => $finalRelPath,
                'st' => 'queued',
                'j' => null,
                'fs' => $size,
            ]);

            $jobId = Job::create($jobType, [
                'source' => $targetPath,
                'target' => $finalPath,
                'user_id' => $_SESSION['user_id'] ?? null,
                'upload_id' => $uploadId,
                'cleanup_source' => true,
                'category_id' => (int)$cat['id'],
                'series_id' => (int)$ser['id'],
                'title' => $baseName,
                'original_name' => $original,
                'file_size' => $size,
            ]);
            \App\Core\Database::connection()->prepare('UPDATE uploads SET job_id = :j WHERE id = :id')->execute(['j' => $jobId, 'id' => $uploadId]);
            Audit::log('upload', $_SESSION['user_id'] ?? null, ['job_id' => $jobId, 'type' => $jobType]);
            $queued++;
        }

        if ($isAjax) {
            $status = ($ok + $queued) > 0 ? 200 : 422;
            Response::json(['ok' => $ok, 'queued' => $queued, 'failed' => $failed, 'errors' => $errors], $status);
        }
        Response::redirect(base_path('/upload?ok=' . $ok . '&queued=' . $queued . '&failed=' . $failed));
    }

    public function history(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $uploads = Upload::byUser((int)$user['id']);
        echo $this->view('upload/history', ['uploads' => $uploads]);
    }

    public function processPending(Request $request): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/upload?error=csrf'));
        }

        $limit = 5;
        $jobs = Job::pendingByUser((int)$user['id'], $limit);
        if (empty($jobs)) {
            Response::redirect(base_path('/upload?processed=0&failed_jobs=0'));
        }

        $processor = new JobProcessor();
        $processed = 0;
        $failed = 0;

        foreach ($jobs as $job) {
            try {
                Job::markRunning((int)$job['id']);
                $processor->process($job);

                $payload = json_decode((string)$job['payload'], true) ?: [];
                $target = (string)($payload['target'] ?? '');
                if ($target !== '' && file_exists($target)) {
                    if (!$this->isValidCbz($target)) {
                        @unlink($target);
                        throw new \RuntimeException('cbz_invalid');
                    }
                    $hash = hash_file('sha256', $target);
                    if (ContentItem::findByHash($hash)) {
                        @unlink($target);
                        throw new \RuntimeException('duplicate');
                    }
                    $libraryRoot = dirname(__DIR__, 2) . '/' . trim((string)config('library.path', 'storage/library'), '/');
                    $libraryRoot = rtrim(str_replace('\\', '/', $libraryRoot), '/');
                    $targetNorm = str_replace('\\', '/', $target);
                    $relative = str_starts_with($targetNorm, $libraryRoot . '/') ? substr($targetNorm, strlen($libraryRoot) + 1) : basename($targetNorm);
                    $title = (string)($payload['title'] ?? pathinfo($target, PATHINFO_FILENAME));
                    $originalName = (string)($payload['original_name'] ?? '');
                    $contentOrder = $this->extractChapterOrder($title, $originalName);
                    ContentItem::create([
                        'l' => null,
                        'c' => (int)($payload['category_id'] ?? 0),
                        's' => (int)($payload['series_id'] ?? 0),
                        't' => $title,
                        'p' => $relative,
                        'h' => $hash,
                        'sz' => (int)($payload['file_size'] ?? filesize($target)),
                        'o' => $originalName,
                        'co' => $contentOrder,
                    ]);
                }

                if (!empty($payload['cleanup_source']) && !empty($payload['source']) && file_exists((string)$payload['source'])) {
                    @unlink((string)$payload['source']);
                }
                Job::markDone((int)$job['id']);
                Upload::setStatusByJob((int)$job['id'], 'done');
                Audit::log('convert_job', null, ['job_id' => (int)$job['id']]);
                $processed++;
            } catch (\Throwable $e) {
                Job::markFailed((int)$job['id'], $e->getMessage());
                Upload::setStatusByJob((int)$job['id'], 'failed');
                $failed++;
            }
        }

        Response::redirect(base_path('/upload?processed=' . $processed . '&failed_jobs=' . $failed));
    }

    private function sanitizeSegment(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[^\pL\pN\s_-]+/u', '', $value) ?? '';
        $value = preg_replace('/\s+/u', ' ', $value) ?? '';
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        $value = str_replace(' ', '_', $value);
        return $value;
    }

    private function extractChapterOrder(string $title, string $originalName = ''): int
    {
        $candidates = array_filter([trim($title), trim($originalName)], static fn ($v) => $v !== '');
        foreach ($candidates as $candidate) {
            $name = pathinfo($candidate, PATHINFO_FILENAME);
            $name = str_replace(['_', '-', '.', '[', ']', '(', ')'], ' ', $name);
            $name = preg_replace('/\s+/u', ' ', $name) ?? '';
            $name = trim($name);
            if ($name === '') {
                continue;
            }
            $lower = mb_strtolower($name, 'UTF-8');
            if (preg_match('/\b(?:cap(?:i?tulo)?|cap[íi]tulo|ch(?:apter)?|ep(?:is[oó]dio)?|vol(?:ume)?)\s*#?\s*(\d{1,4})\b/u', $lower, $match)) {
                return (int)$match[1];
            }
            if (preg_match('/^\s*(\d{1,4})\b/u', $lower, $match)) {
                return (int)$match[1];
            }
            if (preg_match_all('/\b(\d{1,4})\b/u', $lower, $matches)) {
                foreach ($matches[1] as $raw) {
                    $num = (int)$raw;
                    if ($num >= 1900 && $num <= 2100) {
                        continue;
                    }
                    return $num;
                }
            }
        }
        return 0;
    }

    private function sanitizeFileName(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[^\pL\pN\s_-]+/u', '', $value) ?? '';
        $value = preg_replace('/\s+/u', ' ', $value) ?? '';
        $value = trim($value);
        $value = str_replace(' ', '_', $value);
        return $value;
    }

    private function normalizeFiles(array $file): array
    {
        if (!is_array($file['name'] ?? null)) {
            return [$file];
        }
        $result = [];
        $count = count($file['name']);
        for ($i = 0; $i < $count; $i++) {
            $result[] = [
                'name' => $file['name'][$i] ?? '',
                'type' => $file['type'][$i] ?? '',
                'tmp_name' => $file['tmp_name'][$i] ?? '',
                'error' => $file['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                'size' => $file['size'][$i] ?? 0,
            ];
        }
        return $result;
    }

    private function isValidCbz(string $path): bool
    {
        $this->lastCbzError = null;
        if (!class_exists('ZipArchive')) {
            $this->lastCbzError = 'extensão zip do PHP não instalada';
            return false;
        }
        $fh = @fopen($path, 'rb');
        if (!$fh) {
            $this->lastCbzError = 'falha ao abrir arquivo';
            return false;
        }
        $sig = fread($fh, 2);
        fclose($fh);
        if ($sig === false || bin2hex($sig) !== '504b') {
            $this->lastCbzError = 'assinatura inválida (não é ZIP)';
            return false;
        }
        $zip = new \ZipArchive();
        $open = $zip->open($path);
        if ($open !== true) {
            $this->lastCbzError = 'falha ao abrir ZIP';
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
        if (!$hasImage) {
            $this->lastCbzError = 'nenhuma imagem encontrada';
        }
        return $hasImage;
    }

    private function normalizeCbzWithSevenZip(string $sourcePath, string $targetPath, string $original): bool
    {
        if (!class_exists('\ZipArchive')) {
            Logger::error('cbz_normalize_missing_zip', ['name' => $original]);
            $this->lastCbzError = 'extensão zip do PHP não instalada';
            return false;
        }
        $converter = new CbrToCbzConverter();
        if ($converter->convert($sourcePath, $targetPath)) {
            return true;
        }
        Logger::error('cbz_normalize_failed', ['name' => $original, 'reason' => $converter->reason()]);
        $this->lastCbzError = $converter->reason();
        return false;
    }
}
