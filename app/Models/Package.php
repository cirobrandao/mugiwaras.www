<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Package
{
    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM packages ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM packages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO packages (title, description, price, bonus_credits, subscription_days, created_at) VALUES (:t,:d,:p,:b,:s,NOW())'
        );
        $stmt->execute($data);
    }

    public static function update(int $id, array $data): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE packages SET title = :t, description = :d, price = :p, bonus_credits = :b, subscription_days = :s WHERE id = :id'
        );
        $stmt->execute(array_merge($data, ['id' => $id]));
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM packages WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
