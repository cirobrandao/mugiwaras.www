<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Models\Upload;
use App\Models\ContentItem;

final class UploadsController extends Controller
{
    // Controle: arquivo revisado para envio via Git em 2026-02-04
    public function index(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        if ($page < 1) $page = 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $total = Upload::countAll();
        $uploads = Upload::paged($perPage, $offset);

        // attach username for display (keep simple - 20 lookups max per page)
        $users = [];
        foreach ($uploads as &$u) {
            $uid = (int)($u['user_id'] ?? 0);
            if ($uid > 0) {
                if (!isset($users[$uid])) {
                    $usr = \App\Models\User::findById($uid);
                    $users[$uid] = $usr ? ($usr['username'] ?? ('#' . $uid)) : ('#' . $uid);
                }
                $u['username_display'] = $users[$uid];
            } else {
                $u['username_display'] = '(system)';
            }
        }
        unset($u);

        echo $this->view('admin/uploads', [
            'uploads' => $uploads,
            'csrf' => Csrf::token(),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
        ]);
    }

    public function delete(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/uploads'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $upload = Upload::find($id);
        if (!$upload) {
            Response::redirect(base_path('/admin/uploads'));
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
        Response::redirect(base_path('/admin/uploads'));
    }
}
