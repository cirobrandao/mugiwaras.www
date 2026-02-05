<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class ContentItem
{
    public static function create(array $data): int
    {
        if (!array_key_exists('co', $data)) {
            $data['co'] = 0;
        }
        $stmt = Database::connection()->prepare(
            'INSERT INTO content_items (library_id, category_id, series_id, title, cbz_path, file_hash, file_size, original_name, content_order, view_count, download_count, created_at) VALUES (:l,:c,:s,:t,:p,:h,:sz,:o,:co,0,0,NOW())'
        );
        $stmt->execute($data);
        return (int)Database::connection()->lastInsertId();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM content_items WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByHash(string $hash): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM content_items WHERE file_hash = :h');
        $stmt->execute(['h' => $hash]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByPath(string $path): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM content_items WHERE cbz_path = :p');
        $stmt->execute(['p' => $path]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function updateTitle(int $id, string $title): void
    {
        $stmt = Database::connection()->prepare('UPDATE content_items SET title = :t WHERE id = :id');
        $stmt->execute(['t' => $title, 'id' => $id]);
    }

    public static function updateOrder(int $id, int $order): void
    {
        $stmt = Database::connection()->prepare('UPDATE content_items SET content_order = :o WHERE id = :id');
        $stmt->execute(['o' => $order, 'id' => $id]);
    }

    public static function updateCategorySeries(int $id, ?int $categoryId, ?int $seriesId): void
    {
        $stmt = Database::connection()->prepare('UPDATE content_items SET category_id = :c, series_id = :s WHERE id = :id');
        $stmt->bindValue('id', $id, \PDO::PARAM_INT);
        if ($categoryId === null || $categoryId <= 0) {
            $stmt->bindValue('c', null, \PDO::PARAM_NULL);
        } else {
            $stmt->bindValue('c', $categoryId, \PDO::PARAM_INT);
        }
        if ($seriesId === null || $seriesId <= 0) {
            $stmt->bindValue('s', null, \PDO::PARAM_NULL);
        } else {
            $stmt->bindValue('s', $seriesId, \PDO::PARAM_INT);
        }
        $stmt->execute();
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM content_items WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function bySeries(int $seriesId, string $direction = 'asc', int $limit = 100, int $offset = 0): array
    {
        $dir = strtolower($direction) === 'desc' ? 'DESC' : 'ASC';
        $stmt = Database::connection()->prepare("SELECT * FROM content_items WHERE series_id = :s ORDER BY content_order {$dir}, title {$dir}, id {$dir} LIMIT :l OFFSET :o");
        $stmt->bindValue('s', $seriesId, \PDO::PARAM_INT);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('o', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function bySeriesAndFormat(int $seriesId, string $format, string $direction = 'asc', int $limit = 100, int $offset = 0): array
    {
        $format = strtolower($format);
        $isPdf = $format === 'pdf';
        $dir = strtolower($direction) === 'desc' ? 'DESC' : 'ASC';
        $sql = $isPdf
            ? "SELECT * FROM content_items WHERE series_id = :s AND LOWER(cbz_path) LIKE '%.pdf' ORDER BY content_order {$dir}, title {$dir}, id {$dir} LIMIT :l OFFSET :o"
            : "SELECT * FROM content_items WHERE series_id = :s AND (cbz_path IS NULL OR LOWER(cbz_path) NOT LIKE '%.pdf') ORDER BY content_order {$dir}, title {$dir}, id {$dir} LIMIT :l OFFSET :o";
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue('s', $seriesId, \PDO::PARAM_INT);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('o', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countBySeries(int $seriesId): int
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM content_items WHERE series_id = :s');
        $stmt->execute(['s' => $seriesId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function countBySeriesAndFormat(int $seriesId, string $format): int
    {
        $format = strtolower($format);
        $isPdf = $format === 'pdf';
        $sql = $isPdf
            ? "SELECT COUNT(*) AS c FROM content_items WHERE series_id = :s AND LOWER(cbz_path) LIKE '%.pdf'"
            : "SELECT COUNT(*) AS c FROM content_items WHERE series_id = :s AND (cbz_path IS NULL OR LOWER(cbz_path) NOT LIKE '%.pdf')";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['s' => $seriesId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function countByCategory(int $categoryId): int
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM content_items WHERE category_id = :c');
        $stmt->execute(['c' => $categoryId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function byCategory(int $categoryId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM content_items WHERE category_id = :c');
        $stmt->execute(['c' => $categoryId]);
        return $stmt->fetchAll();
    }

    public static function incrementView(int $id): void
    {
        $stmt = Database::connection()->prepare('UPDATE content_items SET view_count = view_count + 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function incrementDownload(int $id): void
    {
        $stmt = Database::connection()->prepare('UPDATE content_items SET download_count = download_count + 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function latestSeriesWithContent(int $limit = 8): array
    {
        $sql = 'SELECT s.id AS series_id, s.name AS series_name,
                       c.id AS category_id, c.name AS category_name, c.tag_color AS category_tag_color,
                       MAX(ci.created_at) AS created_at, COUNT(ci.id) AS chapter_count
                FROM content_items ci
                INNER JOIN series s ON s.id = ci.series_id
                INNER JOIN categories c ON c.id = ci.category_id
                GROUP BY s.id, s.name, c.id, c.name, c.tag_color
                ORDER BY created_at DESC
                LIMIT :l';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
