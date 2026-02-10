<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Audit;
use App\Core\Database;
use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
use DateTimeImmutable;
use DateTimeInterface;

final class PaymentRevocationService
{
    public function revokePayment(int $paymentId, int $adminUserId, string $reason, DateTimeInterface|string|int|null $now = null): array
    {
        $db = Database::connection();
        $nowDt = $this->normalizeNow($now);

        try {
            $db->beginTransaction();

            $payment = $this->findPaymentForUpdate($paymentId);
            if (!$payment) {
                error_log('[revoke] payment_not_found id=' . $paymentId);
                $db->rollBack();
                return ['status' => 'not_found'];
            }

            $status = (string)($payment['status'] ?? '');
            if ($status === 'revoked') {
                $db->commit();
                return ['status' => 'already_revoked'];
            }
            if ($status !== 'approved') {
                $db->commit();
                return ['status' => 'invalid_status'];
            }

            $userId = (int)($payment['user_id'] ?? 0);
            $user = $this->findUserForUpdate($userId);
            if (!$user) {
                $db->rollBack();
                return ['status' => 'user_not_found'];
            }

            $prevTier = (string)($user['access_tier'] ?? 'user');
            $prevExpiresAt = $user['subscription_expires_at'] ?? null;
            $prevCredits = (int)($user['credits'] ?? 0);

            $wasLatestApproved = $this->isLatestApprovedPayment($userId, $paymentId);
            $extensionDays = $this->resolveExtensionDays($payment);
            $package = Package::find((int)($payment['package_id'] ?? 0));
            $bonusCredits = (int)($package['bonus_credits'] ?? 0);

            $this->markRevoked($paymentId, $adminUserId, $prevTier, $prevExpiresAt);

            $newCredits = $prevCredits;
            if ($bonusCredits > 0) {
                User::removeCredits($userId, $bonusCredits);
                $newCredits = max(0, $prevCredits - $bonusCredits);
            }

            $newExpiresAt = $prevExpiresAt;
            $newTier = $prevTier;

            if ($wasLatestApproved && $prevTier !== 'vitalicio') {
                $result = self::calculateRevocationOutcome($prevExpiresAt, $extensionDays, $nowDt, $prevTier);
                $newExpiresAt = $result['new_expires_at'];
                $newTier = $result['new_tier'];

                User::setSubscriptionExpiresAt($userId, $newExpiresAt);
                User::setAccessTier($userId, $newTier);
            }

            Audit::log('payment_revoked', $userId, [
                'payment_id' => $paymentId,
                'admin_user_id' => $adminUserId,
                'user_id' => $userId,
                'package_id' => (int)($payment['package_id'] ?? 0),
                'months' => (int)($payment['months'] ?? 0),
                'reason' => $reason,
                'bonus_credits' => $bonusCredits,
                'prev_credits' => $prevCredits,
                'new_credits' => $newCredits,
                'prev_expires_at' => $prevExpiresAt,
                'new_expires_at' => $newExpiresAt,
                'prev_tier' => $prevTier,
                'new_tier' => $newTier,
                'extension_days' => $extensionDays,
                'was_latest_approved' => $wasLatestApproved ? 1 : 0,
                'warning' => $wasLatestApproved ? null : 'Pagamento antigo revogado. Ajuste manual pode ser necessario.',
            ]);

            $db->commit();
            return ['status' => 'revoked', 'was_latest_approved' => $wasLatestApproved];
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return ['status' => 'error'];
        }
    }

    public static function calculateRevocationOutcome(?string $prevExpiresAt, int $extensionDays, DateTimeInterface $now, string $prevTier): array
    {
        if ($prevTier === 'vitalicio') {
            return [
                'new_expires_at' => $prevExpiresAt,
                'new_tier' => $prevTier,
            ];
        }

        if ($extensionDays <= 0) {
            return [
                'new_expires_at' => $prevExpiresAt,
                'new_tier' => $prevTier,
            ];
        }

        if (empty($prevExpiresAt)) {
            return [
                'new_expires_at' => null,
                'new_tier' => 'user',
            ];
        }

        try {
            $prevDt = new DateTimeImmutable((string)$prevExpiresAt);
        } catch (\Throwable $e) {
            return [
                'new_expires_at' => null,
                'new_tier' => 'user',
            ];
        }
        $newDt = $prevDt->modify('-' . $extensionDays . ' days');
        if ($newDt <= $now) {
            return [
                'new_expires_at' => null,
                'new_tier' => 'user',
            ];
        }

        return [
            'new_expires_at' => $newDt->format('Y-m-d H:i:s'),
            'new_tier' => 'assinante',
        ];
    }

    private function resolveExtensionDays(array $payment): int
    {
        $paymentId = (int)($payment['id'] ?? 0);
        $fromAudit = $this->extensionDaysFromAudit($paymentId);
        if ($fromAudit > 0) {
            return $fromAudit;
        }

        $package = Package::find((int)($payment['package_id'] ?? 0));
        $cycleDays = (int)($package['subscription_days'] ?? 0);
        if ($cycleDays <= 0) {
            $cycleDays = 30;
        }
        $months = (int)($payment['months'] ?? 1);
        $months = max(1, $months);
        return $cycleDays * $months;
    }

    private function extensionDaysFromAudit(int $paymentId): int
    {
        $stmt = Database::connection()->prepare("SELECT JSON_EXTRACT(meta, '$.extension_days') AS extension_days FROM audit_log WHERE event = 'subscription_applied' AND JSON_EXTRACT(meta, '$.payment_id') = :pid ORDER BY id DESC LIMIT 1");
        $stmt->execute(['pid' => $paymentId]);
        $row = $stmt->fetch();
        if (!$row) {
            return 0;
        }
        $value = $row['extension_days'] ?? null;
        if ($value === null) {
            return 0;
        }
        return (int)$value;
    }

    private function isLatestApprovedPayment(int $userId, int $paymentId): bool
    {
        $stmt = Database::connection()->prepare("SELECT id FROM payments WHERE user_id = :uid AND status = 'approved' ORDER BY id DESC LIMIT 1");
        $stmt->execute(['uid' => $userId]);
        $row = $stmt->fetch();
        return (int)($row['id'] ?? 0) === $paymentId;
    }

    private function markRevoked(int $paymentId, int $adminUserId, string $prevTier, ?string $prevExpiresAt): void
    {
        $stmt = Database::connection()->prepare('UPDATE payments SET status = :s, revoked_at = NOW(), revoked_by = :rb, revoked_prev_tier = :pt, revoked_prev_expires_at = :pe, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            's' => 'revoked',
            'rb' => $adminUserId,
            'pt' => $prevTier,
            'pe' => $prevExpiresAt,
            'id' => $paymentId,
        ]);
    }

    private function findPaymentForUpdate(int $paymentId): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM payments WHERE id = :id FOR UPDATE');
        $stmt->execute(['id' => $paymentId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function findUserForUpdate(int $userId): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE id = :id FOR UPDATE');
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function normalizeNow(DateTimeInterface|string|int|null $now): DateTimeImmutable
    {
        if ($now instanceof DateTimeInterface) {
            return new DateTimeImmutable($now->format('Y-m-d H:i:s'));
        }
        if (is_int($now)) {
            return (new DateTimeImmutable())->setTimestamp($now);
        }
        if (is_string($now) && $now !== '') {
            return new DateTimeImmutable($now);
        }
        return new DateTimeImmutable('now');
    }
}
