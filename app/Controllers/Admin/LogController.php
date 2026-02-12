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
        $ip = trim((string)($request->get['ip'] ?? ''));
        $items = $ip !== '' ? User::searchByLastIp($ip) : [];

        echo $this->view('admin/log', [
            'ip' => $ip,
            'items' => $items,
        ]);
    }
}
