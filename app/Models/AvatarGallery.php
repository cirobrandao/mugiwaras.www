<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class AvatarGallery
{
    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM avatar_gallery ORDER BY sort_order ASC, id DESC');
        return $stmt->fetchAll();
    }

    public static function activeAll(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM avatar_gallery WHERE is_active = 1 ORDER BY sort_order ASC, id DESC');
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM avatar_gallery WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO avatar_gallery (title, file_path, is_active, sort_order, created_at) VALUES (:t,:p,:a,:s,NOW())'
        );
        $stmt->execute([
            't' => $data['title'],
            'p' => $data['file_path'],
            'a' => $data['is_active'],
            's' => $data['sort_order'],
        ]);
        return (int)Database::connection()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE avatar_gallery SET title = :t, is_active = :a, sort_order = :s WHERE id = :id'
        );
        $stmt->execute([
            't' => $data['title'],
            'a' => $data['is_active'],
            's' => $data['sort_order'],
            'id' => $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM avatar_gallery WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
