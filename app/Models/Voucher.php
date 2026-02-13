<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Voucher
{
    private const CODE_PREFIX = 'VC-';

    public static function all(): array
    {
        $sql = 'SELECT v.*, p.title AS package_title, p.subscription_days AS package_subscription_days
                FROM vouchers v
                LEFT JOIN packages p ON p.id = v.package_id
                ORDER BY v.created_at DESC';
        $stmt = Database::connection()->query($sql);
        $rows = $stmt->fetchAll();

        if (empty($rows)) {
            return [];
        }

        $codes = array_values(array_filter(array_map(static fn(array $row): string => (string)($row['code'] ?? ''), $rows)));
        $creatorMap = self::creatorByCode($codes);
        $usageMap = self::usageByCode($codes);

        foreach ($rows as &$row) {
            $code = (string)($row['code'] ?? '');
            $days = (int)($row['days'] ?? 0);
            $packageDays = (int)($row['package_subscription_days'] ?? 0);

            $row['added_days'] = $days > 0 ? $days : $packageDays;
            $row['creator_username'] = $creatorMap[$code]['username'] ?? null;
            $row['creator_at'] = $creatorMap[$code]['created_at'] ?? null;
            $row['redeemed_count'] = (int)($usageMap[$code]['count'] ?? 0);
            $row['redeemed_users'] = $usageMap[$code]['users'] ?? [];
            $row['is_used'] = $row['redeemed_count'] > 0 ? 1 : 0;
        }
        unset($row);

        return $rows;
    }

    private static function creatorByCode(array $codes): array
    {
        if (empty($codes)) {
            return [];
        }

        $stmt = Database::connection()->prepare(
            'SELECT al.meta, al.created_at, u.username
             FROM audit_log al
             LEFT JOIN users u ON u.id = al.user_id
             WHERE al.event = :event
             ORDER BY al.created_at ASC'
        );
        $stmt->execute(['event' => 'voucher_saved']);
        $rows = $stmt->fetchAll();

        $validCodes = array_fill_keys($codes, true);
        $result = [];
        foreach ($rows as $row) {
            $meta = json_decode((string)($row['meta'] ?? ''), true);
            if (!is_array($meta)) {
                continue;
            }
            $code = (string)($meta['code'] ?? '');
            if ($code === '' || !isset($validCodes[$code]) || isset($result[$code])) {
                continue;
            }

            $result[$code] = [
                'username' => (string)($row['username'] ?? ''),
                'created_at' => (string)($row['created_at'] ?? ''),
            ];
        }

        return $result;
    }

    private static function usageByCode(array $codes): array
    {
        if (empty($codes)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($codes), '?'));
        $sql = "SELECT vr.voucher_code, u.username
                FROM voucher_redemptions vr
                INNER JOIN users u ON u.id = vr.user_id
                WHERE vr.voucher_code IN ($placeholders)
                ORDER BY vr.redeemed_at DESC";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($codes);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $code = (string)($row['voucher_code'] ?? '');
            if ($code === '') {
                continue;
            }
            if (!isset($result[$code])) {
                $result[$code] = [
                    'count' => 0,
                    'users' => [],
                ];
            }

            $result[$code]['count']++;
            $username = trim((string)($row['username'] ?? ''));
            if ($username !== '' && !in_array($username, $result[$code]['users'], true)) {
                $result[$code]['users'][] = $username;
            }
        }

        return $result;
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
