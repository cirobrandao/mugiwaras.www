<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Models\News;

final class NewsController extends Controller
{
    public function index(): void
    {
        $items = News::all();
        echo $this->view('admin/news', [
            'items' => $items,
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
        $published = !empty($request->post['is_published']);
        $publishedAt = trim((string)($request->post['published_at'] ?? ''));
        $publishedAt = $publishedAt !== '' ? $publishedAt : null;
        if ($title === '' || $body === '') {
            Response::redirect(base_path('/admin/news?error=required'));
        }
        News::create($title, $body, $published, $publishedAt);
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
        $published = !empty($request->post['is_published']);
        $publishedAt = trim((string)($request->post['published_at'] ?? ''));
        $publishedAt = $publishedAt !== '' ? $publishedAt : null;
        if ($id <= 0 || $title === '' || $body === '') {
            Response::redirect(base_path('/admin/news?error=required'));
        }
        News::update($id, $title, $body, $published, $publishedAt);
        Response::redirect(base_path('/admin/news?updated=1'));
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
