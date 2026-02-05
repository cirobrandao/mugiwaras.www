<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use App\Models\UserToken;
use App\Models\UserBlocklist;

final class Auth
{
    public static function isSuperadmin(?array $user): bool
    {
        return !empty($user) && ($user['role'] ?? '') === 'superadmin';
    }

    public static function isAdmin(?array $user): bool
    {
        return !empty($user) && in_array($user['role'] ?? '', ['admin', 'superadmin'], true);
    }

    public static function isEquipe(?array $user): bool
    {
        return !empty($user) && ($user['role'] ?? '') === 'equipe';
    }

    public static function isModerator(?array $user): bool
    {
        return self::isEquipe($user) && !empty($user['moderator_agent']);
    }

    public static function isUploader(?array $user): bool
    {
        return self::isEquipe($user) && !empty($user['uploader_agent']);
    }

    public static function isSupportAgent(?array $user): bool
    {
        return !empty($user) && !empty($user['support_agent']);
    }

    public static function canUpload(?array $user): bool
    {
        return self::isAdmin($user) || self::isModerator($user) || self::isUploader($user);
    }

    public static function isSupportStaff(?array $user): bool
    {
        return self::isAdmin($user) || self::isSupportAgent($user);
    }
    public static function user(): ?array
    {
        if (isset($_SESSION['user_id'])) {
            $user = User::findById((int)$_SESSION['user_id']);
            if ($user && UserBlocklist::isBlocked((int)$user['id'])) {
                self::logout();
                return null;
            }
            return $user;
        }
        return null;
    }

    public static function attempt(string $username, string $password, bool $remember, Request $request): bool
    {
        $user = User::findByUsername($username);
        if (!$user) {
            return false;
        }
        if ($user['lock_until'] && strtotime($user['lock_until']) > time()) {
            return false;
        }
        if (!password_verify($password, $user['password_hash'])) {
            User::incrementFailedLogins((int)$user['id']);
            return false;
        }

        if (UserBlocklist::isBlocked((int)$user['id'])) {
            return false;
        }

        User::resetFailedLogins((int)$user['id']);
        User::updateLastLogin((int)$user['id'], $request->ip(), $request->userAgent());
        $_SESSION['user_id'] = $user['id'];

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            UserToken::create((int)$user['id'], $token);
            setcookie('remember_me', $token, [
                'expires' => time() + (int)config('security.remember_days', 30) * 86400,
                'path' => base_path('/'),
                'secure' => (bool)config('security.session_secure', true),
                'httponly' => true,
                'samesite' => (string)config('security.session_samesite', 'Lax'),
            ]);
        }

        return true;
    }

    public static function checkRemember(Request $request): void
    {
        if (isset($_SESSION['user_id'])) {
            return;
        }
        $token = $request->cookies['remember_me'] ?? null;
        if ($token) {
            $userId = UserToken::validate($token);
            if ($userId) {
                $_SESSION['user_id'] = $userId;
                UserToken::rotate($userId, $token);
            }
        }
    }

    public static function logout(): void
    {
        if (isset($_COOKIE['remember_me'])) {
            UserToken::revoke($_COOKIE['remember_me']);
            setcookie('remember_me', '', time() - 3600, base_path('/'));
        }
        session_destroy();
    }

    public static function requireRole(array $roles): callable
    {
        return function (Request $request) use ($roles): void {
            $user = self::user();
            if (!$user || !in_array($user['role'], $roles, true)) {
                Response::redirect(base_path('/'));
            }
        };
    }

    public static function requireAdmin(): callable
    {
        return function (Request $request): void {
            $user = self::user();
            if (!self::isAdmin($user)) {
                Response::redirect(base_path('/'));
            }
        };
    }

    public static function requireTeamAccess(): callable
    {
        return function (Request $request): void {
            $user = self::user();
            if (!self::isAdmin($user) && !self::isModerator($user)) {
                Response::redirect(base_path('/'));
            }
        };
    }

    public static function requireUploadAccess(): callable
    {
        return function (Request $request): void {
            $user = self::user();
            if (!self::canUpload($user)) {
                Response::redirect(base_path('/'));
            }
        };
    }

    public static function requireSupportStaff(): callable
    {
        return function (Request $request): void {
            $user = self::user();
            if (!self::isSupportStaff($user)) {
                Response::redirect(base_path('/'));
            }
        };
    }
}
