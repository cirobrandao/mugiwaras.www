<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Core\Auth;
use App\Core\Audit;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Voucher;
use App\Models\User;
use App\Core\Database;

final class PaymentController extends Controller
{
    public function packages(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $packages = Package::all();
        $categories = Category::all();
        $packageIds = array_map(static fn ($p) => (int)($p['id'] ?? 0), $packages);
        $packageCategories = Package::categoriesMap($packageIds);
        echo $this->view('loja/packages', [
            'packages' => $packages,
            'categories' => $categories,
            'packageCategories' => $packageCategories,
            'csrf' => Csrf::token(),
        ]);
    }

    public function checkout(Request $request, string $packageId): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $package = Package::find((int)$packageId);
        if (!$package) {
            Response::redirect(base_path('/loja?error=package'));
        }
        $months = (int)($_GET['months'] ?? 1);
        if ($months < 1) {
            $months = 1;
        } elseif ($months > 12) {
            $months = 12;
        }
        $baseTotal = (float)($package['price'] ?? 0) * $months;
        $total = $baseTotal;
        $prorataCredit = 0.0;
        $remainingDays = 0;
        $currentPackageTitle = null;

        $lastApproved = Payment::latestApprovedByUser((int)$user['id']);
        if ($lastApproved) {
            $currentPackage = Package::find((int)$lastApproved['package_id']);
            if ($currentPackage) {
                $currentPackageTitle = (string)($currentPackage['title'] ?? '');
            }
            $expiresAt = $user['subscription_expires_at'] ?? null;
            $expiresTs = is_string($expiresAt) ? strtotime($expiresAt) : false;
            if ($expiresTs !== false && $expiresTs > time()) {
                $remainingDays = (int)ceil(($expiresTs - time()) / 86400);
                if ($currentPackage && (int)$currentPackage['subscription_days'] > 0) {
                    $currentMonths = (int)($lastApproved['months'] ?? 1);
                    $currentMonths = max(1, min(12, $currentMonths));
                    $currentTotalDays = (int)$currentPackage['subscription_days'] * $currentMonths;
                    $currentTotalPrice = (float)($currentPackage['price'] ?? 0) * $currentMonths;
                    if ($currentTotalDays > 0 && $currentTotalPrice > 0) {
                        $dailyRate = $currentTotalPrice / $currentTotalDays;
                        $prorataCredit = $dailyRate * $remainingDays;
                        $total = max(0.0, $baseTotal - $prorataCredit);
                    }
                }
            }
        }
        $pixKey = Setting::get('pix_key', '');
        $pixName = Setting::get('pix_receiver', '');
        echo $this->view('loja/checkout', [
            'package' => $package,
            'months' => $months,
            'baseTotal' => $baseTotal,
            'prorataCredit' => $prorataCredit,
            'remainingDays' => $remainingDays,
            'currentPackageTitle' => $currentPackageTitle,
            'total' => $total,
            'csrf' => Csrf::token(),
            'pixKey' => $pixKey,
            'pixName' => $pixName,
        ]);
    }

    public function requestPayment(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/loja'));
        }
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $packageId = (int)($request->post['package_id'] ?? 0);
        $months = (int)($request->post['months'] ?? 1);
        if ($months < 1) {
            $months = 1;
        } elseif ($months > 12) {
            $months = 12;
        }
        if (!Package::find($packageId)) {
            Response::redirect(base_path('/loja?error=package'));
        }
        $paymentId = Payment::create([
            'uid' => (int)$user['id'],
            'pid' => $packageId,
            'status' => 'pending',
            'months' => $months,
        ]);

        if (!empty($request->files['proof']) && $request->files['proof']['error'] === UPLOAD_ERR_OK) {
            $file = $request->files['proof'];
            $ext = $this->validateProof($file);
            $storageRoot = dirname(__DIR__, 2) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
            $proofDir = $storageRoot . '/payments';
            if (!is_dir($proofDir)) {
                mkdir($proofDir, 0777, true);
            }
            $safeName = 'payment_' . $paymentId . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $target = $proofDir . '/' . $safeName;
            if (move_uploaded_file((string)$file['tmp_name'], $target)) {
                Payment::attachProof($paymentId, 'payments/' . $safeName);
                Audit::log('payment_proof_upload', (int)$user['id'], ['payment_id' => $paymentId]);
            }
        }

        Audit::log('payment_request', (int)$user['id'], ['package_id' => $packageId]);
        Response::redirect(base_path('/loja?requested=1'));
    }

    public function redeemVoucher(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/loja?voucher=error'));
        }
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $code = strtoupper(trim((string)($request->post['code'] ?? '')));
        if ($code === '') {
            Response::redirect(base_path('/loja?voucher=invalid'));
        }
        $voucher = Voucher::findByCode($code);
        if (!$voucher || empty($voucher['is_active'])) {
            Response::redirect(base_path('/loja?voucher=invalid'));
        }
        $expiresAt = $voucher['expires_at'] ?? null;
        if (!empty($expiresAt)) {
            $expTs = strtotime((string)$expiresAt);
            if ($expTs !== false && $expTs < time()) {
                Response::redirect(base_path('/loja?voucher=expired'));
            }
        }
        $maxUses = (int)($voucher['max_uses'] ?? 0);
        $uses = (int)($voucher['uses'] ?? 0);
        if ($maxUses > 0 && $uses >= $maxUses) {
            Response::redirect(base_path('/loja?voucher=limit'));
        }
        if (Voucher::hasRedeemed($code, (int)$user['id'])) {
            Response::redirect(base_path('/loja?voucher=used'));
        }
        $packageId = (int)($voucher['package_id'] ?? 0);
        $package = $packageId > 0 ? Package::find($packageId) : null;
        if (!$package) {
            Response::redirect(base_path('/loja?voucher=invalid'));
        }
        $days = (int)($voucher['days'] ?? 0);
        if ($days <= 0) {
            $days = (int)($package['subscription_days'] ?? 0);
        }
        $months = 1;
        if ((int)($package['subscription_days'] ?? 0) > 0) {
            $months = (int)ceil(max(1, $days) / (int)$package['subscription_days']);
        }

        $db = Database::connection();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare('INSERT INTO voucher_redemptions (voucher_code, user_id, redeemed_at) VALUES (:c,:u,NOW())');
            $stmt->execute(['c' => $code, 'u' => (int)$user['id']]);

            $up = $db->prepare('UPDATE vouchers SET uses = uses + 1 WHERE code = :c');
            $up->execute(['c' => $code]);

            Payment::create([
                'uid' => (int)$user['id'],
                'pid' => $packageId,
                'status' => 'approved',
                'months' => $months,
            ]);

            if ($days > 0) {
                User::extendSubscription((int)$user['id'], $days);
            }
            if (($user['access_tier'] ?? '') === 'restrito') {
                User::setAccessTier((int)$user['id'], 'user');
            }
            $db->commit();
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            Response::redirect(base_path('/loja?voucher=error'));
        }

        Response::redirect(base_path('/loja?voucher=ok'));
    }

    public function history(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $payments = Payment::byUser((int)$user['id']);
        echo $this->view('loja/history', ['payments' => $payments, 'csrf' => Csrf::token()]);
    }

    public function uploadProof(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/loja/history'));
        }
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $paymentId = (int)($request->post['payment_id'] ?? 0);
        $payment = Payment::find($paymentId);
        if (!$payment || (int)$payment['user_id'] !== (int)$user['id']) {
            Response::redirect(base_path('/loja/history'));
        }
        if (empty($request->files['proof']) || $request->files['proof']['error'] !== UPLOAD_ERR_OK) {
            Response::redirect(base_path('/loja/history?error=proof'));
        }

        $file = $request->files['proof'];
        $ext = $this->validateProof($file);

        $storageRoot = dirname(__DIR__, 2) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
        $proofDir = $storageRoot . '/payments';
        if (!is_dir($proofDir)) {
            mkdir($proofDir, 0777, true);
        }
        $safeName = 'payment_' . $paymentId . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $target = $proofDir . '/' . $safeName;
        if (!move_uploaded_file((string)$file['tmp_name'], $target)) {
            Response::redirect(base_path('/loja/history?error=move'));
        }

        Payment::attachProof($paymentId, 'payments/' . $safeName);
        Audit::log('payment_proof_upload', (int)$user['id'], ['payment_id' => $paymentId]);
        Response::redirect(base_path('/loja/history?uploaded=1'));
    }

    private function validateProof(array $file): string
    {
        if ((int)$file['size'] > 4 * 1024 * 1024) {
            Response::redirect(base_path('/loja/history?error=size'));
        }
        $ext = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file((string)$file['tmp_name']);
        $allowed = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
        ];
        if (!isset($allowed[$ext]) || $allowed[$ext] !== $mime) {
            Response::redirect(base_path('/loja/history?error=type'));
        }
        return $ext;
    }
}
