<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class SearchLog
{
    public static function create(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO search_logs (user_id,term,results_count,ip_address,created_at) VALUES (:uid,:term,:cnt,:ip,NOW())'
        );
        $stmt->execute($data);
    }
}
