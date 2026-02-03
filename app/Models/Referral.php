<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Referral
{
    public static function countConfirmed(int $referrerId): int
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM users WHERE referrer_id = :id');
        $stmt->execute(['id' => $referrerId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }
}
