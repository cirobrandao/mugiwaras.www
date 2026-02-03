<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Payment
{
    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare('INSERT INTO payments (user_id,package_id,status,created_at) VALUES (:uid,:pid,:status,NOW())');
        $stmt->execute($data);
        return (int)Database::connection()->lastInsertId();
    }

    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM payments ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public static function byUser(int $userId, int $limit = 100): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM payments WHERE user_id = :u ORDER BY id DESC LIMIT :l');
        $stmt->bindValue('u', $userId, \PDO::PARAM_INT);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM payments WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function setStatus(int $id, string $status): void
    {
        $stmt = Database::connection()->prepare('UPDATE payments SET status = :s, updated_at = NOW() WHERE id = :id');
        $stmt->execute(['s' => $status, 'id' => $id]);
    }

    public static function attachProof(int $id, string $path): void
    {
        $stmt = Database::connection()->prepare('UPDATE payments SET proof_path = :p, updated_at = NOW() WHERE id = :id');
        $stmt->execute(['p' => $path, 'id' => $id]);
    }
}
