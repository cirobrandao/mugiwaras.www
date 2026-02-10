<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Services\SubscriptionPricingService;

function assertSameValue($expected, $actual, string $label): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($label . ' failed. Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

$service = new SubscriptionPricingService();

// User without subscription buys package.
$newPackage = ['id' => 2, 'price' => '10.00', 'subscription_days' => 30];
$quote = SubscriptionPricingService::calculateQuote([], 0, $newPackage, 2);
assertSameValue(2000, $quote['amount_to_charge_cents'], 'purchase_without_subscription_total');
assertSameValue(2000, $quote['new_term_cost_cents'], 'purchase_without_subscription_term');

// Renewal same package should not discount.
$currentPackage = ['id' => 2, 'price' => '10.00', 'subscription_days' => 30];
$quote = SubscriptionPricingService::calculateQuote($currentPackage, 15, $newPackage, 1);
assertSameValue(1000, $quote['amount_to_charge_cents'], 'renew_same_package_total');

// Upgrade with remaining_days 90.
$currentPackage = ['id' => 1, 'price' => '10.00', 'subscription_days' => 30];
$newPackage = ['id' => 2, 'price' => '20.00', 'subscription_days' => 30];
$quote = SubscriptionPricingService::calculateQuote($currentPackage, 90, $newPackage, 1);
assertSameValue(5000, $quote['amount_to_charge_cents'], 'upgrade_total');
assertSameValue(3000, $quote['upgrade_diff_cents'], 'upgrade_diff');
assertSameValue(2000, $quote['new_term_cost_cents'], 'upgrade_term');
assertSameValue(3000, $quote['credit_current_remaining_cents'], 'upgrade_credit');
assertSameValue(6000, $quote['cost_new_remaining_cents'], 'upgrade_cost_remaining');

// Downgrade with active subscription should error.
$thrown = false;
try {
    SubscriptionPricingService::calculateQuote(['id' => 3, 'price' => '20.00', 'subscription_days' => 30], 5, ['id' => 4, 'price' => '10.00', 'subscription_days' => 30], 1);
} catch (RuntimeException $e) {
    $thrown = true;
}
assertSameValue(true, $thrown, 'downgrade_blocked');

// Expired subscription allows downgrade.
$quote = SubscriptionPricingService::calculateQuote(['id' => 3, 'price' => '20.00', 'subscription_days' => 30], 0, ['id' => 4, 'price' => '10.00', 'subscription_days' => 30], 1);
assertSameValue(1000, $quote['amount_to_charge_cents'], 'downgrade_expired_allowed');

// Idempotency simulation for applyApprovedPayment (audit-driven).
$applied = [];
$applyOnce = function (int $paymentId, DateTimeImmutable $base, int $extensionDays) use (&$applied): DateTimeImmutable {
    if (isset($applied[$paymentId])) {
        return $base;
    }
    $applied[$paymentId] = true;
    return $base->modify('+' . $extensionDays . ' days');
};

$base = new DateTimeImmutable('2025-01-01 00:00:00');
$first = $applyOnce(10, $base, 30);
$second = $applyOnce(10, $first, 30);
assertSameValue($first->format('Y-m-d H:i:s'), $second->format('Y-m-d H:i:s'), 'idempotency_apply_once');

echo "All subscription pricing tests passed.\n";
