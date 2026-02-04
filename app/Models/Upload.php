<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Upload
{
    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO uploads (user_id, category_id, series_id, original_name, title, source_path, target_path, status, job_id, file_size, created_at) VALUES (:u,:c,:s,:o,:ttl,:sp,:tp,:st,:j,:fs,NOW())'
        );
        $stmt->execute($data);
        return (int)Database::connection()->lastInsertId();
    }

    public static function byUser(int $userId, int $limit = 200): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM uploads WHERE user_id = :u ORDER BY id DESC LIMIT :l');
        $stmt->bindValue('u', $userId, \PDO::PARAM_INT);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function byUserPaged(int $userId, int $limit, int $offset): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM uploads WHERE user_id = :u ORDER BY id DESC LIMIT :l OFFSET :o');
        $stmt->bindValue('u', $userId, \PDO::PARAM_INT);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('o', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countByUser(int $userId): int
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM uploads WHERE user_id = :u');
        $stmt->execute(['u' => $userId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function totalSizeByUser(int $userId): int
    {
        $stmt = Database::connection()->prepare('SELECT COALESCE(SUM(file_size),0) AS s FROM uploads WHERE user_id = :u');
        $stmt->execute(['u' => $userId]);
        $row = $stmt->fetch();
        return (int)($row['s'] ?? 0);
    }

    public static function countByUserStatus(int $userId, string $status): int
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM uploads WHERE user_id = :u AND status = :s');
        $stmt->execute(['u' => $userId, 's' => $status]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function all(int $limit = 200): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM uploads ORDER BY id DESC LIMIT :l');
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function paged(int $limit, int $offset): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM uploads ORDER BY id DESC LIMIT :l OFFSET :o');
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('o', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countAll(): int
    {
        $stmt = Database::connection()->query('SELECT COUNT(*) AS c FROM uploads');
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function pendingBySeries(int $seriesId, int $limit = 100): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM uploads WHERE series_id = :s AND status = :st ORDER BY id DESC LIMIT :l');
        $stmt->bindValue('s', $seriesId, \PDO::PARAM_INT);
        $stmt->bindValue('st', 'queued');
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM uploads WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function setStatusByJob(int $jobId, string $status): void
    {
        $stmt = Database::connection()->prepare('UPDATE uploads SET status = :s, updated_at = NOW() WHERE job_id = :j');
        $stmt->execute(['s' => $status, 'j' => $jobId]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM uploads WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
