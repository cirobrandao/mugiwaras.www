<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Models\LoginHistory;

final class LogController extends Controller
{
    public function index(Request $request): void
    {
        $currentUser = Auth::user();
        if (!$currentUser || !Auth::isAdmin($currentUser)) {
            Response::redirect(base_path('/home'));
        }

        $query = trim((string)($request->get['q'] ?? ''));
        $perPage = (int)($request->get['perPage'] ?? 100);
        $perPage = max(10, min(500, $perPage));
        $page = (int)($request->get['page'] ?? 1);
        $page = max(1, $page);

        $total = LoginHistory::countAccessLogs($query);
        $pages = max(1, (int)ceil($total / max(1, $perPage)));
        if ($page > $pages) {
            $page = $pages;
        }
        $items = LoginHistory::accessLogs($query, $page, $perPage);

        echo $this->view('admin/log', [
            'query' => $query,
            'items' => $items,
            'page' => $page,
            'pages' => $pages,
            'perPage' => $perPage,
            'total' => $total,
        ]);
    }
}
