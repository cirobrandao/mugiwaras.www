<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class UserToken
{
    private static function hashToken(string $token, string $fingerprint = ''): string
    {
        $pepper = (string)config('app.key', '');
        return hash('sha256', $token . '|' . $fingerprint . '|' . $pepper);
    }

    public static function create(int $userId, string $token, string $fingerprint = ''): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO user_tokens (user_id, token_hash, created_at) VALUES (:uid, :th, NOW())');
        $stmt->execute(['uid' => $userId, 'th' => self::hashToken($token, $fingerprint)]);
    }

    public static function validate(string $token, string $fingerprint = ''): ?int
    {
        $stmt = Database::connection()->prepare('SELECT user_id FROM user_tokens WHERE token_hash = :th1 OR token_hash = :th2');
        $stmt->execute([
            'th1' => self::hashToken($token, $fingerprint),
            'th2' => hash('sha256', $token),
        ]);
        $row = $stmt->fetch();
        return $row ? (int)$row['user_id'] : null;
    }

    public static function rotate(int $userId, string $oldToken, string $fingerprint = ''): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM user_tokens WHERE token_hash = :th1 OR token_hash = :th2');
        $stmt->execute([
            'th1' => self::hashToken($oldToken, $fingerprint),
            'th2' => hash('sha256', $oldToken),
        ]);
        $new = bin2hex(random_bytes(32));
        self::create($userId, $new, $fingerprint);
        setcookie('remember_me', $new, [
            'expires' => time() + (int)config('security.remember_days', 30) * 86400,
            'path' => base_path('/'),
            'secure' => (bool)config('security.session_secure', true),
            'httponly' => true,
            'samesite' => (string)config('security.session_samesite', 'Lax'),
        ]);
    }

    public static function revoke(string $token, string $fingerprint = ''): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM user_tokens WHERE token_hash IN (:th1, :th2, :th3)');
        $stmt->execute([
            'th1' => self::hashToken($token, $fingerprint),
            'th2' => self::hashToken($token),
            'th3' => hash('sha256', $token),
        ]);
    }
}
