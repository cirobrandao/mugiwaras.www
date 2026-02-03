<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class PasswordReset
{
    public static function create(int $userId, string $token, int $ttlMinutes = 60): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO password_resets (user_id, token_hash, expires_at, created_at) VALUES (:uid,:th,DATE_ADD(NOW(), INTERVAL :ttl MINUTE),NOW())'
        );
        $stmt->execute([
            'uid' => $userId,
            'th' => hash('sha256', $token),
            'ttl' => $ttlMinutes,
        ]);
    }

    public static function validate(string $token): ?int
    {
        $stmt = Database::connection()->prepare(
            'SELECT user_id FROM password_resets WHERE token_hash = :th AND used_at IS NULL AND expires_at > NOW()'
        );
        $stmt->execute(['th' => hash('sha256', $token)]);
        $row = $stmt->fetch();
        return $row ? (int)$row['user_id'] : null;
    }

    public static function consume(string $token): void
    {
        $stmt = Database::connection()->prepare('UPDATE password_resets SET used_at = NOW() WHERE token_hash = :th');
        $stmt->execute(['th' => hash('sha256', $token)]);
    }
}
