<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class AuditLog
{
    public static function create(string $event, ?int $userId, array $meta): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO audit_log (event, user_id, meta, created_at) VALUES (:e,:u,:m,NOW())');
        $stmt->execute([
            'e' => $event,
            'u' => $userId,
            'm' => json_encode($meta),
        ]);
    }

    public static function loginFailsForUsername(string $username, int $limit = 10): array
    {
        $username = trim($username);
        if ($username === '' || $limit <= 0) {
            return [];
        }
        $pattern = '%"username":' . json_encode($username) . '%';
        $stmt = Database::connection()->prepare(
            'SELECT meta, created_at
             FROM audit_log
             WHERE event = :event AND meta LIKE :pattern
             ORDER BY created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue('event', 'login_fail');
        $stmt->bindValue('pattern', $pattern);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $results = [];
        foreach ($rows as $row) {
            $meta = json_decode((string)($row['meta'] ?? ''), true);
            if (!is_array($meta) || ($meta['username'] ?? '') !== $username) {
                continue;
            }
            $results[] = [
                'ip' => (string)($meta['ip'] ?? ''),
                'created_at' => (string)($row['created_at'] ?? ''),
            ];
        }
        return $results;
    }

    public static function recentLoginFails(int $limit = 10): array
    {
        $limit = max(1, min(100, $limit));
        $stmt = Database::connection()->prepare(
            'SELECT meta, created_at
             FROM audit_log
             WHERE event = :event
             ORDER BY created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue('event', 'login_fail');
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $results = [];
        foreach ($rows as $row) {
            $meta = json_decode((string)($row['meta'] ?? ''), true);
            if (!is_array($meta)) {
                continue;
            }
            $results[] = [
                'username' => (string)($meta['username'] ?? ''),
                'ip' => (string)($meta['ip'] ?? ''),
                'created_at' => (string)($row['created_at'] ?? ''),
            ];
        }
        return $results;
    }
}
