<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class NewsCategory
{
    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM news_categories ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM news_categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $name, bool $showSidebar, bool $showBelowMostRead): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO news_categories (name, show_sidebar, show_below_most_read, created_at) VALUES (:n,:s,:b,NOW())');
        $stmt->execute([
            'n' => $name,
            's' => $showSidebar ? 1 : 0,
            'b' => $showBelowMostRead ? 1 : 0,
        ]);
    }

    public static function update(int $id, string $name, bool $showSidebar, bool $showBelowMostRead): void
    {
        $stmt = Database::connection()->prepare('UPDATE news_categories SET name = :n, show_sidebar = :s, show_below_most_read = :b WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'n' => $name,
            's' => $showSidebar ? 1 : 0,
            'b' => $showBelowMostRead ? 1 : 0,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM news_categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
