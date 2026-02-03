<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Category
{
    public static function isReady(): bool
    {
        try {
            Database::connection()->query('SELECT 1 FROM categories LIMIT 1');
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM categories ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByName(string $name): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM categories WHERE name = :n');
        $stmt->execute(['n' => $name]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $name, ?string $bannerPath = null, ?string $tagColor = null): int
    {
        $stmt = Database::connection()->prepare('INSERT INTO categories (name, banner_path, tag_color, created_at) VALUES (:n, :b, :t, NOW())');
        $stmt->execute(['n' => $name, 'b' => $bannerPath, 't' => $tagColor]);
        return (int)Database::connection()->lastInsertId();
    }

    public static function rename(int $id, string $name): void
    {
        $stmt = Database::connection()->prepare('UPDATE categories SET name = :n WHERE id = :id');
        $stmt->execute(['n' => $name, 'id' => $id]);
    }

    public static function updateBanner(int $id, ?string $bannerPath): void
    {
        $stmt = Database::connection()->prepare('UPDATE categories SET banner_path = :b WHERE id = :id');
        $stmt->execute(['b' => $bannerPath, 'id' => $id]);
    }

    public static function updateTagColor(int $id, ?string $tagColor): void
    {
        $stmt = Database::connection()->prepare('UPDATE categories SET tag_color = :t WHERE id = :id');
        $stmt->execute(['t' => $tagColor, 'id' => $id]);
    }

    public static function delete(int $id): void
    {
        $seriesCount = \App\Models\Series::countByCategory($id);
        $contentCount = \App\Models\ContentItem::countByCategory($id);
        if ($seriesCount > 0 || $contentCount > 0) {
            throw new \RuntimeException('category_in_use');
        }
        $stmt = Database::connection()->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

}