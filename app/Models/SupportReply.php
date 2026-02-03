<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class SupportReply
{
    public static function create(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO support_replies (support_id,user_id,admin_id,message,attachment_path,attachment_name,created_at) VALUES (:sid,:uid,:aid,:msg,:ap,:an,NOW())'
        );
        $stmt->execute($data);
    }

    public static function bySupportId(int $supportId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM support_replies WHERE support_id = :id ORDER BY id ASC');
        $stmt->execute(['id' => $supportId]);
        return $stmt->fetchAll();
    }

    public static function hasAdminReply(int $supportId): bool
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM support_replies WHERE support_id = :id AND admin_id IS NOT NULL');
        $stmt->execute(['id' => $supportId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0) > 0;
    }

    public static function lastReplyIsAdmin(int $supportId): bool
    {
        $stmt = Database::connection()->prepare('SELECT admin_id FROM support_replies WHERE support_id = :id ORDER BY id DESC LIMIT 1');
        $stmt->execute(['id' => $supportId]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }
        return !empty($row['admin_id']);
    }

    public static function lastReplyIsUser(int $supportId): bool
    {
        $stmt = Database::connection()->prepare('SELECT user_id FROM support_replies WHERE support_id = :id ORDER BY id DESC LIMIT 1');
        $stmt->execute(['id' => $supportId]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }
        return !empty($row['user_id']);
    }

    public static function countPendingForUser(int $userId): int
    {
        $sql = "SELECT COUNT(*) AS c
                FROM support_messages sm
                WHERE sm.user_id = :uid
                  AND sm.status <> 'closed'
                  AND (
                    SELECT sr.admin_id
                    FROM support_replies sr
                    WHERE sr.support_id = sm.id
                    ORDER BY sr.id DESC
                    LIMIT 1
                  ) IS NOT NULL";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }
}
