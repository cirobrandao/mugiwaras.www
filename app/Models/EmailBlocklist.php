<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class EmailBlocklist
{
    public static function normalizeDomainFromInput(string $input): ?string
    {
        $value = strtolower(trim($input));
        if ($value === '') {
            return null;
        }
        if (strpos($value, '@') !== false) {
            $value = substr(strrchr($value, '@') ?: '', 1);
        }
        $value = ltrim($value, '@');
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        if (!preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/i', $value)) {
            return null;
        }
        return $value;
    }

    public static function exists(string $domain): bool
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM email_blocklist WHERE domain = :d');
        $stmt->execute(['d' => $domain]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0) > 0;
    }

    public static function isBlocked(string $email): bool
    {
        $domain = strtolower(substr(strrchr($email, '@') ?: '', 1));
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM email_blocklist WHERE domain = :d');
        $stmt->execute(['d' => $domain]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0) > 0;
    }

    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM email_blocklist ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public static function add(string $input): string
    {
        $domain = self::normalizeDomainFromInput($input);
        if ($domain === null) {
            return 'invalid';
        }
        if (self::exists($domain)) {
            return 'exists';
        }
        $stmt = Database::connection()->prepare('INSERT INTO email_blocklist (domain, created_at) VALUES (:d, NOW())');
        $stmt->execute(['d' => $domain]);
        return 'created';
    }

    public static function remove(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM email_blocklist WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
