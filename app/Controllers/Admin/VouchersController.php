<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Models\Voucher;
use App\Models\Package;

final class VouchersController extends Controller
{
    public function index(): void
    {
        $vouchers = Voucher::all();
        $packages = Package::all();
        echo $this->view('admin/vouchers', [
            'vouchers' => $vouchers,
            'packages' => $packages,
            'csrf' => Csrf::token(),
        ]);
    }

    public function save(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/vouchers'));
        }
        $code = strtoupper(trim((string)($request->post['code'] ?? '')));
        $days = max(0, (int)($request->post['days'] ?? 0));
        $maxUses = (int)($request->post['max_uses'] ?? 0);
        $expiresAtRaw = trim((string)($request->post['expires_at'] ?? ''));
        $packageId = (int)($request->post['package_id'] ?? 0);
        $isActive = !empty($request->post['is_active']) ? 1 : 0;

        if ($code === '') {
            $code = Voucher::generateUniqueCode();
        }
        if (!str_starts_with($code, 'VC-')) {
            Response::redirect(base_path('/admin/vouchers?error=code'));
        }
        if ($packageId <= 0 || !Package::find($packageId)) {
            Response::redirect(base_path('/admin/vouchers?error=package'));
        }

        $expiresAt = null;
        if ($expiresAtRaw !== '') {
            try {
                $dt = new \DateTimeImmutable($expiresAtRaw);
                $expiresAt = $dt->setTime(0, 0, 0)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $expiresAt = null;
            }
        }

        Voucher::upsert([
            'code' => $code,
            'package_id' => $packageId,
            'days' => $days,
            'max_uses' => $maxUses > 0 ? $maxUses : null,
            'expires_at' => $expiresAt,
            'is_active' => $isActive,
        ]);
        Response::redirect(base_path('/admin/vouchers'));
    }

    public function remove(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/vouchers'));
        }
        $code = strtoupper(trim((string)($request->post['code'] ?? '')));
        if ($code !== '' && str_starts_with($code, 'VC-')) {
            Voucher::delete($code);
        }
        Response::redirect(base_path('/admin/vouchers'));
    }
}