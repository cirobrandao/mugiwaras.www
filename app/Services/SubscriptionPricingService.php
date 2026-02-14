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
use RuntimeException;

final class SubscriptionPricingService
{
    public function getActiveSubscription(int $userId, DateTimeInterface|string|int|null $now = null): array
    {
        $user = User::findById($userId) ?? [];
        $expiresAt = $user['subscription_expires_at'] ?? null;
        $remainingDays = 0;
        $nowTs = $this->normalizeNow($now)->getTimestamp();

        if (!empty($expiresAt)) {
            $expiresTs = strtotime((string)$expiresAt);
            if ($expiresTs !== false) {
                $remainingDays = max(0, (int)ceil(($expiresTs - $nowTs) / 86400));
            }
        }

        $currentPayment = Payment::latestApprovedByUser($userId);
        $currentPackage = null;
        if ($currentPayment) {
            $currentPackage = Package::find((int)($currentPayment['package_id'] ?? 0));
        }

        return [
            'expires_at' => $expiresAt,
            'remaining_days' => $remainingDays,
            'current_package' => $currentPackage,
        ];
    }

    public function quotePurchase(int $userId, int $newPackageId, int $months = 1, DateTimeInterface|string|int|null $now = null): array
    {
        $months = max(1, min(12, $months));
        $newPackage = Package::find($newPackageId);
        if (!$newPackage) {
            throw new RuntimeException('Pacote invalido');
        }

        $active = $this->getActiveSubscription($userId, $now);
        $remainingDays = (int)($active['remaining_days'] ?? 0);
        $currentPackage = $active['current_package'] ?? null;
        $currentPackageId = $currentPackage ? (int)($currentPackage['id'] ?? 0) : null;

        $newPriceCents = self::toCents($newPackage['price'] ?? '0');
        $currentPriceCents = $currentPackage ? self::toCents($currentPackage['price'] ?? '0') : 0;

        $cycleDaysCurrent = 0;
        $cycleDaysNew = (int)($newPackage['subscription_days'] ?? 0);
        if ($cycleDaysNew <= 0) {
            $cycleDaysNew = 30;
        }

        $upgradeDiffCents = 0;
        $newTermCostCents = $newPriceCents * $months;
        $creditCurrentRemainingCents = 0;
        $costNewRemainingCents = 0;

        if ($remainingDays > 0 && $currentPackage) {
            $cycleDaysCurrent = (int)($currentPackage['subscription_days'] ?? 0);
            if ($cycleDaysCurrent <= 0) {
                $cycleDaysCurrent = 30;
            }

            if ($newPriceCents < $currentPriceCents) {
                throw new RuntimeException('Downgrade so apos vencimento');
            }

            $samePackage = $currentPackageId !== null && $currentPackageId === (int)$newPackageId;
            if ($newPriceCents > $currentPriceCents && !$samePackage) {
                $creditCurrentRemainingCents = self::ceilDiv($currentPriceCents * $remainingDays, $cycleDaysCurrent);
                $costNewRemainingCents = self::ceilDiv($newPriceCents * $remainingDays, $cycleDaysNew);
                $upgradeDiffCents = max(0, $costNewRemainingCents - $creditCurrentRemainingCents);
            }
        }

        $amountToChargeCents = $upgradeDiffCents + $newTermCostCents;

        return [
            'amount_to_charge_cents' => $amountToChargeCents,
            'remaining_days' => $remainingDays,
            'current_package_id' => $currentPackageId,
            'new_package_id' => (int)$newPackageId,
            'upgrade_diff_cents' => $upgradeDiffCents,
            'new_term_cost_cents' => $newTermCostCents,
            'credit_current_remaining_cents' => $creditCurrentRemainingCents,
            'cost_new_remaining_cents' => $costNewRemainingCents,
            'cycle_days_current' => $cycleDaysCurrent > 0 ? $cycleDaysCurrent : null,
            'cycle_days_new' => $cycleDaysNew,
        ];
    }

