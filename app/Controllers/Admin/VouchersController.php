<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Core\Auth;
use App\Core\Audit;
use App\Models\Voucher;
use App\Models\Package;
use App\Models\Payment;

final class VouchersController extends Controller
{
    public function index(): void
    {
        $vouchers = Voucher::all();
        $packages = Package::all();
        $redeemerIds = [];
        $redeemerMap = [];
        foreach ($vouchers as $voucher) {
            $usersDetailed = $voucher['redeemed_users_detailed'] ?? [];
            if (!is_array($usersDetailed)) {
                continue;
            }
            foreach ($usersDetailed as $user) {
                $uid = (int)($user['id'] ?? 0);
                if ($uid <= 0) {
                    continue;
                }
                $redeemerIds[$uid] = $uid;
                $redeemerMap[$uid] = (string)($user['username'] ?? ('#' . $uid));
            }
        }

        $redeemerIds = array_values($redeemerIds);
        $paymentsByUser = Payment::historyByUsers($redeemerIds);
        $vouchersByUser = Voucher::redemptionHistoryByUsers($redeemerIds);
        $userCommerceHistory = [];

        foreach ($paymentsByUser as $payment) {
            $uid = (int)($payment['user_id'] ?? 0);
            if ($uid <= 0) {
                continue;
            }
            if (!isset($userCommerceHistory[$uid])) {
                $userCommerceHistory[$uid] = [];
            }
            $userCommerceHistory[$uid][] = [
                'type' => 'payment',
                'date' => (string)($payment['created_at'] ?? ''),
                'payment' => $payment,
            ];
        }

        foreach ($vouchersByUser as $voucher) {
            $uid = (int)($voucher['user_id'] ?? 0);
            if ($uid <= 0) {
                continue;
            }
            if (!isset($userCommerceHistory[$uid])) {
                $userCommerceHistory[$uid] = [];
            }
            $userCommerceHistory[$uid][] = [
                'type' => 'voucher',
                'date' => (string)($voucher['redeemed_at'] ?? ''),
                'voucher' => $voucher,
            ];
        }

        foreach ($userCommerceHistory as &$historyItems) {
            usort($historyItems, static function (array $a, array $b): int {
                $aDate = strtotime((string)($a['date'] ?? '')) ?: 0;
                $bDate = strtotime((string)($b['date'] ?? '')) ?: 0;
                return $bDate <=> $aDate;
            });
        }
        unset($historyItems);

        echo $this->view('admin/vouchers', [
            'vouchers' => $vouchers,
            'packages' => $packages,
            'userCommerceHistory' => $userCommerceHistory,
            'redeemerMap' => $redeemerMap,
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

        $currentUser = Auth::user();
        Audit::log('voucher_saved', (int)($currentUser['id'] ?? 0) ?: null, [
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