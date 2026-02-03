<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Core\Auth;
use App\Models\User;
use App\Models\PasswordReset;

final class UsersController extends Controller
{
    public function index(): void
    {
        $currentUser = Auth::user();
        $users = User::all();
        $resetToken = $_GET['reset'] ?? null;
        $resetUserId = $_GET['uid'] ?? null;
        echo $this->view('admin/users', [
            'users' => $users,
            'csrf' => Csrf::token(),
            'resetToken' => $resetToken,
            'resetUserId' => $resetUserId,
            'currentUser' => $currentUser,
        ]);
    }

    public function update(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $role = (string)($request->post['role'] ?? 'none');
        $tier = (string)($request->post['access_tier'] ?? 'user');

        $current = Auth::user();
        $target = User::findById($id);
        if (!$current || !$target) {
            Response::redirect(base_path('/admin/users'));
        }

        if ($target['role'] === 'superadmin') {
            Response::redirect(base_path('/admin/users'));
        }

        if ($current['role'] !== 'superadmin' && in_array($target['role'], ['admin', 'superadmin'], true)) {
            $role = $target['role'];
        }

        if ($role === 'superadmin') {
            $count = User::countSuperadmins();
            if ($count > 0 && $target['role'] !== 'superadmin') {
                Response::redirect(base_path('/admin/users'));
            }
        }

        if ($role !== 'none') {
            $tier = $target['access_tier'];
        }

        User::updateRoleTier($id, $role, $tier);
        Response::redirect(base_path('/admin/users'));
    }

    public function lock(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $target = User::findById($id);
        if (!$target || $target['role'] === 'superadmin') {
            Response::redirect(base_path('/admin/users'));
        }
        $until = (string)($request->post['lock_until'] ?? '2099-12-31 00:00:00');
        User::setLockUntil($id, $until);
        Response::redirect(base_path('/admin/users'));
    }

    public function unlock(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $target = User::findById($id);
        if (!$target || $target['role'] === 'superadmin') {
            Response::redirect(base_path('/admin/users'));
        }
        User::setLockUntil($id, null);
        Response::redirect(base_path('/admin/users'));
    }

    public function reset(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $target = User::findById($id);
        if (!$target || $target['role'] === 'superadmin') {
            Response::redirect(base_path('/admin/users'));
        }
        $token = bin2hex(random_bytes(24));
        PasswordReset::create($id, $token, 60);
        Response::redirect(base_path('/admin/users?reset=' . $token . '&uid=' . $id));
    }
}
