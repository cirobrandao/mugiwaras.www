<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class ContentEvent
{
    public static function log(?int $userId, int $contentId, string $event, ?int $page, string $ip): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO content_events (user_id, content_id, event, page_num, ip_address, created_at) VALUES (:u,:c,:e,:p,:ip,NOW())'
        );
        $stmt->execute([
            'u' => $userId,
            'c' => $contentId,
            'e' => $event,
            'p' => $page,
            'ip' => $ip,
        ]);
    }

    public static function countToday(int $userId, string $event): int
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) AS c FROM content_events WHERE user_id = :u AND event = :e AND DATE(created_at) = CURDATE()'
        );
        $stmt->execute(['u' => $userId, 'e' => $event]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function recentReadsForUser(int $userId, int $limit = 20): array
    {
        if ($userId <= 0) {
            return [];
        }
        $stmt = Database::connection()->prepare(
            'SELECT ce.created_at, ci.id AS content_id, ci.title, s.name AS series_name, c.name AS category_name
             FROM content_events ce
             LEFT JOIN content_items ci ON ci.id = ce.content_id
             LEFT JOIN series s ON s.id = ci.series_id
             LEFT JOIN categories c ON c.id = ci.category_id
             WHERE ce.user_id = :u AND ce.event = :e
             ORDER BY ce.created_at DESC
             LIMIT :l'
        );
        $stmt->bindValue('u', $userId, \PDO::PARAM_INT);
        $stmt->bindValue('e', 'read_open');
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countReadsForUser(int $userId): int
    {
        if ($userId <= 0) {
            return 0;
        }
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) AS c
             FROM content_events
             WHERE user_id = :u AND event = :e'
        );
        $stmt->execute(['u' => $userId, 'e' => 'read_open']);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function pagedReadsForUser(int $userId, int $page, int $perPage): array
    {
        if ($userId <= 0) {
            return [];
        }
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $stmt = Database::connection()->prepare(
            'SELECT ce.created_at, ci.id AS content_id, ci.title, s.name AS series_name, c.name AS category_name
             FROM content_events ce
             LEFT JOIN content_items ci ON ci.id = ce.content_id
             LEFT JOIN series s ON s.id = ci.series_id
             LEFT JOIN categories c ON c.id = ci.category_id
             WHERE ce.user_id = :u AND ce.event = :e
             ORDER BY ce.created_at DESC
             LIMIT :l OFFSET :o'
        );
        $stmt->bindValue('u', $userId, \PDO::PARAM_INT);
        $stmt->bindValue('e', 'read_open');
        $stmt->bindValue('l', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('o', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
