<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class AuditLog
{
    public static function create(string $event, ?int $userId, array $meta): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO audit_log (event, user_id, meta, created_at) VALUES (:e,:u,:m,NOW())');
        $stmt->execute([
            'e' => $event,
            'u' => $userId,
            'm' => json_encode($meta),
        ]);
    }
}
