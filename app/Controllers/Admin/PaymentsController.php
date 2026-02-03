<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Core\Audit;
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
        if ($package) {
            if ((int)$package['bonus_credits'] > 0) {
                User::addCredits((int)$payment['user_id'], (int)$package['bonus_credits']);
            }
            if ((int)$package['subscription_days'] > 0) {
                User::extendSubscription((int)$payment['user_id'], (int)$package['subscription_days']);
            }
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

    public function proof(Request $request, string $id): void
    {
        $payment = Payment::find((int)$id);
        if (!$payment || empty($payment['proof_path'])) {
            http_response_code(404);
            echo 'Comprovante não encontrado.';
            return;
        }
        $root = dirname(__DIR__, 3) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
        $clean = str_replace(['..', '\\'], ['', '/'], (string)$payment['proof_path']);
        $full = rtrim($root, '/') . '/' . ltrim($clean, '/');
        $real = realpath($full);
        $rootReal = realpath($root);
        if (!$real || ($rootReal && !str_starts_with($real, $rootReal))) {
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
