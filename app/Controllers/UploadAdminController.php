<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Models\Category;

final class UploadAdminController extends Controller
{
    private const SESSION_KEY = '_upload_admin_auth';

    public function loginForm(): void
    {
        if ($this->isAuthenticated()) {
            Response::redirect(upload_url('/upload-admin'));
        }

        echo $this->view('upload_admin/login', [
            'csrf' => Csrf::token(),
        ]);
    }

    public function login(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            echo $this->view('upload_admin/login', [
                'csrf' => Csrf::token(),
                'error' => 'Sessão expirada. Recarregue a página e tente novamente.',
            ]);
            return;
        }

        $username = trim((string)($request->post['username'] ?? ''));
        $password = (string)($request->post['password'] ?? '');

        $expectedUsername = (string)(env('UPLOAD_ADMIN_USER', 'uploadadmin') ?? 'uploadadmin');
        $expectedPassword = (string)(env('UPLOAD_ADMIN_PASS', 'change_me') ?? 'change_me');

        if (!hash_equals($expectedUsername, $username) || !hash_equals($expectedPassword, $password)) {
            echo $this->view('upload_admin/login', [
                'csrf' => Csrf::token(),
                'error' => 'Credenciais inválidas.',
            ]);
            return;
        }

        $_SESSION[self::SESSION_KEY] = 1;
        $_SESSION[self::SESSION_KEY . '_at'] = time();

        Response::redirect(upload_url('/upload-admin'));
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY], $_SESSION[self::SESSION_KEY . '_at']);
        Response::redirect(upload_url('/upload-admin/login'));
    }

    public function form(): void
    {
        if (!$this->isAuthenticated()) {
            Response::redirect(upload_url('/upload-admin/login'));
        }

        if (!Category::isReady()) {
            echo $this->view('upload_admin/upload', [
                'csrf' => Csrf::token(),
                'categories' => [],
                'setupError' => true,
            ]);
            return;
        }

        $categories = Category::all();

        echo $this->view('upload_admin/upload', [
            'csrf' => Csrf::token(),
            'categories' => $categories,
            'noCategories' => empty($categories),
        ]);
    }

    private function isAuthenticated(): bool
    {
        return !empty($_SESSION[self::SESSION_KEY]);
    }
}
