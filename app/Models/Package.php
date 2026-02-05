<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Package
{
    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM packages ORDER BY sort_order ASC, id DESC');
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM packages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO packages (title, description, price, bonus_credits, subscription_days, sort_order, created_at) VALUES (:t,:d,:p,:b,:s,:o,NOW())'
        );
        $stmt->execute($data);
        return (int)Database::connection()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE packages SET title = :t, description = :d, price = :p, bonus_credits = :b, subscription_days = :s, sort_order = :o WHERE id = :id'
        );
        $stmt->execute(array_merge($data, ['id' => $id]));
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM packages WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function setCategories(int $packageId, array $categoryIds): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM package_categories WHERE package_id = :p');
        $stmt->execute(['p' => $packageId]);

        $clean = array_values(array_unique(array_filter(array_map('intval', $categoryIds), static fn ($v) => $v > 0)));
        if (empty($clean)) {
            return;
        }
        $stmt = Database::connection()->prepare('INSERT INTO package_categories (package_id, category_id) VALUES (:p,:c)');
        foreach ($clean as $cid) {
            $stmt->execute(['p' => $packageId, 'c' => $cid]);
        }
    }

    public static function categoriesMap(array $packageIds): array
    {
        $clean = array_values(array_unique(array_filter(array_map('intval', $packageIds), static fn ($v) => $v > 0)));
        if (empty($clean)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($clean), '?'));
        $stmt = Database::connection()->prepare('SELECT package_id, category_id FROM package_categories WHERE package_id IN (' . $placeholders . ')');
        $stmt->execute($clean);
        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $row) {
            $pid = (int)($row['package_id'] ?? 0);
            $cid = (int)($row['category_id'] ?? 0);
            if ($pid <= 0 || $cid <= 0) {
                continue;
            }
            if (!isset($map[$pid])) {
                $map[$pid] = [];
            }
            $map[$pid][] = $cid;
        }
        return $map;
    }
}
