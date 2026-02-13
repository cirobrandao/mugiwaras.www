<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use App\Models\UserToken;
use App\Models\LoginHistory;
use App\Core\Validation;

final class Auth
{
    private static function rememberFingerprint(Request $request): string
    {
        $ua = mb_strtolower(trim($request->userAgent()));
        $ua = preg_replace('/\s+/', ' ', $ua) ?? $ua;
        return hash('sha256', $ua);
    }

    private static function rememberIntended(Request $request): void
    {
        $path = parse_url($request->uri, PHP_URL_PATH) ?: '/';
        $query = parse_url($request->uri, PHP_URL_QUERY) ?: '';
        $disallow = ['/', '/login', '/logout', '/register', '/recover'];
        if (in_array($path, $disallow, true)) {
            return;
        }
        if (!str_starts_with($path, '/')) {
            return;
        }
        $target = $path . ($query !== '' ? ('?' . $query) : '');
        $_SESSION['intended_url'] = $target;
    }

    public static function pullIntendedUrl(): ?string
    {
        $value = (string)($_SESSION['intended_url'] ?? '');
        unset($_SESSION['intended_url']);
        if ($value === '' || !str_starts_with($value, '/')) {
            return null;
        }
        if (str_starts_with($value, '//')) {
            return null;
        }
        return $value;
    }

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
            return $user;
        }
        return null;
    }

    public static function needsProfileUpdate(array $user): bool
    {
        $username = (string)($user['username'] ?? '');
        if (mb_strlen($username) < 5) {
            return true;
        }
        $phone = trim((string)($user['phone'] ?? ''));
        $phoneCountry = trim((string)($user['phone_country'] ?? ''));
        if ($phoneCountry === '' || !Validation::phoneCountry($phoneCountry)) {
            return true;
        }
        if ($phone === '' || !Validation::phoneByCountry($phone, $phoneCountry)) {
            return true;
        }
        $birth = trim((string)($user['birth_date'] ?? ''));
        if ($birth === '' || $birth === '0000-00-00' || !Validation::birthDate($birth)) {
            return true;
        }
        return false;
    }

    private static function enforceProfileComplete(Request $request, ?array $user): void
    {
        if (!$user) {
            return;
        }
        if (self::needsProfileUpdate($user)) {
            $path = parse_url($request->uri, PHP_URL_PATH) ?: '/';
            $allow = ['/user/editar', '/logout'];
            if (!in_array($path, $allow, true)) {
                Response::redirect(base_path('/user/editar?force=1'));
            }
        }
    }

    public static function attempt(string $username, string $password, bool $remember, Request $request): bool
    {
        $login = trim($username);
        $user = null;
        if ($login !== '') {
            if (strpos($login, '@') !== false) {
                $user = User::findByEmail(mb_strtolower($login));
            }
            if (!$user) {
                $user = User::findByUsername($login);
                if (!$user && $login !== mb_strtolower($login)) {
                    $user = User::findByUsername(mb_strtolower($login));
                }
            }
        }
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

        User::resetFailedLogins((int)$user['id']);
        User::updateLastLogin((int)$user['id'], $request->ip(), $request->userAgent());
        LoginHistory::record((int)$user['id'], $request->ip(), $request->userAgent());
        $_SESSION['user_id'] = $user['id'];

        if ($remember) {
            if (!empty($request->cookies['remember_me'])) {
                UserToken::revoke((string)$request->cookies['remember_me'], self::rememberFingerprint($request));
            }
            $token = bin2hex(random_bytes(32));
            $fingerprint = self::rememberFingerprint($request);
            UserToken::create((int)$user['id'], $token, $fingerprint);
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
            $fingerprint = self::rememberFingerprint($request);
            $userId = UserToken::validate($token, $fingerprint);
            if ($userId) {
                $_SESSION['user_id'] = $userId;
                UserToken::rotate($userId, $token, $fingerprint);
            }
        }
    }

    public static function logout(?Request $request = null): void
    {
        $fingerprint = $request ? self::rememberFingerprint($request) : '';
        if (isset($_COOKIE['remember_me'])) {
            UserToken::revoke((string)$_COOKIE['remember_me'], $fingerprint);
            setcookie('remember_me', '', [
                'expires' => time() - 3600,
                'path' => base_path('/'),
                'secure' => (bool)config('security.session_secure', true),
                'httponly' => true,
                'samesite' => (string)config('security.session_samesite', 'Lax'),
            ]);
        }
        session_destroy();
    }

    public static function requireRole(array $roles): callable
    {
        return function (Request $request) use ($roles): void {
            $user = self::user();
            if (!$user || !in_array($user['role'], $roles, true)) {
                self::rememberIntended($request);
                Response::redirect(base_path('/'));
            }
            self::enforceProfileComplete($request, $user);
        };
    }

    public static function requireAdmin(): callable
    {
        return function (Request $request): void {
            $user = self::user();
            if (!self::isAdmin($user)) {
                self::rememberIntended($request);
                Response::redirect(base_path('/'));
            }
            self::enforceProfileComplete($request, $user);
        };
    }

    public static function requireTeamAccess(): callable
    {
        return function (Request $request): void {
            $user = self::user();
            if (!self::isAdmin($user) && !self::isModerator($user)) {
                self::rememberIntended($request);
                Response::redirect(base_path('/'));
            }
            self::enforceProfileComplete($request, $user);
        };
    }

    public static function requireUploadAccess(): callable
    {
        return function (Request $request): void {
            $user = self::user();
            if (!self::canUpload($user)) {
                self::rememberIntended($request);
                Response::redirect(base_path('/'));
            }
            self::enforceProfileComplete($request, $user);
        };
    }

    public static function requireSupportStaff(): callable
    {
        return function (Request $request): void {
            $user = self::user();
            if (!self::isSupportStaff($user)) {
                self::rememberIntended($request);
                Response::redirect(base_path('/'));
            }
            self::enforceProfileComplete($request, $user);
        };
    }
}
