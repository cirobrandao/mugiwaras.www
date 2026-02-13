<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class News
{
    public static function all(int $limit = 200): array
    {
        $stmt = Database::connection()->prepare('SELECT n.*, nc.name AS category_name, nc.show_sidebar, nc.show_below_most_read FROM news n LEFT JOIN news_categories nc ON nc.id = n.category_id ORDER BY (n.published_at IS NULL) ASC, n.published_at DESC, n.created_at DESC LIMIT :l');
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

    public static function latestPublishedSidebar(int $limit = 5): array
    {
        $stmt = Database::connection()->prepare("SELECT n.*, nc.name AS category_name FROM news n INNER JOIN news_categories nc ON nc.id = n.category_id WHERE n.is_published = 1 AND nc.show_sidebar = 1 AND (n.published_at IS NULL OR n.published_at <= NOW()) ORDER BY n.published_at DESC, n.created_at DESC LIMIT :l");
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function latestPublishedBelowMostRead(int $limit = 5): array
    {
        $stmt = Database::connection()->prepare("SELECT n.*, nc.name AS category_name FROM news n INNER JOIN news_categories nc ON nc.id = n.category_id WHERE n.is_published = 1 AND nc.show_below_most_read = 1 AND (n.published_at IS NULL OR n.published_at <= NOW()) ORDER BY n.published_at DESC, n.created_at DESC LIMIT :l");
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

    public static function findPublished(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT n.*, nc.name AS category_name FROM news n LEFT JOIN news_categories nc ON nc.id = n.category_id WHERE n.id = :id AND n.is_published = 1 AND (n.published_at IS NULL OR n.published_at <= NOW())');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $title, string $body, int $categoryId, bool $published, ?string $publishedAt, ?string $featuredImagePath = null): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO news (title, body, category_id, is_published, featured_image_path, published_at, created_at) VALUES (:t,:b,:c,:p,:fi,:pa,NOW())');
        $stmt->execute([
            't' => $title,
            'b' => $body,
            'c' => $categoryId,
            'p' => $published ? 1 : 0,
            'fi' => $featuredImagePath,
            'pa' => $publishedAt,
        ]);
    }

    public static function update(int $id, string $title, string $body, int $categoryId, bool $published, ?string $publishedAt, ?string $featuredImagePath = null): void
    {
        $stmt = Database::connection()->prepare('UPDATE news SET title = :t, body = :b, category_id = :c, is_published = :p, featured_image_path = :fi, published_at = :pa, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            't' => $title,
            'b' => $body,
            'c' => $categoryId,
            'p' => $published ? 1 : 0,
            'fi' => $featuredImagePath,
            'pa' => $publishedAt,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM news WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
