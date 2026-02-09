<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Core\Audit;
use App\Core\Auth;
use App\Models\Payment;
use App\Models\Package;
use App\Models\User;

final class PaymentsController extends Controller
{
    public function index(): void
    {
        $payments = Payment::all();
        echo $this->view('admin/payments', [
            'payments' => $payments,
            'csrf' => Csrf::token(),
            'currentUser' => Auth::user(),
        ]);
    }

    public function approve(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/payments'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $payment = Payment::find($id);
        if (!$payment || $payment['status'] !== 'pending') {
            Response::redirect(base_path('/admin/payments'));
        }

        $package = Package::find((int)$payment['package_id']);
        $targetUser = User::findById((int)$payment['user_id']);
        if ($targetUser && ($targetUser['access_tier'] ?? '') === 'restrito') {
            User::setAccessTier((int)$payment['user_id'], 'user');
        }
        if ($package) {
            if ((int)$package['bonus_credits'] > 0) {
                User::addCredits((int)$payment['user_id'], (int)$package['bonus_credits']);
            }
            $months = (int)($payment['months'] ?? 1);
            if ($months < 1) {
                $months = 1;
            } elseif ($months > 12) {
                $months = 12;
            }
            $days = 30 * $months;
            User::extendSubscription((int)$payment['user_id'], $days);
        }

        Payment::setStatus($id, 'approved');
        Audit::log('payment_approve', $_SESSION['user_id'] ?? null, ['payment_id' => $id]);
        Response::redirect(base_path('/admin/payments'));
    }

    public function reject(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/payments'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $payment = Payment::find($id);
        if (!$payment || $payment['status'] !== 'pending') {
            Response::redirect(base_path('/admin/payments'));
        }
        Payment::setStatus($id, 'rejected');
        Audit::log('payment_reject', $_SESSION['user_id'] ?? null, ['payment_id' => $id]);
        Response::redirect(base_path('/admin/payments'));
    }

    public function revoke(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/payments'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $payment = Payment::find($id);
        if (!$payment || $payment['status'] !== 'approved' || !empty($payment['revoked_at'])) {
            Response::redirect(base_path('/admin/payments'));
        }

        $userId = (int)($payment['user_id'] ?? 0);
        $targetUser = User::findById($userId);
        if (!$targetUser) {
            Response::redirect(base_path('/admin/payments'));
        }

        $prevExpires = $targetUser['subscription_expires_at'] ?? null;

        $package = Package::find((int)($payment['package_id'] ?? 0));
        if ($package) {
            $bonusCredits = (int)($package['bonus_credits'] ?? 0);
            if ($bonusCredits > 0) {
                User::removeCredits($userId, $bonusCredits);
            }
            $months = (int)($payment['months'] ?? 1);
            if ($months < 1) {
                $months = 1;
            } elseif ($months > 12) {
                $months = 12;
            }
            if ((int)($package['subscription_days'] ?? 0) > 0) {
                User::setSubscriptionExpiresAt($userId, $prevExpires ? (string)$prevExpires : null);
            }
        }

        Payment::markRevoked($id, (int)($_SESSION['user_id'] ?? 0), $prevExpires ? (string)$prevExpires : null);
        Audit::log('payment_refund', $_SESSION['user_id'] ?? null, ['payment_id' => $id, 'user_id' => $userId]);
        Response::redirect(base_path('/admin/payments'));
    }

    public function cancelRevocation(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/payments'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $payment = Payment::find($id);
        if (!$payment || $payment['status'] !== 'revoked') {
            Response::redirect(base_path('/admin/payments'));
        }

        $userId = (int)($payment['user_id'] ?? 0);
        $targetUser = User::findById($userId);
        if (!$targetUser) {
            Response::redirect(base_path('/admin/payments'));
        }

        $restoreExpires = $payment['revoked_prev_expires_at'] ?? null;
        $package = Package::find((int)($payment['package_id'] ?? 0));
        if ($package) {
            $bonusCredits = (int)($package['bonus_credits'] ?? 0);
            if ($bonusCredits > 0) {
                User::addCredits($userId, $bonusCredits);
            }
            $months = (int)($payment['months'] ?? 1);
            if ($months < 1) {
                $months = 1;
            } elseif ($months > 12) {
                $months = 12;
            }
            if ((int)($package['subscription_days'] ?? 0) > 0) {
                if ($restoreExpires) {
                    User::setSubscriptionExpiresAt($userId, (string)$restoreExpires);
                } else {
                    $days = (int)$package['subscription_days'] * $months;
                    User::extendSubscription($userId, $days);
                }
            }
        }

        Payment::cancelRevocation($id);
        Audit::log('payment_refund_cancel', $_SESSION['user_id'] ?? null, ['payment_id' => $id, 'user_id' => $userId]);
        Response::redirect(base_path('/admin/payments'));
    }

