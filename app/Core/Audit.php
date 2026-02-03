<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\AuditLog;

final class Audit
{
    public static function log(string $event, ?int $userId = null, array $meta = []): void
    {
        AuditLog::create($event, $userId, $meta);
    }
}
