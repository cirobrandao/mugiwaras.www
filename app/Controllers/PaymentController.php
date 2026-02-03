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

final class PaymentController extends Controller
{
    public function packages(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $packages = Package::all();
        echo $this->view('payments/packages', [
            'packages' => $packages,
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
            Response::redirect(base_path('/payments?error=package'));
        }
        $pixKey = Setting::get('pix_key', '');
        $pixName = Setting::get('pix_receiver', '');
        echo $this->view('payments/checkout', [
            'package' => $package,
            'csrf' => Csrf::token(),
            'pixKey' => $pixKey,
            'pixName' => $pixName,
        ]);
    }

    public function requestPayment(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/payments'));
        }
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $packageId = (int)($request->post['package_id'] ?? 0);
        if (!Package::find($packageId)) {
            Response::redirect(base_path('/payments?error=package'));
        }
        $paymentId = Payment::create(['uid' => (int)$user['id'], 'pid' => $packageId, 'status' => 'pending']);

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
        Response::redirect(base_path('/payments?requested=1'));
    }

    public function history(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $payments = Payment::byUser((int)$user['id']);
        echo $this->view('payments/history', ['payments' => $payments, 'csrf' => Csrf::token()]);
    }

    public function uploadProof(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/payments/history'));
        }
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $paymentId = (int)($request->post['payment_id'] ?? 0);
        $payment = Payment::find($paymentId);
        if (!$payment || (int)$payment['user_id'] !== (int)$user['id']) {
            Response::redirect(base_path('/payments/history'));
        }
        if (empty($request->files['proof']) || $request->files['proof']['error'] !== UPLOAD_ERR_OK) {
            Response::redirect(base_path('/payments/history?error=proof'));
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
            Response::redirect(base_path('/payments/history?error=move'));
        }

        Payment::attachProof($paymentId, 'payments/' . $safeName);
        Audit::log('payment_proof_upload', (int)$user['id'], ['payment_id' => $paymentId]);
        Response::redirect(base_path('/payments/history?uploaded=1'));
    }

    private function validateProof(array $file): string
    {
        if ((int)$file['size'] > 4 * 1024 * 1024) {
            Response::redirect(base_path('/payments/history?error=size'));
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
            Response::redirect(base_path('/payments/history?error=type'));
        }
        return $ext;
    }
}
