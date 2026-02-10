<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class UserContentStatus
{
    public static function setRead(int $userId, int $contentId, bool $read): void
    {
        if ($read) {
            $stmt = Database::connection()->prepare('INSERT INTO user_content_status (user_id, content_id, is_read, read_at, updated_at) VALUES (:u,:c,1,NOW(),NOW()) ON DUPLICATE KEY UPDATE is_read = 1, read_at = NOW(), updated_at = NOW()');
            $stmt->execute(['u' => $userId, 'c' => $contentId]);
            return;
        }
        $stmt = Database::connection()->prepare('INSERT INTO user_content_status (user_id, content_id, is_read, read_at, updated_at) VALUES (:u,:c,0,NULL,NOW()) ON DUPLICATE KEY UPDATE is_read = 0, read_at = NULL, updated_at = NOW()');
        $stmt->execute(['u' => $userId, 'c' => $contentId]);
    }

    public static function getReadIdsForUser(int $userId, array $contentIds): array
    {
        if (empty($contentIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($contentIds), '?'));
        $stmt = Database::connection()->prepare("SELECT content_id FROM user_content_status WHERE user_id = ? AND is_read = 1 AND content_id IN ($placeholders)");
        $stmt->execute(array_merge([$userId], $contentIds));
        return array_map('intval', array_column($stmt->fetchAll(), 'content_id'));
    }

    public static function setLastPage(int $userId, int $contentId, int $page): void
    {
        $page = max(0, $page);
        $stmt = Database::connection()->prepare(
            'INSERT INTO user_content_status (user_id, content_id, last_page, updated_at) VALUES (:u,:c,:p,NOW()) ON DUPLICATE KEY UPDATE last_page = :p2, updated_at = NOW()'
        );
        $stmt->execute(['u' => $userId, 'c' => $contentId, 'p' => $page, 'p2' => $page]);
    }

    public static function setReadForSeriesAndTypes(int $userId, int $seriesId, array $types): void
    {
        $types = array_values(array_unique(array_map('strtolower', $types)));
        if (empty($types)) {
            return;
        }
        $parts = [];
        if (in_array('pdf', $types, true)) {
            $parts[] = "LOWER(ci.cbz_path) LIKE '%.pdf'";
        }
        if (in_array('epub', $types, true)) {
            $parts[] = "LOWER(ci.cbz_path) LIKE '%.epub'";
        }
        if (in_array('cbz', $types, true)) {
            $parts[] = "(ci.cbz_path IS NULL OR (LOWER(ci.cbz_path) NOT LIKE '%.pdf' AND LOWER(ci.cbz_path) NOT LIKE '%.epub'))";
        }
        if (empty($parts)) {
            return;
        }
        $where = implode(' OR ', $parts);
        $sql = "INSERT INTO user_content_status (user_id, content_id, is_read, read_at, updated_at)
                SELECT :u, ci.id, 1, NOW(), NOW()
                FROM content_items ci
                WHERE ci.series_id = :s AND ({$where})
                ON DUPLICATE KEY UPDATE is_read = 1, read_at = NOW(), updated_at = NOW()";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['u' => $userId, 's' => $seriesId]);
    }

    public static function setUnreadForSeriesAndTypes(int $userId, int $seriesId, array $types): void
    {
        $types = array_values(array_unique(array_map('strtolower', $types)));
        if (empty($types)) {
            return;
        }
        $parts = [];
        if (in_array('pdf', $types, true)) {
            $parts[] = "LOWER(ci.cbz_path) LIKE '%.pdf'";
        }
        if (in_array('epub', $types, true)) {
            $parts[] = "LOWER(ci.cbz_path) LIKE '%.epub'";
        }
        if (in_array('cbz', $types, true)) {
            $parts[] = "(ci.cbz_path IS NULL OR (LOWER(ci.cbz_path) NOT LIKE '%.pdf' AND LOWER(ci.cbz_path) NOT LIKE '%.epub'))";
        }
        if (empty($parts)) {
            return;
        }
        $where = implode(' OR ', $parts);
        $sql = "INSERT INTO user_content_status (user_id, content_id, is_read, read_at, updated_at)
                SELECT :u, ci.id, 0, NULL, NOW()
                FROM content_items ci
                WHERE ci.series_id = :s AND ({$where})
                ON DUPLICATE KEY UPDATE is_read = 0, read_at = NULL, updated_at = NOW()";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['u' => $userId, 's' => $seriesId]);
    }

    public static function countReadForSeriesAndTypes(int $userId, int $seriesId, array $types): int
    {
        $types = array_values(array_unique(array_map('strtolower', $types)));
        if (empty($types)) {
            return 0;
        }
        $parts = [];
        if (in_array('pdf', $types, true)) {
            $parts[] = "LOWER(ci.cbz_path) LIKE '%.pdf'";
        }
        if (in_array('epub', $types, true)) {
            $parts[] = "LOWER(ci.cbz_path) LIKE '%.epub'";
        }
        if (in_array('cbz', $types, true)) {
            $parts[] = "(ci.cbz_path IS NULL OR (LOWER(ci.cbz_path) NOT LIKE '%.pdf' AND LOWER(ci.cbz_path) NOT LIKE '%.epub'))";
        }
        if (empty($parts)) {
            return 0;
        }
        $where = implode(' OR ', $parts);
        $sql = "SELECT COUNT(*) AS c
                FROM content_items ci
                INNER JOIN user_content_status ucs ON ucs.content_id = ci.id
                    AND ucs.user_id = :u
                    AND ucs.is_read = 1
                WHERE ci.series_id = :s AND ({$where})";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['u' => $userId, 's' => $seriesId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function getProgressForUser(int $userId, array $contentIds): array
    {
        if (empty($contentIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($contentIds), '?'));
        $stmt = Database::connection()->prepare("SELECT content_id, last_page FROM user_content_status WHERE user_id = ? AND content_id IN ($placeholders)");
        $stmt->execute(array_merge([$userId], $contentIds));
        $rows = $stmt->fetchAll();
        $result = [];
        foreach ($rows as $row) {
            $result[(int)$row['content_id']] = (int)($row['last_page'] ?? 0);
        }
        return $result;
    }
}