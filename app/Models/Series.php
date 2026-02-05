<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Series
{
    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM series ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM series WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function byCategory(int $categoryId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM series WHERE category_id = :c ORDER BY name ASC');
        $stmt->execute(['c' => $categoryId]);
        return $stmt->fetchAll();
    }

    public static function byCategoryWithCounts(int $categoryId): array
    {
        $sql = 'SELECT s.*, COUNT(ci.id) AS chapter_count
                FROM series s
                LEFT JOIN content_items ci ON ci.series_id = s.id
                WHERE s.category_id = :c
                GROUP BY s.id
                ORDER BY s.pin_order DESC, s.name ASC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['c' => $categoryId]);
        return $stmt->fetchAll();
    }

    public static function byCategoryWithCountsAndTypes(int $categoryId): array
    {
        $sql = "SELECT s.*, COUNT(ci.id) AS chapter_count,
                       SUM(CASE WHEN LOWER(ci.cbz_path) LIKE '%.pdf' THEN 1 ELSE 0 END) AS pdf_count,
                       SUM(CASE WHEN ci.cbz_path IS NOT NULL AND LOWER(ci.cbz_path) NOT LIKE '%.pdf' THEN 1 ELSE 0 END) AS cbz_count
                FROM series s
                LEFT JOIN content_items ci ON ci.series_id = s.id
                WHERE s.category_id = :c
                GROUP BY s.id
                ORDER BY s.pin_order DESC, s.name ASC";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['c' => $categoryId]);
        return $stmt->fetchAll();
    }

    public static function searchByNameWithCounts(string $query, int $limit = 60, int $minChapters = 0): array
    {
        $sql = 'SELECT s.*, c.name AS category_name, COUNT(ci.id) AS chapter_count
                FROM series s
                INNER JOIN categories c ON c.id = s.category_id
                LEFT JOIN content_items ci ON ci.series_id = s.id
                WHERE s.name LIKE :q
                GROUP BY s.id';
        if ($minChapters > 0) {
            $sql .= ' HAVING COUNT(ci.id) >= :min';
        }
        $sql .= ' ORDER BY s.name ASC LIMIT :l';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue('q', '%' . $query . '%');
        if ($minChapters > 0) {
            $stmt->bindValue('min', $minChapters, \PDO::PARAM_INT);
        }
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findByName(int $categoryId, string $name): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM series WHERE category_id = :c AND name = :n');
        $stmt->execute(['c' => $categoryId, 'n' => $name]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(int $categoryId, string $name): int
    {
        $stmt = Database::connection()->prepare('INSERT INTO series (category_id, name, created_at) VALUES (:c, :n, NOW())');
        $stmt->execute(['c' => $categoryId, 'n' => $name]);
        return (int)Database::connection()->lastInsertId();
    }

    public static function rename(int $id, string $name): void
    {
        $stmt = Database::connection()->prepare('UPDATE series SET name = :n WHERE id = :id');
        $stmt->execute(['n' => $name, 'id' => $id]);
    }

    public static function updatePinOrder(int $id, int $pinOrder): void
    {
        $stmt = Database::connection()->prepare('UPDATE series SET pin_order = :p WHERE id = :id');
        $stmt->execute(['p' => $pinOrder, 'id' => $id]);
    }

    public static function countByCategory(int $categoryId): int
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM series WHERE category_id = :c');
        $stmt->execute(['c' => $categoryId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function favoritesForUser(int $userId, int $limit = 12): array
    {
        $sql = 'SELECT s.*, c.name AS category_name, COUNT(ci.id) AS chapter_count
                FROM user_series_favorites usf
                INNER JOIN series s ON s.id = usf.series_id
                INNER JOIN categories c ON c.id = s.category_id
                LEFT JOIN content_items ci ON ci.series_id = s.id
                WHERE usf.user_id = :u
                GROUP BY s.id
                ORDER BY usf.created_at DESC
                LIMIT :l';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue('u', $userId, \PDO::PARAM_INT);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function mostRead(int $limit = 5): array
    {
        $sql = "SELECT s.id, s.name, s.category_id, c.name AS category_name, c.tag_color AS category_tag_color, COUNT(DISTINCT ce.user_id) AS read_count,
                   MAX(CASE WHEN LOWER(ci_all.cbz_path) LIKE '%.pdf' THEN 1 ELSE 0 END) AS has_pdf
            FROM content_events ce
            INNER JOIN content_items ci ON ci.id = ce.content_id
            INNER JOIN series s ON s.id = ci.series_id
            INNER JOIN categories c ON c.id = s.category_id
            LEFT JOIN content_items ci_all ON ci_all.series_id = s.id
            WHERE ce.event = 'read_open' AND ce.user_id IS NOT NULL
            GROUP BY s.id
            ORDER BY read_count DESC, s.name ASC
            LIMIT :l";
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}