<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Models\AvatarGallery;

final class AvatarGalleryController extends Controller
{
    public function index(): void
    {
        echo $this->view('admin/avatar_gallery', [
            'avatars' => AvatarGallery::all(),
            'csrf' => Csrf::token(),
        ]);
    }

    public function upload(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/avatar-gallery'));
        }
        $file = $request->files['avatar'] ?? null;
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            Response::redirect(base_path('/admin/avatar-gallery?error=empty'));
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            Response::redirect(base_path('/admin/avatar-gallery?error=upload'));
        }
        $dir = $this->publicUploadsRoot() . '/avatars';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $error = null;
        if (!$this->validateAvatarUpload($file, $dir, $error)) {
            Response::redirect(base_path('/admin/avatar-gallery?error=' . ($error ?? 'type')));
        }
        $ext = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
        $filename = 'avatar_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $target = $dir . '/' . $filename;
        if (!move_uploaded_file((string)$file['tmp_name'], $target)) {
            Response::redirect(base_path('/admin/avatar-gallery?error=move'));
        }

        $title = trim((string)($request->post['title'] ?? ''));
        $sortOrder = (int)($request->post['sort_order'] ?? 0);
        $isActive = !empty($request->post['is_active']) ? 1 : 0;

        AvatarGallery::create([
            'title' => $title !== '' ? $title : null,
            'file_path' => 'uploads/avatars/' . $filename,
            'is_active' => $isActive,
            'sort_order' => $sortOrder,
        ]);

        Response::redirect(base_path('/admin/avatar-gallery?created=1'));
    }

    public function update(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/avatar-gallery'));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id <= 0) {
            Response::redirect(base_path('/admin/avatar-gallery'));
        }
        $title = trim((string)($request->post['title'] ?? ''));
        $sortOrder = (int)($request->post['sort_order'] ?? 0);
        $isActive = !empty($request->post['is_active']) ? 1 : 0;

        AvatarGallery::update($id, [
            'title' => $title !== '' ? $title : null,
            'is_active' => $isActive,
            'sort_order' => $sortOrder,
        ]);

        Response::redirect(base_path('/admin/avatar-gallery?updated=1'));
    }

    public function delete(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/avatar-gallery'));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id <= 0) {
            Response::redirect(base_path('/admin/avatar-gallery'));
        }
        $avatar = AvatarGallery::find($id);
        if ($avatar) {
            $path = $this->publicUploadsRoot() . '/' . ltrim((string)($avatar['file_path'] ?? ''), '/');
            if (is_file($path)) {
                @unlink($path);
            }
            AvatarGallery::delete($id);
        }
        Response::redirect(base_path('/admin/avatar-gallery?deleted=1'));
    }

    private function publicUploadsRoot(): string
    {
        return dirname(__DIR__, 3) . '/public/uploads';
    }

    private function validateAvatarUpload(array $file, string $dir, ?string &$error): bool
    {
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        $maxBytes = 2 * 1024 * 1024;

        $ext = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            $error = 'type';
            return false;
        }
        $size = (int)($file['size'] ?? 0);
        if ($size <= 0) {
            $error = 'upload';
            return false;
        }
        if ($size > $maxBytes) {
            $error = 'size';
            return false;
        }
        $free = @disk_free_space($dir);
        if ($free !== false && $size > $free) {
            $error = 'space';
            return false;
        }

        $info = @getimagesize((string)($file['tmp_name'] ?? ''));
        if ($info === false) {
            $error = 'type';
            return false;
        }
        $mime = (string)($info['mime'] ?? '');
        if (!in_array($mime, $allowedMime, true)) {
            $error = 'type';
            return false;
        }
        $width = (int)($info[0] ?? 0);
        $height = (int)($info[1] ?? 0);
        if ($width <= 0 || $height <= 0 || $width > 500 || $height > 500) {
            $error = 'dim';
            return false;
        }
        return true;
    }
}
