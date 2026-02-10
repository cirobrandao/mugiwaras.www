<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Services\PaymentRevocationService;
use DateTimeImmutable;

function assertSameValue($expected, $actual, string $label): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($label . ' failed. Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

$now = new DateTimeImmutable('2026-02-09 10:00:00');

// Latest approved: reduce expiration.
$prevExpires = '2026-04-10 10:00:00';
$outcome = PaymentRevocationService::calculateRevocationOutcome($prevExpires, 30, $now, 'assinante');
assertSameValue('2026-03-11 10:00:00', $outcome['new_expires_at'], 'latest_reduce_expires');
assertSameValue('assinante', $outcome['new_tier'], 'latest_reduce_tier');

// Latest approved: new expires <= now.
$prevExpires = '2026-02-15 10:00:00';
$outcome = PaymentRevocationService::calculateRevocationOutcome($prevExpires, 30, $now, 'assinante');
assertSameValue(null, $outcome['new_expires_at'], 'latest_expired_expires');
assertSameValue('user', $outcome['new_tier'], 'latest_expired_tier');

// Vitalicio is preserved.
$prevExpires = '2030-01-01 00:00:00';
$outcome = PaymentRevocationService::calculateRevocationOutcome($prevExpires, 365, $now, 'vitalicio');
assertSameValue('2030-01-01 00:00:00', $outcome['new_expires_at'], 'vitalicio_expires');
assertSameValue('vitalicio', $outcome['new_tier'], 'vitalicio_tier');

// No previous expiration.
$outcome = PaymentRevocationService::calculateRevocationOutcome(null, 30, $now, 'assinante');
assertSameValue(null, $outcome['new_expires_at'], 'no_prev_expires');
assertSameValue('user', $outcome['new_tier'], 'no_prev_tier');

echo "All payment revocation tests passed.\n";
