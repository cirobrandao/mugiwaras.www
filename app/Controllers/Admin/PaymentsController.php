<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Core\Audit;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Payment;
use App\Models\Package;
use App\Models\User;
use App\Services\SubscriptionPricingService;
use App\Services\PaymentRevocationService;

final class PaymentsController extends Controller
{
    public function index(): void
    {
        $payments = Payment::all();
        $userIds = array_values(array_unique(array_map(static fn ($p) => (int)($p['user_id'] ?? 0), $payments)));
        $userIds = array_values(array_filter($userIds, static fn ($id) => $id > 0));
        $historyRows = Payment::byUsers($userIds);
        $historyByUser = [];
        foreach ($historyRows as $row) {
            $uid = (int)($row['user_id'] ?? 0);
            if ($uid <= 0) {
                continue;
            }
            if (!isset($historyByUser[$uid])) {
                $historyByUser[$uid] = [];
            }
            $historyByUser[$uid][] = $row;
        }
        echo $this->view('admin/payments', [
            'payments' => $payments,
            'historyByUser' => $historyByUser,
            'csrf' => Csrf::token(),
            'currentUser' => Auth::user(),
        ]);
    }

    public function details(Request $request, string $id): void
    {
        $payment = Payment::findDetails((int)$id);
        if (!$payment) {
            $this->respondJson(['ok' => false, 'message' => 'Pagamento nao encontrado.'], 404);
            return;
        }
        $proofPath = (string)($payment['proof_path'] ?? '');
        $payment['proof_url'] = $proofPath !== '' ? base_path('/admin/payments/proof/' . (int)$payment['id']) : null;
        $this->respondJson(['ok' => true, 'payment' => $payment]);
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

        Payment::setStatus($id, 'approved');
        $package = Package::find((int)$payment['package_id']);
        if ($package && (int)($package['bonus_credits'] ?? 0) > 0) {
            User::addCredits((int)$payment['user_id'], (int)$package['bonus_credits']);
        }
        $pricing = new SubscriptionPricingService();
        $pricing->applyApprovedPayment($id, 'now');
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

    public function revoke(Request $request, ?string $id = null): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            $this->respondJson(['ok' => false, 'message' => 'CSRF invalido.'], 400);
            return;
        }
        $rawUri = (string)($_SERVER['REQUEST_URI'] ?? '');
        $rawMethod = (string)($_SERVER['REQUEST_METHOD'] ?? '');
        $rawId = $id ?? ($request->post['id'] ?? null);
        $rawIdStr = is_scalar($rawId) ? (string)$rawId : '';
        $normalizedId = (int)preg_replace('/\D+/', '', $rawIdStr);
        $paymentId = $normalizedId > 0 ? $normalizedId : (int)($request->post['id'] ?? 0);
        $reason = trim((string)($request->post['reason'] ?? ''));
        $adminId = (int)($_SESSION['user_id'] ?? 0);
        error_log('[revoke] method=' . $rawMethod . ' uri=' . $rawUri);
        error_log('[revoke] id_raw=' . $rawIdStr . ' id_normalized=' . $paymentId . ' admin=' . $adminId . ' reason=' . $reason);
        error_log('[revoke] get=' . json_encode($_GET ?? []));
        error_log('[revoke] post=' . json_encode($_POST ?? []));

        if ($paymentId <= 0) {
            $this->respondJson([
                'ok' => false,
                'message' => 'ID invalido no estorno.',
                'result' => [
                    'status' => 'invalid_id',
                    'payment_id' => $paymentId,
                    'raw_id' => $rawIdStr,
                    'uri' => $rawUri,
                ],
            ], 400);
            return;
        }

        $db = Database::connection();
        $info = $db->query('SELECT DATABASE() AS db, @@hostname AS host, @@port AS port')->fetch();

        $q1 = $db->prepare('SELECT id, user_id, package_id, status, created_at FROM payments WHERE id = :id LIMIT 1');
        $q1->execute(['id' => $paymentId]);
        $existsRow = $q1->fetch();

        $q2 = $db->prepare('SELECT COUNT(*) AS c FROM payments WHERE id = :id');
        $q2->execute(['id' => $paymentId]);
        $countRow = $q2->fetch();
        $count = (int)($countRow['c'] ?? 0);

        error_log('[revoke] exists_count=' . $count . ' exists_row=' . json_encode($existsRow));

