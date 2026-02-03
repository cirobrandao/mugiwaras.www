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
    public function index(): void
    {
        $uploads = Upload::all();
        echo $this->view('admin/uploads', [
            'uploads' => $uploads,
            'csrf' => Csrf::token(),
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
