<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Core\Auth;
use App\Models\Category;
use App\Models\User;

final class UploadAdminController extends Controller
{
    private const SESSION_KEY = '_upload_admin_auth';

    public function loginForm(): void
    {
        if ($this->isAuthenticated()) {
            Response::redirect(base_path('/upload'));
        }

        echo $this->view('upload_admin/login', [
            'csrf' => Csrf::token(),
            'hideHeader' => true,
            'title' => 'Upload Admin Login',
        ]);
    }

    public function login(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            echo $this->view('upload_admin/login', [
                'csrf' => Csrf::token(),
                'error' => 'Sessão expirada. Recarregue a página e tente novamente.',
                'hideHeader' => true,
                'title' => 'Upload Admin Login',
            ]);
            return;
        }

        $username = trim((string)($request->post['username'] ?? ''));
        $password = (string)($request->post['password'] ?? '');

        $fixedUsername = trim((string)(env('UPLOAD_ADMIN_USER', '') ?? ''));
        $fixedPassword = (string)(env('UPLOAD_ADMIN_PASS', '') ?? '');

        if ($fixedUsername !== '' || $fixedPassword !== '') {
            if (!hash_equals($fixedUsername, $username) || !hash_equals($fixedPassword, $password)) {
                echo $this->view('upload_admin/login', [
                    'csrf' => Csrf::token(),
                    'error' => 'Credenciais inválidas.',
                    'hideHeader' => true,
                    'title' => 'Upload Admin Login',
                ]);
                return;
            }

            $actingUser = $this->resolveActingUser();
            if (!$actingUser) {
                echo $this->view('upload_admin/login', [
                    'csrf' => Csrf::token(),
                    'error' => 'Configure UPLOAD_ADMIN_ACT_AS_USER_ID ou UPLOAD_ADMIN_ACT_AS_USERNAME com um usuário que tenha permissão de upload.',
                    'hideHeader' => true,
                    'title' => 'Upload Admin Login',
                ]);
                return;
            }

            $_SESSION['user_id'] = (int)$actingUser['id'];
        } else {
            if (!Auth::attempt($username, $password, false, $request)) {
                echo $this->view('upload_admin/login', [
                    'csrf' => Csrf::token(),
                    'error' => 'Credenciais inválidas.',
                    'hideHeader' => true,
                    'title' => 'Upload Admin Login',
                ]);
                return;
            }

            $currentUser = Auth::user();
            if (!Auth::canUpload($currentUser)) {
                unset($_SESSION['user_id']);
                echo $this->view('upload_admin/login', [
                    'csrf' => Csrf::token(),
                    'error' => 'Usuário sem permissão de upload.',
                    'hideHeader' => true,
                    'title' => 'Upload Admin Login',
                ]);
                return;
            }
        }

        $_SESSION[self::SESSION_KEY] = 1;
        $_SESSION[self::SESSION_KEY . '_at'] = time();

        Response::redirect(base_path('/upload'));
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY], $_SESSION[self::SESSION_KEY . '_at'], $_SESSION['user_id']);
        Response::redirect(base_path('/login'));
    }

    public function form(): void
    {
        if (!$this->isAuthenticated()) {
            Response::redirect(base_path('/login'));
        }

        if (!Category::isReady()) {
            echo $this->view('upload_admin/upload', [
                'csrf' => Csrf::token(),
                'categories' => [],
                'setupError' => true,
                'hideHeader' => true,
                'title' => 'Upload Admin',
            ]);
            return;
        }

        $categories = Category::all();

        echo $this->view('upload_admin/upload', [
            'csrf' => Csrf::token(),
            'categories' => $categories,
            'noCategories' => empty($categories),
            'hideHeader' => true,
            'title' => 'Upload Admin',
        ]);
    }

    public function submit(Request $request): void
    {
        if (!$this->isAuthenticated()) {
            Response::redirect(base_path('/login'));
        }

        $uploader = new UploadController();
        $uploader->submit($request);
    }

    private function isAuthenticated(): bool
    {
        if (empty($_SESSION[self::SESSION_KEY]) || empty($_SESSION['user_id'])) {
            return false;
        }
        $user = Auth::user();
        return Auth::canUpload($user);
    }

    private function resolveActingUser(): ?array
    {
        $id = (int)(env('UPLOAD_ADMIN_ACT_AS_USER_ID', '0') ?? '0');
        if ($id > 0) {
            $user = User::findById($id);
            if ($user && Auth::canUpload($user)) {
                return $user;
            }
        }

        $username = mb_strtolower(trim((string)(env('UPLOAD_ADMIN_ACT_AS_USERNAME', '') ?? '')));
        if ($username !== '') {
            $user = User::findByUsername($username);
            if ($user && Auth::canUpload($user)) {
                return $user;
            }
        }

        $candidates = array_merge(
            User::uploaders(1),
            User::moderators(1),
            User::byRole('admin', 1),
            User::byRole('superadmin', 1)
        );

        foreach ($candidates as $candidate) {
            if (Auth::canUpload($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
