<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Notification
{
    public static function adminAll(): array
    {
        $sql = "SELECT * FROM notifications ORDER BY is_active DESC, CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END ASC, created_at DESC";
        $stmt = Database::connection()->query($sql);
        return $stmt->fetchAll() ?: [];
    }

    public static function activeForUsers(int $limit = 5): array
    {
        $sql = "SELECT * FROM notifications
                WHERE is_active = 1
                  AND (starts_at IS NULL OR starts_at <= NOW())
                  AND (ends_at IS NULL OR ends_at >= NOW())
                ORDER BY CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END ASC, created_at DESC
                LIMIT :l";
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue('l', max(1, $limit), \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    public static function create(string $title, string $body, string $priority, bool $active, ?string $startsAt, ?string $endsAt): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO notifications (title, body, priority, is_active, starts_at, ends_at, created_at, updated_at) VALUES (:t, :b, :p, :a, :s, :e, NOW(), NOW())');
        $stmt->execute([
            't' => $title,
            'b' => $body,
            'p' => $priority,
            'a' => $active ? 1 : 0,
            's' => $startsAt,
            'e' => $endsAt,
        ]);
    }

    public static function update(int $id, string $title, string $body, string $priority, bool $active, ?string $startsAt, ?string $endsAt): void
    {
        $stmt = Database::connection()->prepare('UPDATE notifications SET title = :t, body = :b, priority = :p, is_active = :a, starts_at = :s, ends_at = :e, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            't' => $title,
            'b' => $body,
            'p' => $priority,
            'a' => $active ? 1 : 0,
            's' => $startsAt,
            'e' => $endsAt,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM notifications WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
