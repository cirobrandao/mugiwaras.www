<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class LoginHistory
{
    public static function record(int $userId, string $ip, string $ua): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO login_history (user_id, ip_address, user_agent, logged_at) VALUES (:u,:ip,:ua,NOW())');
        $stmt->execute([
            'u' => $userId,
            'ip' => $ip,
            'ua' => $ua,
        ]);
    }

    public static function forUsers(array $userIds, int $limit = 10): array
    {
        $clean = array_values(array_unique(array_filter(array_map('intval', $userIds), static fn ($v) => $v > 0)));
        if (empty($clean)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($clean), '?'));
        $stmt = Database::connection()->prepare(
            'SELECT user_id, ip_address, user_agent, logged_at
             FROM login_history
             WHERE user_id IN (' . $placeholders . ')
             ORDER BY logged_at DESC'
        );
        $stmt->execute($clean);
        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $row) {
            $uid = (int)($row['user_id'] ?? 0);
            if ($uid <= 0) {
                continue;
            }
            if (!isset($map[$uid])) {
                $map[$uid] = [];
            }
            if (count($map[$uid]) >= $limit) {
                continue;
            }
            $map[$uid][] = $row;
        }
        return $map;
    }

    public static function forUser(int $userId, int $limit = 20): array
    {
        if ($userId <= 0) {
            return [];
        }
        $stmt = Database::connection()->prepare(
            'SELECT ip_address, user_agent, logged_at
             FROM login_history
             WHERE user_id = :u
             ORDER BY logged_at DESC
             LIMIT :l'
        );
        $stmt->bindValue('u', $userId, \PDO::PARAM_INT);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function forUserAll(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }
        $stmt = Database::connection()->prepare(
            'SELECT ip_address, user_agent, logged_at
             FROM login_history
             WHERE user_id = :u
             ORDER BY logged_at DESC'
        );
        $stmt->bindValue('u', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}