        if ($count === 0) {
            $this->respondJson([
                'ok' => false,
                'message' => 'Pagamento nao encontrado.',
                'result' => [
                    'status' => 'not_found',
                    'payment_id' => $paymentId,
                    'raw_id' => $rawIdStr,
                    'db' => $info['db'] ?? null,
                    'host' => $info['host'] ?? null,
                    'port' => $info['port'] ?? null,
                    'hint' => 'Verifique se admin e cliente apontam pro mesmo banco',
                ],
            ], 404);
            return;
        }
        $service = new PaymentRevocationService();
        $result = $service->revokePayment($paymentId, $adminId, $reason, 'now');
        Audit::log('payment_refund', $adminId > 0 ? $adminId : null, ['payment_id' => $paymentId, 'result' => $result]);
        if (($result['status'] ?? '') === 'revoked' || ($result['status'] ?? '') === 'already_revoked') {
            $this->respondJson(['ok' => true, 'result' => $result]);
            return;
        }
        if (($result['status'] ?? '') === 'not_found') {
            error_log('[revoke] not_found id=' . $paymentId);
        }
        $this->respondJson(['ok' => false, 'message' => 'Nao foi possivel estornar.', 'result' => $result], 400);
    }

    private function respondJson(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
    }

    public function cancelRevocation(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/payments'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $payment = Payment::find($id);
        $revokedAt = (string)($payment['revoked_at'] ?? '');
        $isRevoked = $revokedAt !== '' && $revokedAt !== '0000-00-00 00:00:00' && $revokedAt !== '0000-00-00';
        if (!$payment || $payment['status'] !== 'revoked' || !$isRevoked) {
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
            echo 'Comprovante nÃ£o encontrado.';
            return;
        }

        $proofPath = (string)($payment['proof_path'] ?? '');
        $projectRoot = dirname(__DIR__, 3);
        $projectReal = realpath($projectRoot);
        $configRaw = str_replace('\\', '/', (string)config('storage.path', 'storage/uploads'));
        $configIsAbsolute = $this->isAbsolutePath($configRaw);
        $configPath = $configIsAbsolute ? rtrim($configRaw, '/') : trim($configRaw, '/');
        $configReal = $configIsAbsolute ? realpath($configPath) : realpath($projectRoot . '/' . $configPath);

        $real = null;
        if ($proofPath !== '' && $this->isAbsolutePath($proofPath)) {
            $absolute = realpath($proofPath);
            if ($absolute) {
                $allowProject = $projectReal && str_starts_with($absolute, $projectReal);
                $allowStorage = $configReal && str_starts_with($absolute, $configReal);
                if ($allowProject || $allowStorage) {
                    $real = $absolute;
                }
            }
        }

        if (!$real && empty($payment['proof_path'])) {
            $found = $this->findProofById((int)$id, $configPath, $configIsAbsolute, $projectRoot);
            if ($found) {
                Payment::attachProof((int)$id, 'payments/' . basename($found));
                $payment = Payment::find((int)$id) ?? $payment;
            } else {
                http_response_code(404);
                echo 'Comprovante nÃ£o encontrado.';
                return;
            }
        }

        if (!$real) {
            $clean = str_replace(['..', '\\'], ['', '/'], (string)($payment['proof_path'] ?? ''));
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
                if ($configIsAbsolute) {
                    $candidates[] = $configPath . '/' . $rel;
                    if (!str_contains($configPath, 'uploads')) {
                        $candidates[] = rtrim($configPath, '/') . '/uploads/' . $rel;
                    }
                } else {
                    $candidates[] = $projectRoot . '/' . $configPath . '/' . $rel;
                    $candidates[] = $projectRoot . '/app/' . $configPath . '/' . $rel;
                    $candidates[] = dirname(__DIR__, 2) . '/' . $configPath . '/' . $rel;
                    if (str_starts_with($rel, 'storage/') || str_starts_with($rel, 'uploads/')) {
                        $candidates[] = $projectRoot . '/' . $rel;
                        $candidates[] = $projectRoot . '/app/' . $rel;
                    }
                }
            }

            foreach ($candidates as $full) {
                $candidate = realpath($full);
                if (!$candidate) {
                    continue;
                }
                $allowProject = $projectReal && str_starts_with($candidate, $projectReal);
                $allowStorage = $configReal && str_starts_with($candidate, $configReal);
                if ($allowProject || $allowStorage) {
                    $real = $candidate;
                    break;
                }
            }

            if (!$real) {
                $found = $this->findProofById((int)$id, $configPath, $configIsAbsolute, $projectRoot);
                if ($found) {
                    Payment::attachProof((int)$id, 'payments/' . basename($found));
                    $real = realpath($found) ?: null;
                }
            }
        }

        if (!$real) {
            http_response_code(404);
            echo 'Comprovante nÃ£o encontrado.';
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

    private function isAbsolutePath(string $path): bool
    {
        if ($path === '') {
            return false;
        }
        if (str_starts_with($path, '/')) {
            return true;
        }
        return (bool)preg_match('/^[A-Za-z]:[\\/\\\\]/', $path);
    }

    private function findProofById(int $id, string $configPath, bool $configIsAbsolute, string $projectRoot): ?string
    {
        $paths = [$configPath];
        if (!str_contains($configPath, 'uploads')) {
            $paths[] = rtrim($configPath, '/') . '/uploads';
        }
        $paymentsDirs = [];
        foreach (array_unique($paths) as $path) {
            if ($configIsAbsolute) {
                $paymentsDirs[] = rtrim($path, '/') . '/payments';
                continue;
            }
            $paymentsDirs[] = $projectRoot . '/' . $path . '/payments';
            $paymentsDirs[] = $projectRoot . '/app/' . $path . '/payments';
            $paymentsDirs[] = dirname(__DIR__, 2) . '/' . $path . '/payments';
        }
        $paymentsDirs[] = $projectRoot . '/storage/uploads/payments';
        $paymentsDirs[] = $projectRoot . '/storage/payments';
        $paymentsDirs[] = $projectRoot . '/uploads/payments';
        $paymentsDirs[] = $projectRoot . '/app/storage/uploads/payments';
        $paymentsDirs[] = $projectRoot . '/app/storage/payments';
        foreach ($paymentsDirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }
            $matches = glob(rtrim($dir, '/') . '/payment_' . $id . '_*');
            if (!empty($matches)) {
                return $matches[0];
            }
        }
        return null;
    }
}
