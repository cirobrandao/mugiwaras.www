<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class UserFavorite
{
    public static function add(int $userId, int $contentId): void
    {
        $stmt = Database::connection()->prepare('INSERT IGNORE INTO user_favorites (user_id, content_id, created_at) VALUES (:u,:c,NOW())');
        $stmt->execute(['u' => $userId, 'c' => $contentId]);
    }

    public static function remove(int $userId, int $contentId): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM user_favorites WHERE user_id = :u AND content_id = :c');
        $stmt->execute(['u' => $userId, 'c' => $contentId]);
    }

    public static function getIdsForUser(int $userId, array $contentIds): array
    {
        if (empty($contentIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($contentIds), '?'));
        $stmt = Database::connection()->prepare("SELECT content_id FROM user_favorites WHERE user_id = ? AND content_id IN ($placeholders)");
        $stmt->execute(array_merge([$userId], $contentIds));
        return array_map('intval', array_column($stmt->fetchAll(), 'content_id'));
    }
}