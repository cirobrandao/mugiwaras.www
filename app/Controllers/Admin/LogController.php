<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\User;

final class LogController extends Controller
{
    public function index(Request $request): void
    {
        $query = trim((string)($request->get['q'] ?? ''));
        $items = $query !== '' ? User::searchByLastIp($query) : [];

        echo $this->view('admin/log', [
            'query' => $query,
            'items' => $items,
        ]);
    }
}
