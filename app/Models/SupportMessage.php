<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class SupportMessage
{
    private static ?array $columnsCache = null;

    public static function create(array $data): array
    {
        $pdo = Database::connection();

        $columns = self::columns();
        if (!empty($columns)) {
            $map = [
                'user_id' => 'uid',
                'email' => 'email',
                'subject' => 'subject',
                'message' => 'message',
                'ip_address' => 'ip',
                'attachment_path' => 'ap',
                'attachment_name' => 'an',
                'status' => 'status',
                'public_token' => 'token',
                'whatsapp_opt_in' => 'wopt',
                'whatsapp_number' => 'wnum',
            ];
            $cols = [];
            $params = [];
            $values = [];
            foreach ($map as $col => $key) {
                if (in_array($col, $columns, true)) {
                    $cols[] = $col;
                    $values[] = ':' . $key;
                    $params[$key] = $data[$key] ?? null;
                }
            }
            if (in_array('created_at', $columns, true)) {
                $cols[] = 'created_at';
                $values[] = 'NOW()';
            }

            if (!empty($cols)) {
                try {
                    $sql = 'INSERT INTO support_messages (' . implode(',', $cols) . ') VALUES (' . implode(',', $values) . ')';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    return [
                        'id' => (int)$pdo->lastInsertId(),
                        'tokenStored' => in_array('public_token', $columns, true) && !empty($data['token']),
                    ];
                } catch (\Throwable $e) {
                    // fallback below
                }
            }
        }

        $queries = [
            [
                'sql' => 'INSERT INTO support_messages (user_id,email,subject,message,ip_address,attachment_path,attachment_name,status,public_token,created_at) VALUES (:uid,:email,:subject,:message,:ip,:ap,:an,:status,:token,NOW())',
                'params' => ['uid','email','subject','message','ip','ap','an','status','token'],
                'tokenStored' => true,
            ],
            [
                'sql' => 'INSERT INTO support_messages (user_id,email,subject,message,ip_address,attachment_path,attachment_name,created_at) VALUES (:uid,:email,:subject,:message,:ip,:ap,:an,NOW())',
                'params' => ['uid','email','subject','message','ip','ap','an'],
                'tokenStored' => false,
            ],
            [
                'sql' => 'INSERT INTO support_messages (user_id,email,subject,message,ip_address,created_at) VALUES (:uid,:email,:subject,:message,:ip,NOW())',
                'params' => ['uid','email','subject','message','ip'],
                'tokenStored' => false,
            ],
        ];

        foreach ($queries as $q) {
            try {
                $stmt = $pdo->prepare($q['sql']);
                $params = [];
                foreach ($q['params'] as $key) {
                    $params[$key] = $data[$key] ?? null;
                }
                $stmt->execute($params);
                return [
                    'id' => (int)$pdo->lastInsertId(),
                    'tokenStored' => $q['tokenStored'] && !empty($data['token']),
                ];
            } catch (\Throwable $e) {
                continue;
            }
        }

        return ['id' => 0, 'tokenStored' => false];
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM support_messages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByToken(string $token): ?array
    {
        try {
            $stmt = Database::connection()->prepare('SELECT * FROM support_messages WHERE public_token = :t');
            $stmt->execute(['t' => $token]);
            $row = $stmt->fetch();
            if ($row) {
                return $row;
            }
        } catch (\Throwable $e) {
            // fallback below
        }

        $fallback = self::readTokenFallback($token);
        if ($fallback && !empty($fallback['id'])) {
            $row = self::find((int)$fallback['id']);
            if ($row) {
                $row['public_token'] = $token;
                return $row;
            }
        }
        return null;
    }

    public static function setPublicToken(int $id, string $token): bool
    {
        try {
            $columns = self::columns();
            if (!empty($columns) && !in_array('public_token', $columns, true)) {
                return self::storeTokenFallback($id, $token);
            }
            $stmt = Database::connection()->prepare('UPDATE support_messages SET public_token = :t WHERE id = :id');
            $stmt->execute(['t' => $token, 'id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (\Throwable $e) {
            return self::storeTokenFallback($id, $token);
        }
    }

    private static function columns(): array
    {
        if (self::$columnsCache !== null) {
            return self::$columnsCache;
        }
        try {
            $stmt = Database::connection()->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'support_messages'");
            $cols = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            self::$columnsCache = $cols ?: [];
            return self::$columnsCache;
        } catch (\Throwable $e) {
            self::$columnsCache = [];
            return self::$columnsCache;
        }
    }

    private static function storeTokenFallback(int $id, string $token): bool
    {
        if ($id <= 0 || $token === '') {
            return false;
        }
        $dir = self::tokenDir();
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $path = $dir . '/' . $token . '.json';
        $payload = ['id' => $id, 'created_at' => date('c')];
        return (bool)file_put_contents($path, json_encode($payload));
    }

    private static function readTokenFallback(string $token): ?array
    {
        $path = self::tokenDir() . '/' . $token . '.json';
        if (!file_exists($path)) {
            return null;
        }
        $data = json_decode((string)file_get_contents($path), true);
        return is_array($data) ? $data : null;
    }

    private static function tokenDir(): string
    {
        return dirname(__DIR__, 2) . '/storage/support_tokens';
    }

    public static function byUser(int $userId, int $limit = 50): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM support_messages WHERE user_id = :uid ORDER BY id DESC LIMIT :l');
        $stmt->bindValue('uid', $userId, \PDO::PARAM_INT);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function latest(int $limit = 50): array
    {
        $stmt = Database::connection()->prepare('SELECT sm.*, u.username FROM support_messages sm LEFT JOIN users u ON u.id = sm.user_id ORDER BY sm.id DESC LIMIT :l');
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function setStatus(int $id, string $status): void
    {
        $stmt = Database::connection()->prepare('UPDATE support_messages SET status = :s, updated_at = NOW() WHERE id = :id');
        $stmt->execute(['s' => $status, 'id' => $id]);
    }

    public static function addNote(int $id, string $note): void
    {
        $stmt = Database::connection()->prepare('UPDATE support_messages SET admin_note = :n, updated_at = NOW() WHERE id = :id');
        $stmt->execute(['n' => $note, 'id' => $id]);
    }

    public static function countOpenForStaff(): int
    {
        $sql = "SELECT COUNT(*) AS c
                FROM support_messages sm
                LEFT JOIN support_replies sr ON sr.id = (
                    SELECT sr2.id FROM support_replies sr2 WHERE sr2.support_id = sm.id ORDER BY sr2.id DESC LIMIT 1
                )
                WHERE sm.status <> 'closed' AND (sr.id IS NULL OR sr.user_id IS NOT NULL)";
        try {
            $stmt = Database::connection()->query($sql);
            $row = $stmt->fetch();
            return (int)($row['c'] ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
