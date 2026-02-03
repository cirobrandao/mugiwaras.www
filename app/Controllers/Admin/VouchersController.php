<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Models\Setting;

final class VouchersController extends Controller
{
    public function index(): void
    {
        $settings = array_values(array_filter(Setting::all(), function ($s): bool {
            $key = (string)($s['key'] ?? '');
            return !str_starts_with($key, 'system_');
        }));
        echo $this->view('admin/vouchers', [
            'settings' => $settings,
            'csrf' => Csrf::token(),
        ]);
    }

    public function save(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/vouchers'));
        }
        $key = trim((string)($request->post['key'] ?? ''));
        $valueRaw = trim((string)($request->post['value'] ?? ''));
        $value = (string)max(0, (int)$valueRaw);
        if ($key !== '' && !str_starts_with($key, 'system_')) {
            Setting::set($key, $value);
        }
        Response::redirect(base_path('/admin/vouchers'));
    }

    public function remove(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/vouchers'));
        }
        $key = trim((string)($request->post['key'] ?? ''));
        if ($key !== '' && !str_starts_with($key, 'system_')) {
            Setting::delete($key);
        }
        Response::redirect(base_path('/admin/vouchers'));
    }
}