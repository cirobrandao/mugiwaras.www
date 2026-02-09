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

    public static function pagedWithRelations(int $limit, int $offset): array
    {
        $sql = 'SELECT u.*, c.name AS category_name, s.name AS series_name, usr.username AS username_display
                FROM uploads u
                LEFT JOIN categories c ON c.id = u.category_id
                LEFT JOIN series s ON s.id = u.series_id
                LEFT JOIN users usr ON usr.id = u.user_id
                ORDER BY u.id DESC
                LIMIT :l OFFSET :o';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('o', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function pagedWithRelationsFiltered(int $limit, int $offset, array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = 'u.category_id = :category_id';
            $params['category_id'] = (int)$filters['category_id'];
        }
        if (!empty($filters['series_id'])) {
            $where[] = 'u.series_id = :series_id';
            $params['series_id'] = (int)$filters['series_id'];
        }
        if (!empty($filters['user_id'])) {
            $where[] = 'u.user_id = :user_id';
            $params['user_id'] = (int)$filters['user_id'];
        } elseif (!empty($filters['username'])) {
            $where[] = 'usr.username LIKE :username';
            $params['username'] = '%' . $filters['username'] . '%';
        }
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'failed') {
                $where[] = '(u.status = :status OR j.status = :job_status)';
                $params['status'] = 'failed';
                $params['job_status'] = 'failed';
            } else {
                $where[] = 'u.status = :status';
                $params['status'] = (string)$filters['status'];
            }
        }

        $sql = 'SELECT u.*, c.name AS category_name, s.name AS series_name, usr.username AS username_display, j.status AS job_status, j.error_message AS job_error
                FROM uploads u
                LEFT JOIN categories c ON c.id = u.category_id
                LEFT JOIN series s ON s.id = u.series_id
                LEFT JOIN users usr ON usr.id = u.user_id
                LEFT JOIN jobs j ON j.id = u.job_id';
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY u.id DESC LIMIT :l OFFSET :o';

        $stmt = Database::connection()->prepare($sql);
        foreach ($params as $k => $v) {
            if (is_int($v)) {
                $stmt->bindValue($k, $v, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($k, $v);
            }
        }
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

    public static function countFiltered(array $filters): int
    {
        $where = [];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = 'u.category_id = :category_id';
            $params['category_id'] = (int)$filters['category_id'];
        }
        if (!empty($filters['series_id'])) {
            $where[] = 'u.series_id = :series_id';
            $params['series_id'] = (int)$filters['series_id'];
        }
        if (!empty($filters['user_id'])) {
            $where[] = 'u.user_id = :user_id';
            $params['user_id'] = (int)$filters['user_id'];
        } elseif (!empty($filters['username'])) {
            $where[] = 'usr.username LIKE :username';
            $params['username'] = '%' . $filters['username'] . '%';
        }
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'failed') {
                $where[] = '(u.status = :status OR j.status = :job_status)';
                $params['status'] = 'failed';
                $params['job_status'] = 'failed';
            } else {
                $where[] = 'u.status = :status';
                $params['status'] = (string)$filters['status'];
            }
        }

        $sql = 'SELECT COUNT(*) AS c FROM uploads u LEFT JOIN users usr ON usr.id = u.user_id LEFT JOIN jobs j ON j.id = u.job_id';
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $stmt = Database::connection()->prepare($sql);
        foreach ($params as $k => $v) {
            if (is_int($v)) {
                $stmt->bindValue($k, $v, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($k, $v);
            }
        }
        $stmt->execute();
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function totalSizeAll(): int
    {
        $stmt = Database::connection()->query('SELECT COALESCE(SUM(file_size),0) AS s FROM uploads');
        $row = $stmt->fetch();
        return (int)($row['s'] ?? 0);
    }

    public static function totalSizeFiltered(array $filters): int
    {
        $where = [];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = 'u.category_id = :category_id';
            $params['category_id'] = (int)$filters['category_id'];
        }
        if (!empty($filters['series_id'])) {
            $where[] = 'u.series_id = :series_id';
            $params['series_id'] = (int)$filters['series_id'];
        }
        if (!empty($filters['user_id'])) {
            $where[] = 'u.user_id = :user_id';
            $params['user_id'] = (int)$filters['user_id'];
        } elseif (!empty($filters['username'])) {
            $where[] = 'usr.username LIKE :username';
            $params['username'] = '%' . $filters['username'] . '%';
        }
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'failed') {
                $where[] = '(u.status = :status OR j.status = :job_status)';
                $params['status'] = 'failed';
                $params['job_status'] = 'failed';
            } else {
                $where[] = 'u.status = :status';
                $params['status'] = (string)$filters['status'];
            }
        }

        $sql = 'SELECT COALESCE(SUM(u.file_size),0) AS s FROM uploads u LEFT JOIN users usr ON usr.id = u.user_id LEFT JOIN jobs j ON j.id = u.job_id';
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $stmt = Database::connection()->prepare($sql);
        foreach ($params as $k => $v) {
            if (is_int($v)) {
                $stmt->bindValue($k, $v, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($k, $v);
            }
        }
        $stmt->execute();
        $row = $stmt->fetch();
        return (int)($row['s'] ?? 0);
    }

    public static function countPending(): int
    {
        $sql = "SELECT COUNT(*) AS c
                FROM uploads u
                LEFT JOIN jobs j ON j.id = u.job_id
                WHERE u.status IN ('pending','queued','processing')
                   OR j.status IN ('pending','queued','processing')";
        try {
            $stmt = Database::connection()->query($sql);
            $row = $stmt->fetch();
            return (int)($row['c'] ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
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

    public static function pendingCountsBySeries(array $seriesIds): array
    {
        if (empty($seriesIds)) {
            return [];
        }
        $ids = array_values(array_map('intval', $seriesIds));
        $status = ['pending', 'queued', 'processing'];
        $idPlaceholders = implode(',', array_fill(0, count($ids), '?'));
        $statusPlaceholders = implode(',', array_fill(0, count($status), '?'));
        $sql = "SELECT series_id, COUNT(*) AS c
                FROM uploads
                WHERE series_id IN ($idPlaceholders) AND status IN ($statusPlaceholders)
                GROUP BY series_id";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(array_merge($ids, $status));
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[(int)$row['series_id']] = (int)$row['c'];
        }
        return $out;
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM uploads WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findWithJobStatus(int $id): ?array
    {
        $sql = 'SELECT u.*, j.status AS job_status
                FROM uploads u
                LEFT JOIN jobs j ON j.id = u.job_id
                WHERE u.id = :id
                LIMIT 1';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function setStatusByJob(int $jobId, string $status): void
    {
        $stmt = Database::connection()->prepare('UPDATE uploads SET status = :s, updated_at = NOW() WHERE job_id = :j');
        $stmt->execute(['s' => $status, 'j' => $jobId]);
    }

    public static function setStatus(int $id, string $status): void
    {
        $stmt = Database::connection()->prepare('UPDATE uploads SET status = :s, updated_at = NOW() WHERE id = :id');
        $stmt->execute(['s' => $status, 'id' => $id]);
    }

    public static function setJobId(int $id, ?int $jobId): void
    {
        $stmt = Database::connection()->prepare('UPDATE uploads SET job_id = :j, updated_at = NOW() WHERE id = :id');
        $stmt->execute(['j' => $jobId, 'id' => $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM uploads WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function failedIds(): array
    {
        $sql = "SELECT u.id
                FROM uploads u
                LEFT JOIN jobs j ON j.id = u.job_id
                WHERE u.status = 'failed' OR j.status = 'failed'";
        $stmt = Database::connection()->query($sql);
        $rows = $stmt->fetchAll();
        return array_map(static fn (array $row): int => (int)$row['id'], $rows);
    }

    public static function updateCategorySeries(int $id, ?int $categoryId, ?int $seriesId): void
    {
        $stmt = Database::connection()->prepare('UPDATE uploads SET category_id = :c, series_id = :s WHERE id = :id');
        $stmt->bindValue('id', $id, \PDO::PARAM_INT);
        if ($categoryId === null || $categoryId <= 0) {
            $stmt->bindValue('c', null, \PDO::PARAM_NULL);
        } else {
            $stmt->bindValue('c', $categoryId, \PDO::PARAM_INT);
        }
        if ($seriesId === null || $seriesId <= 0) {
            $stmt->bindValue('s', null, \PDO::PARAM_NULL);
        } else {
            $stmt->bindValue('s', $seriesId, \PDO::PARAM_INT);
        }
        $stmt->execute();
    }
}
