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
use App\Models\Category;
use App\Models\Package;
use App\Models\Payment;

final class UsersController extends Controller
{
    public function index(): void
    {
        $currentUser = Auth::user();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = (int)($_GET['perPage'] ?? 50);
        if ($perPage < 10) {
            $perPage = 10;
        }
        if ($perPage > 200) {
            $perPage = 200;
        }
        $total = User::countNonStaff();
        $pages = (int)max(1, ceil($total / $perPage));
        if ($page > $pages) {
            $page = $pages;
        }
        $users = User::pagedNonStaff($page, $perPage);
        $userIds = array_map(static fn ($u) => (int)($u['id'] ?? 0), $users);
        $latestPayments = Payment::latestApprovedByUsers($userIds);
        $packages = Package::all();
        $packageIds = array_map(static fn ($p) => (int)($p['id'] ?? 0), $packages);
        $packageCategories = Package::categoriesMap($packageIds);
        $packageMap = [];
        foreach ($packages as $pkg) {
            $pid = (int)($pkg['id'] ?? 0);
            if ($pid > 0) {
                $packageMap[$pid] = $pkg;
            }
        }
        $categories = Category::all();
        $resetToken = $_GET['reset'] ?? null;
        $resetUserId = $_GET['uid'] ?? null;
        $resetUserName = null;
        if (!empty($resetUserId)) {
            $ru = User::findById((int)$resetUserId);
            if ($ru) {
                $resetUserName = (string)($ru['username'] ?? null);
            }
        }
        echo $this->view('admin/users', [
            'users' => $users,
            'csrf' => Csrf::token(),
            'resetToken' => $resetToken,
            'resetUserId' => $resetUserId,
            'resetUserName' => $resetUserName,
            'currentUser' => $currentUser,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'pages' => $pages,
            'latestPayments' => $latestPayments,
            'packageCategories' => $packageCategories,
            'packageMap' => $packageMap,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $tier = (string)($request->post['access_tier'] ?? 'user');

        $current = Auth::user();
        $target = User::findById($id);
        if (!$current || !$target) {
            Response::redirect(base_path('/admin/users'));
        }

        if ($target['role'] === 'superadmin') {
            Response::redirect(base_path('/admin/users'));
        }

        $username = trim((string)($request->post['username'] ?? (string)$target['username']));
        $email = trim((string)($request->post['email'] ?? (string)$target['email']));
        $phone = trim((string)($request->post['phone'] ?? (string)$target['phone']));
        $phoneCountry = trim((string)($request->post['phone_country'] ?? (string)$target['phone_country']));
        $birthDate = trim((string)($request->post['birth_date'] ?? (string)$target['birth_date']));
        $observations = trim((string)($request->post['observations'] ?? (string)($target['observations'] ?? '')));
        $phoneWhatsApp = (int)($request->post['phone_has_whatsapp'] ?? (int)($target['phone_has_whatsapp'] ?? 0));

        if ($username === '' || $email === '' || $phone === '' || $phoneCountry === '' || $birthDate === '') {
            Response::redirect(base_path('/admin/users'));
        }

        User::updateProfile($id, [
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'phone_country' => $phoneCountry,
            'phone_has_whatsapp' => $phoneWhatsApp,
            'birth_date' => $birthDate,
            'observations' => $observations,
            'access_tier' => $tier,
        ]);
        Response::redirect(base_path('/admin/users'));
    }

    public function restrict(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $target = User::findById($id);
        if (!$target || $target['role'] === 'superadmin' || $target['role'] === 'admin') {
            Response::redirect(base_path('/admin/users'));
        }

        $isRestricted = ($target['access_tier'] ?? '') === 'restrito';
        $newTier = $isRestricted ? 'user' : 'restrito';

        User::updateRoleFlags($id, 'user', 0, 0, 0);
        User::setAccessTier($id, $newTier);
        Response::redirect(base_path('/admin/users'));
    }

    public function team(): void
    {
        $currentUser = Auth::user();
        echo $this->view('admin/team', [
            'teamMembers' => User::teamMembers(),
            'userPool' => User::nonStaff(),
            'csrf' => Csrf::token(),
            'currentUser' => $currentUser,
        ]);
    }

    public function teamUpdate(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/team'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $role = (string)($request->post['role'] ?? '');

        $current = Auth::user();
        $target = User::findById($id);
        if (!$current || !$target) {
            Response::redirect(base_path('/admin/team'));
        }

        if ($target['role'] === 'superadmin') {
            Response::redirect(base_path('/admin/team'));
        }

        $currentRole = (string)($current['role'] ?? 'user');
        if ($role === '') {
            $role = (string)($target['role'] ?? 'user');
        }

        $supportAgent = !empty($request->post['support_agent']) ? 1 : 0;
        $uploaderAgent = !empty($request->post['uploader_agent']) ? 1 : 0;
        $moderatorAgent = !empty($request->post['moderator_agent']) ? 1 : 0;

        $isSuper = $currentRole === 'superadmin';
        $isAdmin = $currentRole === 'admin' || $isSuper;
        $isModerator = ($currentRole === 'equipe' && !empty($current['moderator_agent']));

        if ($role === '') {
            $role = (string)($target['role'] ?? 'user');
        }
        if ($role === 'superadmin') {
            Response::redirect(base_path('/admin/team'));
        }

        if ($isSuper) {
            // superadmin pode gerir admin e equipe
        } elseif ($isAdmin) {
            if ($role === 'admin') {
                Response::redirect(base_path('/admin/team'));
            }
            if (($target['role'] ?? '') === 'admin') {
                Response::redirect(base_path('/admin/team'));
            }
        } elseif ($isModerator) {
            if ($role !== 'equipe') {
                Response::redirect(base_path('/admin/team'));
            }
            if (in_array(($target['role'] ?? ''), ['admin','superadmin'], true)) {
                Response::redirect(base_path('/admin/team'));
            }
        } else {
            Response::redirect(base_path('/admin/team'));
        }

        if (!$isAdmin) {
            $supportAgent = (int)($target['support_agent'] ?? 0);
            $moderatorAgent = (int)($target['moderator_agent'] ?? 0);
        }
        if (!$isAdmin && !$isModerator) {
            $uploaderAgent = (int)($target['uploader_agent'] ?? 0);
        }

        if ($role === 'admin' || $role === 'superadmin') {
            $supportAgent = 0;
            $uploaderAgent = 0;
            $moderatorAgent = 0;
        }

        if ($role === 'equipe' && ($supportAgent > 0 || $uploaderAgent > 0 || $moderatorAgent > 0) === false) {
            $role = 'equipe';
        }

        User::updateRoleFlags($id, $role, $supportAgent > 0 ? 1 : 0, $uploaderAgent > 0 ? 1 : 0, $moderatorAgent > 0 ? 1 : 0);
        Response::redirect(base_path('/admin/team'));
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
