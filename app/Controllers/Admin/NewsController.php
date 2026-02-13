<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Models\News;
use App\Models\NewsCategory;

final class NewsController extends Controller
{
    public function index(): void
    {
        $items = News::all();
        $categories = NewsCategory::all();
        echo $this->view('admin/news', [
            'items' => $items,
            'categories' => $categories,
            'csrf' => Csrf::token(),
        ]);
    }

    public function createForm(): void
    {
        $categories = NewsCategory::all();
        echo $this->view('admin/news_form', [
            'mode' => 'create',
            'news' => null,
            'categories' => $categories,
            'csrf' => Csrf::token(),
        ]);
    }

    public function editForm(Request $request, string $id): void
    {
        $newsId = (int)$id;
        if ($newsId <= 0) {
            Response::redirect(base_path('/admin/news?error=required'));
        }
        $news = News::find($newsId);
        if (!$news) {
            Response::redirect(base_path('/admin/news?error=required'));
        }
        $categories = NewsCategory::all();
        echo $this->view('admin/news_form', [
            'mode' => 'edit',
            'news' => $news,
            'categories' => $categories,
            'csrf' => Csrf::token(),
        ]);
    }

    public function create(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/news'));
        }
        $title = trim((string)($request->post['title'] ?? ''));
        $body = trim((string)($request->post['body'] ?? ''));
        $categoryId = (int)($request->post['category_id'] ?? 0);
        $published = !empty($request->post['is_published']);
        $publishNow = !empty($request->post['publish_now']);
        $publishedAt = trim((string)($request->post['published_at'] ?? ''));
        $publishedAt = $publishedAt !== '' ? $publishedAt : null;
        if ($publishNow) {
            $published = true;
            $publishedAt = date('Y-m-d H:i:s');
        }
        if ($title === '' || $body === '') {
            Response::redirect(base_path('/admin/news/create?error=required'));
        }
        if ($categoryId <= 0 || !NewsCategory::find($categoryId)) {
            Response::redirect(base_path('/admin/news/create?error=category'));
        }
        $featuredImagePath = $this->storeFeaturedImage($request->files['featured_image'] ?? null, null);
        if ($featuredImagePath === false) {
            Response::redirect(base_path('/admin/news/create?error=image'));
        }
        News::create($title, $body, $categoryId, $published, $publishedAt, $featuredImagePath ?: null);
        Response::redirect(base_path('/admin/news?created=1'));
    }

    public function update(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/news'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $title = trim((string)($request->post['title'] ?? ''));
        $body = trim((string)($request->post['body'] ?? ''));
        $categoryId = (int)($request->post['category_id'] ?? 0);
        $published = !empty($request->post['is_published']);
        $publishNow = !empty($request->post['publish_now']);
        $publishedAt = trim((string)($request->post['published_at'] ?? ''));
        $publishedAt = $publishedAt !== '' ? $publishedAt : null;
        if ($publishNow) {
            $published = true;
            $publishedAt = date('Y-m-d H:i:s');
        }
        if ($id <= 0 || $title === '' || $body === '') {
            Response::redirect(base_path('/admin/news/edit/' . $id . '?error=required'));
        }
        if ($categoryId <= 0 || !NewsCategory::find($categoryId)) {
            Response::redirect(base_path('/admin/news/edit/' . $id . '?error=category'));
        }
        $current = News::find($id);
        if (!$current) {
            Response::redirect(base_path('/admin/news?error=required'));
        }
        $currentImagePath = trim((string)($current['featured_image_path'] ?? ''));
        $removeFeaturedImage = !empty($request->post['remove_featured_image']);
        if ($removeFeaturedImage && $currentImagePath !== '') {
            $this->deleteFeaturedImageFile($currentImagePath);
            $currentImagePath = '';
        }

        $featuredImagePath = $this->storeFeaturedImage($request->files['featured_image'] ?? null, $currentImagePath);
        if ($featuredImagePath === false) {
            Response::redirect(base_path('/admin/news/edit/' . $id . '?error=image'));
        }
        News::update($id, $title, $body, $categoryId, $published, $publishedAt, $featuredImagePath ?: null);
        Response::redirect(base_path('/admin/news?updated=1'));
    }

    public function uploadBodyImage(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::json(['error' => 'csrf'], 422);
        }

        $storedPath = $this->storeBodyImage($request->files['body_image'] ?? null);
        if ($storedPath === false) {
            Response::json(['error' => 'image'], 422);
        }

        $relativeUrl = base_path('/' . ltrim($storedPath, '/'));
        $url = $this->toAbsoluteUrl('/' . ltrim($storedPath, '/'), $request);
        Response::json([
            'ok' => true,
            'path' => $storedPath,
            'relative_url' => $relativeUrl,
            'url' => $url,
            'markdown' => '![imagem](' . $relativeUrl . ')',
        ]);
    }

    public function images(): void
    {
        $images = array_merge(
            $this->collectNewsImages('uploads/news', 'Destaque'),
            $this->collectNewsImages('uploads/imagens', 'Corpo')
        );
        usort($images, static fn (array $a, array $b): int => ($b['modified_ts'] <=> $a['modified_ts']));

        echo $this->view('admin/news_images', [
            'images' => $images,
            'csrf' => Csrf::token(),
        ]);
    }

    public function deleteImage(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/images?error=csrf'));
        }

        $relative = trim((string)($request->post['path'] ?? ''));
        $targetPath = $this->resolveAllowedNewsImagePath($relative);
        if ($targetPath === null) {
            Response::redirect(base_path('/admin/images?error=invalid'));
        }

        if (!is_file($targetPath)) {
            Response::redirect(base_path('/admin/images?error=notfound'));
        }

        @unlink($targetPath);
        Response::redirect(base_path('/admin/images?deleted=1'));
    }

    private function storeFeaturedImage(?array $file, ?string $currentPath): string|false
    {
        $currentPath = trim((string)$currentPath);
        if (!$file || (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $currentPath;
        }
        if ((int)($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return false;
        }

        $tmpName = (string)($file['tmp_name'] ?? '');
        $size = (int)($file['size'] ?? 0);
        if ($tmpName === '' || $size <= 0 || $size > (4 * 1024 * 1024)) {
            return false;
        }

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string)$finfo->file($tmpName);
        if (!isset($allowedMimes[$mime])) {
            return false;
        }

        $publicRoot = dirname(__DIR__, 3) . '/public';
        $dir = $publicRoot . '/uploads/news';
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            return false;
        }

        $ext = $allowedMimes[$mime];
        $filename = 'news_' . date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
        $target = $dir . '/' . $filename;
        if (!move_uploaded_file($tmpName, $target)) {
            return false;
        }

        if ($currentPath !== '') {
            $old = $publicRoot . '/' . ltrim($currentPath, '/');
            if (is_file($old)) {
                @unlink($old);
            }
        }

        return 'uploads/news/' . $filename;
    }

    private function storeBodyImage(?array $file): string|false
    {
        if (!$file || (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return false;
        }
        if ((int)($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return false;
        }

        $tmpName = (string)($file['tmp_name'] ?? '');
        $size = (int)($file['size'] ?? 0);
        if ($tmpName === '' || $size <= 0 || $size > (4 * 1024 * 1024)) {
            return false;
        }

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string)$finfo->file($tmpName);
        if (!isset($allowedMimes[$mime])) {
            return false;
        }

        $publicRoot = dirname(__DIR__, 3) . '/public';
        $dir = $publicRoot . '/uploads/imagens';
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            return false;
        }

        $ext = $allowedMimes[$mime];
        $filename = 'news_body_' . date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
        $target = $dir . '/' . $filename;
        if (!move_uploaded_file($tmpName, $target)) {
            return false;
        }

        return 'uploads/imagens/' . $filename;
    }

    private function deleteFeaturedImageFile(string $path): void
    {
        $path = trim($path);
        if ($path === '') {
            return;
        }
        $publicRoot = dirname(__DIR__, 3) . '/public';
        $old = $publicRoot . '/' . ltrim($path, '/');
        if (is_file($old)) {
            @unlink($old);
        }
    }

    private function collectNewsImages(string $relativeDir, string $type): array
    {
        $publicRoot = dirname(__DIR__, 3) . '/public';
        $dir = $publicRoot . '/' . ltrim($relativeDir, '/');
        if (!is_dir($dir)) {
            return [];
        }

        $list = [];
        $entries = scandir($dir) ?: [];
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $full = $dir . '/' . $entry;
            if (!is_file($full)) {
                continue;
            }
            $ext = mb_strtolower(pathinfo($entry, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                continue;
            }

            $relativePath = trim(ltrim($relativeDir, '/') . '/' . $entry, '/');
            $mtime = @filemtime($full) ?: 0;
            $list[] = [
                'type' => $type,
                'name' => $entry,
                'relative_path' => $relativePath,
                'url' => url('/' . $relativePath),
                'size' => (int)(@filesize($full) ?: 0),
                'modified_ts' => $mtime,
                'modified_at' => $mtime > 0 ? date('Y-m-d H:i:s', $mtime) : '-',
            ];
        }

        return $list;
    }

    private function resolveAllowedNewsImagePath(string $relative): ?string
    {
        if ($relative === '') {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $relative), '/');
        $allowedPrefixes = ['uploads/news/', 'uploads/imagens/'];
        $allowed = false;
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($normalized, $prefix)) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            return null;
        }

        $publicRoot = realpath(dirname(__DIR__, 3) . '/public');
        if ($publicRoot === false) {
            return null;
        }
        $full = realpath($publicRoot . '/' . $normalized);
        if ($full === false) {
            return null;
        }

        $allowedDirs = [
            realpath($publicRoot . '/uploads/news'),
            realpath($publicRoot . '/uploads/imagens'),
        ];
        foreach ($allowedDirs as $allowedDir) {
            if ($allowedDir !== false && str_starts_with($full, $allowedDir . DIRECTORY_SEPARATOR)) {
                return $full;
            }
        }

        return null;
    }

    private function toAbsoluteUrl(string $path, ?Request $request = null): string
    {
        $normalizedPath = '/' . ltrim($path, '/');
        $appUrl = trim((string)config('app.url', ''));
        if ($appUrl !== '') {
            return rtrim($appUrl, '/') . base_path($normalizedPath);
        }

        if ($request !== null) {
            $forwardedProto = trim((string)($request->server['HTTP_X_FORWARDED_PROTO'] ?? ''));
            $https = (string)($request->server['HTTPS'] ?? '');
            $isSecure = ($forwardedProto !== '' && mb_strtolower($forwardedProto) === 'https')
                || ($https !== '' && mb_strtolower($https) !== 'off');
            $proto = $isSecure ? 'https' : 'http';
            $forwardedHost = trim((string)($request->server['HTTP_X_FORWARDED_HOST'] ?? ''));
            $host = $forwardedHost !== '' ? $forwardedHost : trim((string)($request->server['HTTP_HOST'] ?? ''));
            if ($host !== '') {
                return $proto . '://' . $host . base_path($normalizedPath);
            }
        }

        return base_path($normalizedPath);
    }

    public function createCategory(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/news'));
        }
        $name = trim((string)($request->post['name'] ?? ''));
        $showSidebar = !empty($request->post['show_sidebar']);
        $showBelowMostRead = !empty($request->post['show_below_most_read']);
        if ($name === '') {
            Response::redirect(base_path('/admin/news?error=category'));
        }
        NewsCategory::create($name, $showSidebar, $showBelowMostRead);
        Response::redirect(base_path('/admin/news?category_created=1'));
    }

    public function updateCategory(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/news'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $name = trim((string)($request->post['name'] ?? ''));
        $showSidebar = !empty($request->post['show_sidebar']);
        $showBelowMostRead = !empty($request->post['show_below_most_read']);
        if ($id <= 0 || $name === '') {
            Response::redirect(base_path('/admin/news?error=category'));
        }
        NewsCategory::update($id, $name, $showSidebar, $showBelowMostRead);
        Response::redirect(base_path('/admin/news?category_updated=1'));
    }

    public function deleteCategory(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/news'));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id > 0) {
            NewsCategory::delete($id);
        }
        Response::redirect(base_path('/admin/news?category_deleted=1'));
    }

    public function delete(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/news'));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id > 0) {
            $current = News::find($id);
            News::delete($id);
            if (!empty($current['featured_image_path'])) {
                $this->deleteFeaturedImageFile((string)$current['featured_image_path']);
            }
        }
        Response::redirect(base_path('/admin/news?deleted=1'));
    }
}
