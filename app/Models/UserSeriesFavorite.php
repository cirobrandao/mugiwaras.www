<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class UserSeriesFavorite
{
    public static function add(int $userId, int $seriesId): void
    {
        $stmt = Database::connection()->prepare('INSERT IGNORE INTO user_series_favorites (user_id, series_id, created_at) VALUES (:u, :s, NOW())');
        $stmt->execute(['u' => $userId, 's' => $seriesId]);
    }

    public static function remove(int $userId, int $seriesId): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM user_series_favorites WHERE user_id = :u AND series_id = :s');
        $stmt->execute(['u' => $userId, 's' => $seriesId]);
    }

    public static function getIdsForUser(int $userId, array $seriesIds): array
    {
        if (empty($seriesIds)) {
            return [];
        }
        $in = implode(',', array_fill(0, count($seriesIds), '?'));
        $stmt = Database::connection()->prepare('SELECT series_id FROM user_series_favorites WHERE user_id = ? AND series_id IN (' . $in . ')');
        $params = array_merge([$userId], $seriesIds);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        return array_map(fn ($r) => (int)$r['series_id'], $rows);
    }
}
