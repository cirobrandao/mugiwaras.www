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
            $user = Auth::user();
            if (!$user) {
                Response::redirect(base_path('/'));
            }
            if (Auth::needsProfileUpdate($user)) {
                $path = parse_url($request->uri, PHP_URL_PATH) ?: '/';
                $allow = ['/perfil/editar', '/logout'];
                if (!in_array($path, $allow, true)) {
                    Response::redirect(base_path('/perfil/editar?force=1'));
                }
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
            $isAdmin = in_array($user['role'], ['admin', 'superadmin'], true);
            $isEquipe = ($user['role'] ?? '') === 'equipe';
            if ($isAdmin || ($isEquipe && (!empty($user['uploader_agent']) || !empty($user['moderator_agent']) || !empty($user['support_agent'])))) {
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
                    Response::redirect(base_path('/loja?expired=1'));
                }
            }
        };
    }
}
