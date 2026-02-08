<?php

declare(strict_types=1);

namespace App\Core;

final class Middleware
{
    public static function requireAuth(): callable
    {
        return function (Request $request): void {
            if (!Auth::user()) {
                Response::abort404('Voce nao tem acesso a esta pagina.');
            }
            $user = Auth::user();
            if (!$user) {
                Response::abort404('Voce nao tem acesso a esta pagina.');
            }
            if (Auth::needsProfileUpdate($user)) {
                $path = parse_url($request->uri, PHP_URL_PATH) ?: '/';
                $allow = ['/user/editar', '/logout'];
                if (!in_array($path, $allow, true)) {
                    Response::redirect(base_path('/user/editar?force=1'));
                }
            }
        };
    }

    public static function requireActiveAccess(): callable
    {
        return function (Request $request): void {
            $user = Auth::user();
            if (!$user) {
                Response::abort404('Voce nao tem acesso a esta pagina.');
            }
            $isAdmin = in_array($user['role'], ['admin', 'superadmin'], true);
            $isEquipe = ($user['role'] ?? '') === 'equipe';
            if ($isAdmin || ($isEquipe && (!empty($user['uploader_agent']) || !empty($user['moderator_agent']) || !empty($user['support_agent'])))) {
                return;
            }
            if (($user['access_tier'] ?? '') === 'restrito') {
                Response::abort404('Voce nao tem acesso a esta pagina.');
            }
            if ($user['access_tier'] === 'vitalicio') {
                return;
            }
            if (!empty($user['subscription_expires_at'])) {
                $expires = strtotime((string)$user['subscription_expires_at']);
                if ($expires !== false && $expires < time()) {
                    Response::abort404('Voce nao tem acesso a esta pagina.');
                }
            }
        };
    }
}
