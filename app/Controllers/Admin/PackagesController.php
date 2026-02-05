<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Models\Package;
use App\Models\Category;
use App\Models\Payment;

final class PackagesController extends Controller
{
    public function index(): void
    {
        $packages = Package::all();
        $categories = Category::all();
        $packageIds = array_map(static fn ($p) => (int)($p['id'] ?? 0), $packages);
        $packageCategories = Package::categoriesMap($packageIds);
        $packagePayments = Payment::countByPackageIds($packageIds);
        echo $this->view('admin/packages', [
            'packages' => $packages,
            'categories' => $categories,
            'packageCategories' => $packageCategories,
            'packagePayments' => $packagePayments,
            'csrf' => Csrf::token(),
            'error' => $_GET['error'] ?? null,
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
        $order = (int)($request->post['sort_order'] ?? 0);
        $categories = $request->post['categories'] ?? [];

        if ($title === '') {
            Response::redirect(base_path('/admin/packages'));
        }

        $packageId = Package::create([
            't' => $title,
            'd' => $description,
            'p' => $price,
            'b' => $bonus,
            's' => $days,
            'o' => $order,
        ]);
        Package::setCategories($packageId, is_array($categories) ? $categories : []);
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
        $order = (int)($request->post['sort_order'] ?? 0);
        $categories = $request->post['categories'] ?? [];

        if ($id <= 0 || $title === '') {
            Response::redirect(base_path('/admin/packages'));
        }

        Package::update($id, [
            't' => $title,
            'd' => $description,
            'p' => $price,
            'b' => $bonus,
            's' => $days,
            'o' => $order,
        ]);
        Package::setCategories($id, is_array($categories) ? $categories : []);
        Response::redirect(base_path('/admin/packages'));
    }

    public function delete(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/packages'));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id > 0) {
            $payments = Payment::countByPackageIds([$id]);
            if (!empty($payments[$id])) {
                Response::redirect(base_path('/admin/packages?error=has_payments'));
            }
            Package::delete($id);
        }
        Response::redirect(base_path('/admin/packages'));
    }
}
