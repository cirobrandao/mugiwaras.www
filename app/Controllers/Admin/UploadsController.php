<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Models\Upload;
use App\Models\ContentItem;
use App\Models\Category;
use App\Models\Series;
use App\Models\Job;
use App\Core\Audit;

final class UploadsController extends Controller
{
    // Controle: arquivo revisado para envio via Git em 2026-02-04
    public function index(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        if ($page < 1) $page = 1;
        $perPage = (int)($_GET['perPage'] ?? 50);
        if ($perPage < 20) $perPage = 20;
        if ($perPage > 200) $perPage = 200;
        $offset = ($page - 1) * $perPage;

        $userFilterRaw = trim((string)($_GET['user'] ?? ''));
        $categoryFilter = (int)($_GET['category'] ?? 0);
        $seriesFilter = (int)($_GET['series'] ?? 0);
        $statusFilter = trim((string)($_GET['status'] ?? ''));
        $filters = [];
        if ($categoryFilter > 0) {
            $filters['category_id'] = $categoryFilter;
        }
        if ($seriesFilter > 0) {
            $filters['series_id'] = $seriesFilter;
        }
        if ($statusFilter !== '') {
            $filters['status'] = $statusFilter;
        }
        if ($userFilterRaw !== '') {
            if (ctype_digit($userFilterRaw)) {
                $filters['user_id'] = (int)$userFilterRaw;
            } else {
                $filters['username'] = $userFilterRaw;
            }
        }

        $total = Upload::countFiltered($filters);
        $totalSize = Upload::totalSizeFiltered($filters);
        $uploads = Upload::pagedWithRelationsFiltered($perPage, $offset, $filters);
        $categories = Category::all();
        $series = Series::all();
        $seriesByCategory = [];
        foreach ($series as $s) {
            $cid = (int)($s['category_id'] ?? 0);
            if (!isset($seriesByCategory[$cid])) {
                $seriesByCategory[$cid] = [];
            }
            $seriesByCategory[$cid][] = $s;
        }

        echo $this->view('admin/uploads', [
            'uploads' => $uploads,
            'csrf' => Csrf::token(),
            'total' => $total,
            'totalSize' => $totalSize,
            'page' => $page,
            'perPage' => $perPage,
            'categories' => $categories,
            'seriesByCategory' => $seriesByCategory,
            'filterUser' => $userFilterRaw,
            'filterCategory' => $categoryFilter,
            'filterSeries' => $seriesFilter,
            'filterStatus' => $statusFilter,
        ]);
    }

    public function update(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/uploads'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $upload = Upload::find($id);
        if (!$upload) {
            Response::redirect(base_path('/admin/uploads'));
        }

        $categoryId = (int)($request->post['category_id'] ?? 0);
        $seriesId = (int)($request->post['series_id'] ?? 0);
        $newSeriesName = trim((string)($request->post['series_new'] ?? ''));

        if ($newSeriesName !== '') {
            if ($categoryId <= 0) {
                $categoryId = (int)($upload['category_id'] ?? 0);
            }
            if ($categoryId <= 0) {
                Response::redirect(base_path('/admin/uploads'));
            }
            $existing = Series::findByName($categoryId, $newSeriesName);
            if ($existing) {
                $seriesId = (int)($existing['id'] ?? 0);
            } else {
                $seriesId = Series::create($categoryId, $newSeriesName);
            }
        }

        $series = null;
        if ($seriesId > 0) {
            $series = Series::findById($seriesId);
            if (!$series) {
                Response::redirect(base_path('/admin/uploads'));
            }
            $seriesCategory = (int)($series['category_id'] ?? 0);
            if ($seriesCategory > 0) {
                $categoryId = $seriesCategory;
            }
        }

        if ($categoryId <= 0) {
            $categoryId = (int)($upload['category_id'] ?? 0);
        }

        $categoryIdValue = $categoryId > 0 ? $categoryId : null;
        $seriesIdValue = $seriesId > 0 ? $seriesId : null;

        Upload::updateCategorySeries($id, $categoryIdValue, $seriesIdValue);

        $target = (string)($upload['target_path'] ?? '');
        if ($target !== '') {
            $item = ContentItem::findByPath($target);
            if ($item) {
                ContentItem::updateCategorySeries((int)$item['id'], $categoryIdValue, $seriesIdValue);
            }
        }

        $page = (int)($request->post['page'] ?? 1);
        $perPage = (int)($request->post['perPage'] ?? 50);
        $page = $page < 1 ? 1 : $page;
        $perPage = $perPage < 20 ? 20 : ($perPage > 200 ? 200 : $perPage);
        Response::redirect(base_path('/admin/uploads?page=' . $page . '&perPage=' . $perPage));
    }

