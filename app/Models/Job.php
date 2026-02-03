<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Job
{
    public static function pending(int $limit = 10): array
    {
        $stmt = Database::connection()->prepare("SELECT * FROM jobs WHERE status = 'pending' ORDER BY id ASC LIMIT :l");
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function pendingByUser(int $userId, int $limit = 10): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT j.* FROM jobs j INNER JOIN uploads u ON u.job_id = j.id WHERE u.user_id = :u AND j.status = 'pending' ORDER BY j.id ASC LIMIT :l"
        );
        $stmt->bindValue('u', $userId, \PDO::PARAM_INT);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function create(string $type, array $payload): int
    {
        $stmt = Database::connection()->prepare("INSERT INTO jobs (job_type, payload, status, created_at) VALUES (:t,:p,'pending',NOW())");
        $stmt->execute([
            't' => $type,
            'p' => json_encode($payload),
        ]);
        return (int)Database::connection()->lastInsertId();
    }

    public static function markRunning(int $id): void
    {
        $stmt = Database::connection()->prepare("UPDATE jobs SET status = 'running', started_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public static function markDone(int $id): void
    {
        $stmt = Database::connection()->prepare("UPDATE jobs SET status = 'done', finished_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public static function markFailed(int $id, string $error): void
    {
        $stmt = Database::connection()->prepare("UPDATE jobs SET status = 'failed', error_message = :e, finished_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $id, 'e' => $error]);
    }
}
