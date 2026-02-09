<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Voucher
{
    private const CODE_PREFIX = 'VC-';

    public static function all(): array
    {
        $sql = 'SELECT v.*, p.title AS package_title
                FROM vouchers v
                LEFT JOIN packages p ON p.id = v.package_id
                ORDER BY v.created_at DESC';
        $stmt = Database::connection()->query($sql);
        return $stmt->fetchAll();
    }

    public static function findByCode(string $code): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM vouchers WHERE code = :c');
        $stmt->execute(['c' => $code]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function upsert(array $data): void
    {
        $sql = 'INSERT INTO vouchers (code, package_id, days, max_uses, expires_at, is_active, created_at)
                VALUES (:c, :p, :d, :m, :e, :a, NOW())
                ON DUPLICATE KEY UPDATE package_id = :p2, days = :d2, max_uses = :m2, expires_at = :e2, is_active = :a2';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            'c' => $data['code'],
            'p' => $data['package_id'],
            'd' => $data['days'],
            'm' => $data['max_uses'],
            'e' => $data['expires_at'],
            'a' => $data['is_active'],
            'p2' => $data['package_id'],
            'd2' => $data['days'],
            'm2' => $data['max_uses'],
            'e2' => $data['expires_at'],
            'a2' => $data['is_active'],
        ]);
    }

    public static function delete(string $code): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM vouchers WHERE code = :c');
        $stmt->execute(['c' => $code]);
    }

    public static function hasRedeemed(string $code, int $userId): bool
    {
        $stmt = Database::connection()->prepare('SELECT 1 FROM voucher_redemptions WHERE voucher_code = :c AND user_id = :u LIMIT 1');
        $stmt->execute(['c' => $code, 'u' => $userId]);
        return (bool)$stmt->fetch();
    }

    public static function generateUniqueCode(int $bytes = 6): string
    {
        $attempts = 0;
        do {
            $raw = bin2hex(random_bytes($bytes));
            $code = self::CODE_PREFIX . strtoupper($raw);
            $exists = self::findByCode($code) !== null;
            $attempts++;
        } while ($exists && $attempts < 10);

        if ($exists) {
            $code = self::CODE_PREFIX . strtoupper(bin2hex(random_bytes($bytes + 2)));
        }

        return $code;
    }
}
