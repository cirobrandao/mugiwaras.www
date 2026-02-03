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
}