    public function proof(Request $request, string $id): void
    {
        $payment = Payment::find((int)$id);
        if (!$payment) {
            http_response_code(404);
            echo 'Comprovante não encontrado.';
            return;
        }

        if (empty($payment['proof_path'])) {
            $configPath = trim((string)config('storage.path', 'storage/uploads'), '/');
            $projectRoot = dirname(__DIR__, 3);
            $paths = [$configPath];
            if (!str_contains($configPath, 'uploads')) {
                $paths[] = rtrim($configPath, '/') . '/uploads';
            }
            $paymentsDirs = [];
            foreach (array_unique($paths) as $path) {
                $paymentsDirs[] = $projectRoot . '/' . $path . '/payments';
                $paymentsDirs[] = $projectRoot . '/app/' . $path . '/payments';
                $paymentsDirs[] = dirname(__DIR__, 2) . '/' . $path . '/payments';
            }
            $paymentsDirs[] = $projectRoot . '/storage/uploads/payments';
            $paymentsDirs[] = $projectRoot . '/storage/payments';
            $paymentsDirs[] = $projectRoot . '/uploads/payments';
            $paymentsDirs[] = $projectRoot . '/app/storage/uploads/payments';
            $paymentsDirs[] = $projectRoot . '/app/storage/payments';
            $found = null;
            foreach ($paymentsDirs as $dir) {
                if (!is_dir($dir)) {
                    continue;
                }
                $matches = glob(rtrim($dir, '/') . '/payment_' . (int)$id . '_*');
                if (!empty($matches)) {
                    $found = $matches[0];
                    break;
                }
            }
            if ($found) {
                Payment::attachProof((int)$id, 'payments/' . basename($found));
                $payment = Payment::find((int)$id) ?? $payment;
            } else {
                http_response_code(404);
                echo 'Comprovante não encontrado.';
                return;
            }
        }
        $clean = str_replace(['..', '\\'], ['', '/'], (string)$payment['proof_path']);
        $configPath = trim((string)config('storage.path', 'storage/uploads'), '/');
        $projectRoot = dirname(__DIR__, 3);
        $projectReal = realpath($projectRoot);
        $candidates = [];
        $cleanTrim = ltrim($clean, '/');
        $altTrim = $cleanTrim;

        if (str_starts_with($altTrim, 'storage/')) {
            $altTrim = substr($altTrim, strlen('storage/'));
        }
        if (str_starts_with($altTrim, 'uploads/')) {
            $altTrim = substr($altTrim, strlen('uploads/'));
        }

        $relatives = array_unique(array_filter([
            $cleanTrim,
            $altTrim,
            $altTrim !== '' && !str_starts_with($altTrim, 'payments/') ? 'payments/' . $altTrim : null,
        ]));

        foreach ($relatives as $rel) {
            $candidates[] = $projectRoot . '/' . $configPath . '/' . $rel;
            $candidates[] = $projectRoot . '/app/' . $configPath . '/' . $rel;
            $candidates[] = dirname(__DIR__, 2) . '/' . $configPath . '/' . $rel;
            if (str_starts_with($rel, 'storage/') || str_starts_with($rel, 'uploads/')) {
                $candidates[] = $projectRoot . '/' . $rel;
                $candidates[] = $projectRoot . '/app/' . $rel;
            }
        }

        $real = null;
        foreach ($candidates as $full) {
            $candidate = realpath($full);
            if ($candidate && $projectReal && str_starts_with($candidate, $projectReal)) {
                $real = $candidate;
                break;
            }
        }
        if (!$real) {
            http_response_code(404);
            echo 'Comprovante não encontrado.';
            return;
        }

        $ext = strtolower(pathinfo($real, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            default => 'image/jpeg',
        };
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . basename($real) . '"');
        readfile($real);
    }
}