    public function applyApprovedPayment(int $paymentId, DateTimeInterface|string|int|null $now = null): void
    {
        $payment = Payment::find($paymentId);
        if (!$payment || ($payment['status'] ?? '') !== 'approved') {
            return;
        }

        if ($this->alreadyApplied($paymentId)) {
            return;
        }

        $userId = (int)($payment['user_id'] ?? 0);
        $packageId = (int)($payment['package_id'] ?? 0);
        $user = User::findById($userId);
        $package = Package::find($packageId);
        if (!$user || !$package) {
            return;
        }

        $nowDt = $this->normalizeNow($now);
        $prevExpiresAt = $user['subscription_expires_at'] ?? null;
        $base = $nowDt;
        if (!empty($prevExpiresAt)) {
            $prevDt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', (string)$prevExpiresAt) ?: new DateTimeImmutable((string)$prevExpiresAt);
            if ($prevDt > $nowDt) {
                $base = $prevDt;
            }
        }

        $voucherDays = (int)($payment['voucher_days'] ?? 0);
        $extensionDays = 0;
        
        if ($voucherDays > 0) {
            $extensionDays = $voucherDays;
        } else {
            $months = (int)($payment['months'] ?? 1);
            $months = max(1, $months);
            $cycleDays = (int)($package['subscription_days'] ?? 0);
            if ($cycleDays <= 0) {
                $cycleDays = 30;
            }
            $extensionDays = $cycleDays * $months;
        }
        
        $newExpiresAt = $base->modify('+' . $extensionDays . ' days');

        User::setSubscriptionExpiresAt($userId, $newExpiresAt->format('Y-m-d H:i:s'));
        if ($extensionDays > 0 && ($user['access_tier'] ?? '') !== 'vitalicio') {
            User::setAccessTier($userId, 'assinante');
        }

        Audit::log('subscription_applied', $userId, [
            'payment_id' => $paymentId,
            'user_id' => $userId,
            'package_id' => $packageId,
            'months' => $months,
            'extension_days' => $extensionDays,
            'prev_expires_at' => $prevExpiresAt,
            'new_expires_at' => $newExpiresAt->format('Y-m-d H:i:s'),
        ]);
    }

    public static function calculateQuote(array $currentPackage, int $remainingDays, array $newPackage, int $months = 1): array
    {
        $current = $currentPackage ?: null;
        $remainingDays = max(0, $remainingDays);
        $months = max(1, min(12, $months));

        $currentPriceCents = $current ? self::toCents($current['price'] ?? '0') : 0;
        $newPriceCents = self::toCents($newPackage['price'] ?? '0');

        $cycleDaysCurrent = 0;
        $cycleDaysNew = (int)($newPackage['subscription_days'] ?? 0);
        if ($cycleDaysNew <= 0) {
            $cycleDaysNew = 30;
        }

        $upgradeDiffCents = 0;
        $newTermCostCents = $newPriceCents * $months;
        $creditCurrentRemainingCents = 0;
        $costNewRemainingCents = 0;

        if ($remainingDays > 0 && $current) {
            $cycleDaysCurrent = (int)($current['subscription_days'] ?? 0);
            if ($cycleDaysCurrent <= 0) {
                $cycleDaysCurrent = 30;
            }
            if ($newPriceCents < $currentPriceCents) {
                throw new RuntimeException('Downgrade so apos vencimento');
            }
            $samePackage = (int)($current['id'] ?? 0) === (int)($newPackage['id'] ?? 0);
            if ($newPriceCents > $currentPriceCents && !$samePackage) {
                $creditCurrentRemainingCents = self::ceilDiv($currentPriceCents * $remainingDays, $cycleDaysCurrent);
                $costNewRemainingCents = self::ceilDiv($newPriceCents * $remainingDays, $cycleDaysNew);
                $upgradeDiffCents = max(0, $costNewRemainingCents - $creditCurrentRemainingCents);
            }
        }

        $amountToChargeCents = $upgradeDiffCents + $newTermCostCents;

        return [
            'amount_to_charge_cents' => $amountToChargeCents,
            'remaining_days' => $remainingDays,
            'current_package_id' => $current ? (int)($current['id'] ?? 0) : null,
            'new_package_id' => (int)($newPackage['id'] ?? 0),
            'upgrade_diff_cents' => $upgradeDiffCents,
            'new_term_cost_cents' => $newTermCostCents,
            'credit_current_remaining_cents' => $creditCurrentRemainingCents,
            'cost_new_remaining_cents' => $costNewRemainingCents,
            'cycle_days_current' => $cycleDaysCurrent > 0 ? $cycleDaysCurrent : null,
            'cycle_days_new' => $cycleDaysNew,
        ];
    }

    public static function toCents(string|int|float $value): int
    {
        $raw = trim((string)$value);
        if ($raw === '') {
            return 0;
        }
        $raw = str_replace(',', '.', $raw);
        if (!str_contains($raw, '.')) {
            return (int)$raw * 100;
        }
        [$whole, $frac] = explode('.', $raw, 2);
        $whole = $whole === '' ? '0' : $whole;
        $frac = substr($frac . '00', 0, 2);
        return ((int)$whole * 100) + (int)$frac;
    }

    public static function ceilDiv(int $a, int $b): int
    {
        if ($b <= 0) {
            return 0;
        }
        return (int)(($a + $b - 1) / $b);
    }

    private function alreadyApplied(int $paymentId): bool
    {
        $stmt = Database::connection()->prepare("SELECT id FROM audit_log WHERE event = 'subscription_applied' AND JSON_EXTRACT(meta, '$.payment_id') = :pid LIMIT 1");
        $stmt->execute(['pid' => $paymentId]);
        return (bool)$stmt->fetch();
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
