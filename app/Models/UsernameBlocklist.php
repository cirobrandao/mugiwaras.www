<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class UsernameBlocklist
{
    public static function normalize(string $value): ?string
    {
        $value = strtolower(trim($value));
        if ($value === '') {
            return null;
        }
        $value = preg_replace('/[^a-z0-9_.]/', '', $value) ?? '';
        if ($value === '' || strlen($value) < 3) {
            return null;
        }
        return $value;
    }

    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM username_blocklist ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public static function isBlocked(string $username): bool
    {
        $normalized = self::normalize($username);
        if ($normalized === null) {
            return false;
        }
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM username_blocklist WHERE username = :u');
        $stmt->execute(['u' => $normalized]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0) > 0;
    }

    public static function add(string $input): string
    {
        $normalized = self::normalize($input);
        if ($normalized === null) {
            return 'invalid';
        }
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM username_blocklist WHERE username = :u');
        $stmt->execute(['u' => $normalized]);
        $row = $stmt->fetch();
        if ((int)($row['c'] ?? 0) > 0) {
            return 'exists';
        }
        $stmt = Database::connection()->prepare('INSERT INTO username_blocklist (username, created_at) VALUES (:u, NOW())');
        $stmt->execute(['u' => $normalized]);
        return 'created';
    }

    public static function remove(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM username_blocklist WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}