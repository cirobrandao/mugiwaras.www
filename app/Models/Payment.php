<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Payment
{
    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare('INSERT INTO payments (user_id,package_id,status,months,created_at) VALUES (:uid,:pid,:status,:months,NOW())');
        $stmt->execute($data);
        return (int)Database::connection()->lastInsertId();
    }

    public static function all(): array
    {
        $sql = 'SELECT p.*, u.username AS user_name, pk.title AS package_name
                FROM payments p
                LEFT JOIN users u ON u.id = p.user_id
                LEFT JOIN packages pk ON pk.id = p.package_id
                ORDER BY p.id DESC';
        $stmt = Database::connection()->query($sql);
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

    public static function countByPackageIds(array $packageIds): array
    {
        $clean = array_values(array_unique(array_filter(array_map('intval', $packageIds), static fn ($v) => $v > 0)));
        if (empty($clean)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($clean), '?'));
        $stmt = Database::connection()->prepare('SELECT package_id, COUNT(*) AS c FROM payments WHERE package_id IN (' . $placeholders . ') GROUP BY package_id');
        $stmt->execute($clean);
        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $row) {
            $pid = (int)($row['package_id'] ?? 0);
            $map[$pid] = (int)($row['c'] ?? 0);
        }
        return $map;
    }

    public static function latestApprovedByUser(int $userId): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM payments WHERE user_id = :u AND status = :s ORDER BY id DESC LIMIT 1');
        $stmt->execute(['u' => $userId, 's' => 'approved']);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function latestApprovedByUsers(array $userIds): array
    {
        $clean = array_values(array_unique(array_filter(array_map('intval', $userIds), static fn ($v) => $v > 0)));
        if (empty($clean)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($clean), '?'));
        $sql = "SELECT p.*
                FROM payments p
                INNER JOIN (
                    SELECT user_id, MAX(id) AS id
                    FROM payments
                    WHERE status = 'approved' AND user_id IN ($placeholders)
                    GROUP BY user_id
                ) t ON t.id = p.id";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($clean);
        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $row) {
            $uid = (int)($row['user_id'] ?? 0);
            if ($uid > 0) {
                $map[$uid] = $row;
            }
        }
        return $map;
    }

    public static function countPending(): int
    {
        try {
            $stmt = Database::connection()->query("SELECT COUNT(*) AS c FROM payments WHERE status = 'pending'");
            $row = $stmt->fetch();
            return (int)($row['c'] ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
