<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class UserBlocklist
{
    public static function isBlocked(int $userId): bool
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM user_blocklist WHERE user_id = :id AND active = 1');
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0) > 0;
    }

    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM user_blocklist ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public static function add(int $userId, string $reason): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO user_blocklist (user_id, reason, active, created_at) VALUES (:u,:r,1,NOW())');
        $stmt->execute(['u' => $userId, 'r' => $reason]);
    }

    public static function deactivate(int $id): void
    {
        $stmt = Database::connection()->prepare('UPDATE user_blocklist SET active = 0 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
