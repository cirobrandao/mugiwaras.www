<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class SeriesSearchLog
{
    public static function createMany(int $userId, string $term, array $seriesIds): void
    {
        if (empty($seriesIds)) {
            return;
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO series_search_logs (series_id,user_id,term,created_at) VALUES (:sid,:uid,:term,NOW())');
        foreach ($seriesIds as $sid) {
            $stmt->execute([
                'sid' => (int)$sid,
                'uid' => $userId > 0 ? $userId : null,
                'term' => $term,
            ]);
        }
    }
}
