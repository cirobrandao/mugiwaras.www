<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Setting
{
    public static function get(string $key, string $default = ''): string
    {
        $stmt = Database::connection()->prepare('SELECT value FROM settings WHERE `key` = :k');
        $stmt->execute(['k' => $key]);
        $row = $stmt->fetch();
        return $row ? (string)$row['value'] : $default;
    }

    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM settings ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public static function set(string $key, string $value): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO settings (`key`, value) VALUES (:k,:v) ON DUPLICATE KEY UPDATE value = :v2');
        $stmt->execute(['k' => $key, 'v' => $value, 'v2' => $value]);
    }

    public static function delete(string $key): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM settings WHERE `key` = :k');
        $stmt->execute(['k' => $key]);
    }
}