    public function approve(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect($this->uploadsRedirect($request));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id <= 0) {
            Response::redirect($this->uploadsRedirect($request));
        }
        $this->approveUploadById($id);
        Response::redirect($this->uploadsRedirect($request));
    }

    public function approveMultiple(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect($this->uploadsRedirect($request));
        }
        $ids = $request->post['ids'] ?? [];
        if (!is_array($ids) || empty($ids)) {
            Response::redirect($this->uploadsRedirect($request));
        }
        foreach ($ids as $id) {
            $id = (int)$id;
            if ($id > 0) {
                $this->approveUploadById($id);
            }
        }
        Response::redirect($this->uploadsRedirect($request));
    }

    public function delete(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect($this->uploadsRedirect($request));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id <= 0) {
            Response::redirect($this->uploadsRedirect($request));
        }

        $upload = Upload::find($id);
        if (!$upload) {
            Response::redirect($this->uploadsRedirect($request));
        }

        if (($request->post['confirm'] ?? '') !== '1') {
            Response::redirect($this->uploadsRedirect($request));
        }

        $this->deleteUploadById($id);
        Response::redirect($this->uploadsRedirect($request));
    }

    public function deleteMultiple(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/uploads'));
        }
        $ids = $request->post['ids'] ?? [];
        if (!is_array($ids)) {
            Response::redirect(base_path('/admin/uploads'));
        }
        foreach ($ids as $id) {
            $id = (int)$id;
            if ($id > 0) {
                $this->deleteUploadById($id);
            }
        }
        Response::redirect(base_path('/admin/uploads'));
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

    private function uploadsRedirect(Request $request): string
    {
        $params = [
            'page' => (int)($request->post['page'] ?? $request->get['page'] ?? 1),
            'perPage' => (int)($request->post['perPage'] ?? $request->get['perPage'] ?? 50),
            'user' => (string)($request->post['user'] ?? $request->get['user'] ?? ''),
            'category' => (int)($request->post['category'] ?? $request->get['category'] ?? 0),
            'series' => (int)($request->post['series'] ?? $request->get['series'] ?? 0),
            'status' => (string)($request->post['status'] ?? $request->get['status'] ?? ''),
        ];
        return base_path('/admin/uploads?' . http_build_query($params));
    }

    private function approveUploadById(int $id): void
    {
        $upload = Upload::find($id);
        if (!$upload || ($upload['status'] ?? '') !== 'pending') {
            return;
        }

        $storageRoot = dirname(__DIR__, 3) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
        $libraryRoot = dirname(__DIR__, 3) . '/' . trim((string)config('library.path', 'storage/library'), '/');
        if (!is_dir($libraryRoot)) {
            mkdir($libraryRoot, 0777, true);
        }

        $sourceRel = ltrim((string)($upload['source_path'] ?? ''), '/');
        $targetRel = ltrim((string)($upload['target_path'] ?? ''), '/');
        $sourcePath = rtrim($storageRoot, '/') . '/' . $sourceRel;
        $targetPath = rtrim($libraryRoot, '/') . '/' . $targetRel;

        if ($sourceRel === '' || !file_exists($sourcePath)) {
            Upload::setStatus($id, 'failed');
            return;
        }

        $ext = strtolower(pathinfo((string)($upload['original_name'] ?? ''), PATHINFO_EXTENSION));
        $isPdf = $ext === 'pdf';
        $isCbz = $ext === 'cbz';

        if ($isPdf || $isCbz) {
            $targetDir = dirname($targetPath);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            if (!rename($sourcePath, $targetPath)) {
                Upload::setStatus($id, 'failed');
                return;
            }

            $hash = hash_file('sha256', $targetPath);
            if (ContentItem::findByHash($hash)) {
                @unlink($targetPath);
                Upload::setStatus($id, 'failed');
                return;
            }

            $size = filesize($targetPath) ?: (int)($upload['file_size'] ?? 0);
            $title = (string)($upload['title'] ?? pathinfo($targetPath, PATHINFO_FILENAME));
            $contentOrder = $this->extractChapterOrder($title, (string)($upload['original_name'] ?? ''));
            ContentItem::create([
                'l' => null,
                'c' => (int)($upload['category_id'] ?? 0),
                's' => (int)($upload['series_id'] ?? 0),
                't' => $title,
                'p' => $targetRel,
                'h' => $hash,
                'sz' => $size,
                'o' => (string)($upload['original_name'] ?? ''),
                'co' => $contentOrder,
            ]);
            Upload::setStatus($id, 'done');
            Audit::log('upload_approved', null, ['upload_id' => $id]);
            return;
        }

        $jobType = match ($ext) {
            'epub' => 'epub_to_cbz',
            'zip' => 'zip_to_cbz',
            'cbr' => 'cbr_to_cbz',
            default => '',
        };
        if ($jobType === '') {
            Upload::setStatus($id, 'failed');
            return;
        }

        $jobId = Job::create($jobType, [
            'source' => $sourcePath,
            'target' => $targetPath,
            'user_id' => $upload['user_id'] ?? null,
            'upload_id' => $id,
            'cleanup_source' => true,
            'category_id' => (int)($upload['category_id'] ?? 0),
            'series_id' => (int)($upload['series_id'] ?? 0),
            'title' => (string)($upload['title'] ?? ''),
            'original_name' => (string)($upload['original_name'] ?? ''),
            'file_size' => (int)($upload['file_size'] ?? 0),
        ]);
        Upload::setJobId($id, $jobId);
        Upload::setStatus($id, 'queued');
        Audit::log('upload_approved', null, ['upload_id' => $id, 'job_id' => $jobId]);
    }

    private function deleteUploadById(int $id): void
    {
        $upload = Upload::find($id);
        if (!$upload) {
            return;
        }

        $storageRoot = dirname(__DIR__, 3) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
        $source = (string)($upload['source_path'] ?? '');
        if ($source !== '') {
            $full = $storageRoot . '/' . ltrim($source, '/');
            if (file_exists($full)) {
                @unlink($full);
            }
        }

        $target = (string)($upload['target_path'] ?? '');
        if ($target !== '') {
            $libraryRoot = dirname(__DIR__, 3) . '/' . trim((string)config('library.path', 'storage/library'), '/');
            $libPath = $libraryRoot . '/' . ltrim($target, '/');
            if (file_exists($libPath)) {
                @unlink($libPath);
            }
            $item = ContentItem::findByPath($target);
            if ($item) {
                ContentItem::delete((int)$item['id']);
            }
        }

        Upload::delete($id);
    }
}
