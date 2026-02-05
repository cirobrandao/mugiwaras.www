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
            Response::redirect(base_path('/admin/news?error=required'));
        }
        if ($categoryId <= 0 || !NewsCategory::find($categoryId)) {
            Response::redirect(base_path('/admin/news?error=category'));
        }
        News::create($title, $body, $categoryId, $published, $publishedAt);
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
            Response::redirect(base_path('/admin/news?error=required'));
        }
        if ($categoryId <= 0 || !NewsCategory::find($categoryId)) {
            Response::redirect(base_path('/admin/news?error=category'));
        }
        News::update($id, $title, $body, $categoryId, $published, $publishedAt);
        Response::redirect(base_path('/admin/news?updated=1'));
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
            News::delete($id);
        }
        Response::redirect(base_path('/admin/news?deleted=1'));
    }
}
