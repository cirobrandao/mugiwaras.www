<?php

declare(strict_types=1);

namespace App\Core;

final class Middleware
{
    public static function requireAuth(): callable
    {
        return function (Request $request): void {
            if (!Auth::user()) {
                Response::redirect(base_path('/'));
            }
        };
    }

    public static function requireActiveAccess(): callable
    {
        return function (Request $request): void {
            $user = Auth::user();
            if (!$user) {
                Response::redirect(base_path('/'));
            }
            if (in_array($user['role'], ['admin', 'superadmin', 'moderator', 'uploader'], true)) {
                return;
            }
            if (($user['access_tier'] ?? '') === 'restrito') {
                Response::redirect(base_path('/support?restricted=1'));
            }
            if ($user['access_tier'] === 'vitalicio') {
                return;
            }
            if (!empty($user['subscription_expires_at'])) {
                $expires = strtotime((string)$user['subscription_expires_at']);
                if ($expires !== false && $expires < time()) {
                    Response::redirect(base_path('/payments?expired=1'));
                }
            }
        };
    }
}
