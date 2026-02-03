<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class EmailBlocklist
{
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

    public static function add(string $domain): void
    {
        $domain = strtolower(trim($domain));
        if ($domain === '') {
            return;
        }
        $stmt = Database::connection()->prepare('INSERT INTO email_blocklist (domain, created_at) VALUES (:d, NOW())');
        $stmt->execute(['d' => $domain]);
    }

    public static function remove(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM email_blocklist WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
