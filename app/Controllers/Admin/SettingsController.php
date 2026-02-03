<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Models\Setting;

final class SettingsController extends Controller
{
    public function index(): void
    {
        echo $this->view('admin/settings', [
            'systemName' => Setting::get('system_name', 'Mugiwaras'),
            'systemLogo' => Setting::get('system_logo', ''),
            'termsOfUse' => Setting::get('terms_of_use', 'Ao criar uma conta, você declara ter lido e aceito os termos de uso do serviço, incluindo regras de acesso, limites e políticas de privacidade.'),
            'csrf' => Csrf::token(),
        ]);
    }

    public function save(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/settings'));
        }
        $name = trim((string)($request->post['system_name'] ?? ''));
        if ($name !== '') {
            Setting::set('system_name', $name);
        }

        $logoPath = $this->handleLogoUpload($request);
        if ($logoPath === false) {
            Response::redirect(base_path('/admin/settings?error=logo'));
        }
        if (is_string($logoPath)) {
            Setting::set('system_logo', $logoPath);
        }

        $faviconPath = $this->handleFaviconUpload($request);
        if ($faviconPath === false) {
            Response::redirect(base_path('/admin/settings?error=favicon'));
        }
        if (is_string($faviconPath)) {
            Setting::set('system_favicon', $faviconPath);
        }

        $terms = trim((string)($request->post['terms_of_use'] ?? ''));
        if ($terms !== '') {
            Setting::set('terms_of_use', $terms);
        }
        Response::redirect(base_path('/admin/settings'));
    }

    private function handleLogoUpload(Request $request)
    {
        $file = $request->files['logo'] ?? null;
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return false;
        }
        $ext = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg'], true)) {
            return false;
        }
        $dir = $this->publicUploadsRoot() . '/branding';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $filename = 'logo_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $target = $dir . '/' . $filename;
        if (!move_uploaded_file((string)$file['tmp_name'], $target)) {
            return false;
        }

        $old = Setting::get('system_logo', '');
        if ($old) {
            $oldPath = $this->publicUploadsRoot() . '/' . ltrim($old, '/');
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        return 'uploads/branding/' . $filename;
    }

    private function handleFaviconUpload(Request $request)
    {
        $file = $request->files['favicon'] ?? null;
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return false;
        }
        $ext = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($ext, ['ico', 'png', 'svg', 'jpg', 'jpeg', 'webp'], true)) {
            return false;
        }
        $dir = $this->publicUploadsRoot() . '/branding';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $filename = 'favicon_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $target = $dir . '/' . $filename;
        if (!move_uploaded_file((string)$file['tmp_name'], $target)) {
            return false;
        }

        $old = Setting::get('system_favicon', '');
        if ($old) {
            $oldPath = $this->publicUploadsRoot() . '/' . ltrim($old, '/');
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        return 'uploads/branding/' . $filename;
    }

    private function publicUploadsRoot(): string
    {
        return dirname(__DIR__, 3) . '/public/uploads';
    }
}
