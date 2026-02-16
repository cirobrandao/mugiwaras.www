<?php

declare(strict_types=1);

namespace App\Core;

final class Middleware
{
    public static function requireAuth(): callable
    {
        return function (Request $request): void {
            if (!Auth::user()) {
                $path = parse_url($request->uri, PHP_URL_PATH) ?: '/';
                $query = parse_url($request->uri, PHP_URL_QUERY) ?: '';
                if ($path !== '/' && !str_starts_with($path, '/login')) {
                    $_SESSION['intended_url'] = $path . ($query !== '' ? ('?' . $query) : '');
                }
                Response::redirect(base_path('/'));
            }
            $user = Auth::user();
            if (!$user) {
                Response::redirect(base_path('/'));
            }
            
            // Update activity timestamp every 5 minutes for online user tracking
            $lastActivity = $_SESSION['last_activity_update'] ?? 0;
            $now = time();
            if (($now - $lastActivity) >= 300) { // 5 minutes
                try {
                    \App\Models\User::updateActivity((int)$user['id']);
                    $_SESSION['last_activity_update'] = $now;
                } catch (\Exception $e) {
                    error_log("Activity tracking error: " . $e->getMessage());
                }
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
                $path = parse_url($request->uri, PHP_URL_PATH) ?: '/';
                $query = parse_url($request->uri, PHP_URL_QUERY) ?: '';
                if ($path !== '/' && !str_starts_with($path, '/login')) {
                    $_SESSION['intended_url'] = $path . ($query !== '' ? ('?' . $query) : '');
                }
                Response::redirect(base_path('/'));
            }
            $isAdmin = in_array($user['role'], ['admin', 'superadmin'], true);
            $isEquipe = ($user['role'] ?? '') === 'equipe';
            if ($isAdmin || $isEquipe) {
                return;
            }
            if (($user['access_tier'] ?? '') === 'restrito') {
                Response::abort404('Voce nao tem acesso a esta pagina.');
            }
            if (in_array((string)($user['access_tier'] ?? ''), ['vitalicio', 'especial'], true)) {
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
