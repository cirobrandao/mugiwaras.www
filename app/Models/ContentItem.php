<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class ContentItem
{
    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO content_items (library_id, category_id, series_id, title, cbz_path, file_hash, file_size, original_name, view_count, download_count, created_at) VALUES (:l,:c,:s,:t,:p,:h,:sz,:o,0,0,NOW())'
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

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM content_items WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function bySeries(int $seriesId, int $limit = 100, int $offset = 0): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM content_items WHERE series_id = :s ORDER BY id DESC LIMIT :l OFFSET :o');
        $stmt->bindValue('s', $seriesId, \PDO::PARAM_INT);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('o', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function bySeriesAndFormat(int $seriesId, string $format, int $limit = 100, int $offset = 0): array
    {
        $format = strtolower($format);
        $isPdf = $format === 'pdf';
        $sql = $isPdf
            ? "SELECT * FROM content_items WHERE series_id = :s AND LOWER(cbz_path) LIKE '%.pdf' ORDER BY id DESC LIMIT :l OFFSET :o"
            : "SELECT * FROM content_items WHERE series_id = :s AND (cbz_path IS NULL OR LOWER(cbz_path) NOT LIKE '%.pdf') ORDER BY id DESC LIMIT :l OFFSET :o";
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
}
