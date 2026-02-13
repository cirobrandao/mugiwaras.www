<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class LoginHistory
{
    public static function record(int $userId, string $ip, string $ua): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO login_history (user_id, ip_address, user_agent, logged_at) VALUES (:u,:ip,:ua,NOW())');
        $stmt->execute([
            'u' => $userId,
            'ip' => $ip,
            'ua' => $ua,
        ]);
    }

    public static function forUsers(array $userIds, int $limit = 10): array
    {
        $clean = array_values(array_unique(array_filter(array_map('intval', $userIds), static fn ($v) => $v > 0)));
        if (empty($clean)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($clean), '?'));
        $stmt = Database::connection()->prepare(
            'SELECT user_id, ip_address, user_agent, logged_at
             FROM login_history
             WHERE user_id IN (' . $placeholders . ')
             ORDER BY logged_at DESC'
        );
        $stmt->execute($clean);
        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $row) {
            $uid = (int)($row['user_id'] ?? 0);
            if ($uid <= 0) {
                continue;
            }
            if (!isset($map[$uid])) {
                $map[$uid] = [];
            }
            if (count($map[$uid]) >= $limit) {
                continue;
            }
            $map[$uid][] = $row;
        }
        return $map;
    }

    public static function forUser(int $userId, int $limit = 20): array
    {
        if ($userId <= 0) {
            return [];
        }
        $stmt = Database::connection()->prepare(
            'SELECT ip_address, user_agent, logged_at
             FROM login_history
             WHERE user_id = :u
             ORDER BY logged_at DESC
             LIMIT :l'
        );
        $stmt->bindValue('u', $userId, \PDO::PARAM_INT);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function forUserAll(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }
        $stmt = Database::connection()->prepare(
            'SELECT ip_address, user_agent, logged_at
             FROM login_history
             WHERE user_id = :u
             ORDER BY logged_at DESC'
        );
        $stmt->bindValue('u', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countAccessLogs(string $query = ''): int
    {
        $sql = 'SELECT COUNT(*) AS c FROM login_history lh INNER JOIN users u ON u.id = lh.user_id';
        $params = [];
        $where = self::searchWhere($query, $params);
        if ($where !== '') {
            $sql .= ' WHERE ' . $where;
        }
        $stmt = Database::connection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function accessLogs(string $query = '', int $page = 1, int $perPage = 100): array
    {
        $page = max(1, $page);
        $perPage = max(10, min(500, $perPage));
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT lh.id, lh.user_id, lh.ip_address, lh.user_agent, lh.logged_at, u.username, u.email, u.role, u.access_tier FROM login_history lh INNER JOIN users u ON u.id = lh.user_id';
        $params = [];
        $where = self::searchWhere($query, $params);
        if ($where !== '') {
            $sql .= ' WHERE ' . $where;
        }
        $sql .= ' ORDER BY lh.logged_at DESC, lh.id DESC LIMIT :l OFFSET :o';

        $stmt = Database::connection()->prepare($sql);
        foreach ($params as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue(':' . $key, $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':' . $key, $value);
            }
        }
        $stmt->bindValue('l', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('o', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private static function searchWhere(string $query, array &$params): string
    {
        $query = trim($query);
        if ($query === '') {
            return '';
        }
        $params['q1'] = '%' . $query . '%';
        $params['q2'] = '%' . $query . '%';
        $params['q3'] = '%' . $query . '%';
        $params['q4'] = '%' . $query . '%';
        $parts = [
            'u.username LIKE :q1',
            'u.email LIKE :q2',
            'lh.ip_address LIKE :q3',
            'lh.user_agent LIKE :q4',
        ];
        if (ctype_digit($query)) {
            $params['uid'] = (int)$query;
            $params['lid'] = (int)$query;
            $parts[] = 'lh.user_id = :uid';
            $parts[] = 'lh.id = :lid';
        }
        return '(' . implode(' OR ', $parts) . ')';
    }
}