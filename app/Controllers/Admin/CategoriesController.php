<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Models\Category;

final class CategoriesController extends Controller
{
    public function index(): void
    {
        if (!Category::isReady()) {
            echo $this->view('admin/categories', [
                'items' => [],
                'csrf' => Csrf::token(),
                'setupError' => true,
            ]);
            return;
        }
        $items = Category::all();
        $this->writeTagCss();
        echo $this->view('admin/categories', [
            'items' => $items,
            'csrf' => Csrf::token(),
        ]);
    }

    public function create(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/categories'));
        }
        $name = trim((string)($request->post['name'] ?? ''));
        $tagColor = trim((string)($request->post['tag_color'] ?? ''));
        if ($tagColor === '') {
            $tagColor = null;
        }
        if ($name === '') {
            Response::redirect(base_path('/admin/categories?error=required'));
        }
        $bannerPath = $this->handleBannerUpload($request);
        if ($bannerPath === false) {
            Response::redirect(base_path('/admin/categories?error=banner'));
        }
        if (!Category::findByName($name)) {
            Category::create($name, $bannerPath ?: null, $tagColor);
        }
        $this->writeTagCss();
        Response::redirect(base_path('/admin/categories?created=1'));
    }

    public function update(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/categories'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $name = trim((string)($request->post['name'] ?? ''));
        $tagColor = trim((string)($request->post['tag_color'] ?? ''));
        if ($tagColor === '') {
            $tagColor = null;
        }
        if ($id <= 0 || $name === '') {
            Response::redirect(base_path('/admin/categories?error=required'));
        }
        Category::rename($id, $name);
        Category::updateTagColor($id, $tagColor);
        $bannerPath = $this->handleBannerUpload($request, $id);
        if ($bannerPath === false) {
            Response::redirect(base_path('/admin/categories?error=banner'));
        }
        if (is_string($bannerPath)) {
            Category::updateBanner($id, $bannerPath);
        }
        $this->writeTagCss();
        Response::redirect(base_path('/admin/categories?updated=1'));
    }

    public function delete(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/categories'));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id > 0) {
            try {
                $this->deleteCascade($id);
                \App\Core\Audit::log('category_delete', $_SESSION['user_id'] ?? null, ['category_id' => $id]);
                $this->writeTagCss();
            } catch (\RuntimeException $e) {
                Response::redirect(base_path('/admin/categories?error=inuse'));
            }
        }
        Response::redirect(base_path('/admin/categories?deleted=1'));
    }


    private function deleteCascade(int $categoryId): void
    {
        $content = \App\Models\ContentItem::byCategory($categoryId);
        $libraryRoot = dirname(__DIR__, 3) . '/' . trim((string)config('library.path', 'storage/library'), '/');
        foreach ($content as $item) {
            $path = (string)($item['cbz_path'] ?? '');
            $clean = str_replace(['..', '\\'], ['', '/'], $path);
            $full = rtrim($libraryRoot, '/') . '/' . ltrim($clean, '/');
            if (file_exists($full)) {
                @unlink($full);
            }
            \App\Models\ContentItem::delete((int)$item['id']);
        }
        $db = \App\Core\Database::connection();
        $db->prepare('DELETE FROM series WHERE category_id = :c')->execute(['c' => $categoryId]);
        $db->prepare('DELETE FROM categories WHERE id = :c')->execute(['c' => $categoryId]);
    }

    private function handleBannerUpload(Request $request, int $categoryId = 0)
    {
        $file = $request->files['banner'] ?? null;
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return false;
        }
        $ext = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return false;
        }
        $info = @getimagesize((string)$file['tmp_name']);
        if (!$info || empty($info[0]) || empty($info[1])) {
            return false;
        }

        $dir = $this->publicUploadsRoot() . '/categories';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $filename = bin2hex(random_bytes(8)) . '.' . $ext;
        $target = $dir . '/' . $filename;
        if (!move_uploaded_file((string)$file['tmp_name'], $target)) {
            return false;
        }

        if ($categoryId > 0) {
            $existing = Category::findById($categoryId);
            $old = $existing['banner_path'] ?? null;
            if ($old) {
                $oldPath = $this->publicUploadsRoot() . '/' . ltrim((string)$old, '/');
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        return 'uploads/categories/' . $filename;
    }


    private function publicUploadsRoot(): string
    {
        return dirname(__DIR__, 3) . '/public/uploads';
    }

    private function writeTagCss(): void
    {
        $target = dirname(__DIR__, 3) . '/public/assets/category-tags.css';
        $lines = ["/* category tag colors - generated */"];
        foreach (Category::all() as $cat) {
            $id = (int)($cat['id'] ?? 0);
            $color = (string)($cat['tag_color'] ?? '');
            if ($id <= 0 || $color === '') {
                continue;
            }
            $safe = preg_replace('/[^a-fA-F0-9]/', '', $color);
            if ($safe === '' || !in_array(strlen($safe), [3, 6], true)) {
                continue;
            }
            $lines[] = ".cat-badge-{$id} { background-color: #{$safe} !important; color: #fff; }";
        }
        @file_put_contents($target, implode("\n", $lines) . "\n");
    }
}