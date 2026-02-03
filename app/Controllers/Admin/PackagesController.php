<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Models\Package;

final class PackagesController extends Controller
{
    public function index(): void
    {
        $packages = Package::all();
        echo $this->view('admin/packages', [
            'packages' => $packages,
            'csrf' => Csrf::token(),
        ]);
    }

    public function create(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/packages'));
        }

        $title = trim((string)($request->post['title'] ?? ''));
        $description = trim((string)($request->post['description'] ?? ''));
        $price = (float)($request->post['price'] ?? 0);
        $bonus = (int)($request->post['bonus_credits'] ?? 0);
        $days = (int)($request->post['subscription_days'] ?? 0);

        if ($title === '') {
            Response::redirect(base_path('/admin/packages'));
        }

        Package::create([
            't' => $title,
            'd' => $description,
            'p' => $price,
            'b' => $bonus,
            's' => $days,
        ]);
        Response::redirect(base_path('/admin/packages'));
    }

    public function update(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/packages'));
        }

        $id = (int)($request->post['id'] ?? 0);
        $title = trim((string)($request->post['title'] ?? ''));
        $description = trim((string)($request->post['description'] ?? ''));
        $price = (float)($request->post['price'] ?? 0);
        $bonus = (int)($request->post['bonus_credits'] ?? 0);
        $days = (int)($request->post['subscription_days'] ?? 0);

        if ($id <= 0 || $title === '') {
            Response::redirect(base_path('/admin/packages'));
        }

        Package::update($id, [
            't' => $title,
            'd' => $description,
            'p' => $price,
            'b' => $bonus,
            's' => $days,
        ]);
        Response::redirect(base_path('/admin/packages'));
    }

    public function delete(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/packages'));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id > 0) {
            Package::delete($id);
        }
        Response::redirect(base_path('/admin/packages'));
    }
}
