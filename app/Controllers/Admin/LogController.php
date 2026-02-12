<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Models\User;

final class LogController extends Controller
{
    public function index(Request $request): void
    {
        $currentUser = Auth::user();
        if (!$currentUser || !Auth::isAdmin($currentUser)) {
            Response::redirect(base_path('/dashboard'));
        }

        $query = trim((string)($request->get['q'] ?? ''));
        $items = $query !== '' ? User::searchByLastIp($query) : [];

        echo $this->view('admin/log', [
            'query' => $query,
            'items' => $items,
        ]);
    }
}
