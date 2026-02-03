<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class UserToken
{
    public static function create(int $userId, string $token): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO user_tokens (user_id, token_hash, created_at) VALUES (:uid, :th, NOW())');
        $stmt->execute(['uid' => $userId, 'th' => hash('sha256', $token)]);
    }

    public static function validate(string $token): ?int
    {
        $stmt = Database::connection()->prepare('SELECT user_id FROM user_tokens WHERE token_hash = :th');
        $stmt->execute(['th' => hash('sha256', $token)]);
        $row = $stmt->fetch();
        return $row ? (int)$row['user_id'] : null;
    }

    public static function rotate(int $userId, string $oldToken): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM user_tokens WHERE token_hash = :th');
        $stmt->execute(['th' => hash('sha256', $oldToken)]);
        $new = bin2hex(random_bytes(32));
        self::create($userId, $new);
        setcookie('remember_me', $new, [
            'expires' => time() + (int)config('security.remember_days', 30) * 86400,
            'path' => base_path('/'),
            'secure' => (bool)config('security.session_secure', true),
            'httponly' => true,
            'samesite' => (string)config('security.session_samesite', 'Lax'),
        ]);
    }

    public static function revoke(string $token): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM user_tokens WHERE token_hash = :th');
        $stmt->execute(['th' => hash('sha256', $token)]);
    }
}
