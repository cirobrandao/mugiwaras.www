<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class News
{
    public static function all(int $limit = 200): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM news ORDER BY created_at DESC LIMIT :l');
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function latestPublished(int $limit = 5): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM news WHERE is_published = 1 AND (published_at IS NULL OR published_at <= NOW()) ORDER BY published_at DESC, created_at DESC LIMIT :l');
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM news WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $title, string $body, bool $published, ?string $publishedAt): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO news (title, body, is_published, published_at, created_at) VALUES (:t,:b,:p,:pa,NOW())');
        $stmt->execute([
            't' => $title,
            'b' => $body,
            'p' => $published ? 1 : 0,
            'pa' => $publishedAt,
        ]);
    }

    public static function update(int $id, string $title, string $body, bool $published, ?string $publishedAt): void
    {
        $stmt = Database::connection()->prepare('UPDATE news SET title = :t, body = :b, is_published = :p, published_at = :pa, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            't' => $title,
            'b' => $body,
            'p' => $published ? 1 : 0,
            'pa' => $publishedAt,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM news WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
