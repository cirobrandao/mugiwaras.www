<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class User
{
    public static function all(int $limit = 200): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users ORDER BY id DESC LIMIT :l');
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByUsername(string $username): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE username = :u');
        $stmt->execute(['u' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE email = :e');
        $stmt->execute(['e' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $sql = 'INSERT INTO users (username,email,phone,phone_country,phone_has_whatsapp,birth_date,password_hash,access_tier,role,referral_code,referrer_id,ip_cadastro,ip_ultimo_acesso,ip_penultimo_acesso,data_registro) VALUES (:username,:email,:phone,:phone_country,:phone_has_whatsapp,:birth_date,:password_hash,:access_tier,:role,:referral_code,:referrer_id,:ip_cadastro,:ip_ultimo_acesso,:ip_penultimo_acesso,NOW())';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($data);
        return (int)Database::connection()->lastInsertId();
    }

    public static function incrementFailedLogins(int $id): void
    {
        $sql = 'UPDATE users SET tentativas_login = tentativas_login + 1, lock_until = IF(tentativas_login + 1 >= 5, DATE_ADD(NOW(), INTERVAL 15 MINUTE), lock_until) WHERE id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    public static function resetFailedLogins(int $id): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET tentativas_login = 0, lock_until = NULL WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function updateLastLogin(int $id, string $ip, string $ua): void
    {
        $sql = 'UPDATE users SET ip_penultimo_acesso = ip_ultimo_acesso, ip_ultimo_acesso = :ip, data_ultimo_login = NOW(), user_agent_ultimo_login = :ua WHERE id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id, 'ip' => $ip, 'ua' => $ua]);
    }

    public static function countSuperadmins(): int
    {
        $stmt = Database::connection()->query("SELECT COUNT(*) AS c FROM users WHERE role = 'superadmin'");
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function updateRoleTier(int $id, string $role, string $tier): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET role = :r, access_tier = :t WHERE id = :id');
        $stmt->execute(['r' => $role, 't' => $tier, 'id' => $id]);
    }

    public static function setLockUntil(int $id, ?string $until): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET lock_until = :u WHERE id = :id');
        $stmt->execute(['u' => $until, 'id' => $id]);
    }

    public static function addCredits(int $id, int $credits): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET credits = credits + :c WHERE id = :id');
        $stmt->execute(['c' => $credits, 'id' => $id]);
    }

    public static function extendSubscription(int $id, int $days): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE users SET subscription_expires_at = IF(subscription_expires_at IS NULL OR subscription_expires_at < NOW(), DATE_ADD(NOW(), INTERVAL :d DAY), DATE_ADD(subscription_expires_at, INTERVAL :d DAY)) WHERE id = :id'
        );
        $stmt->execute(['d' => $days, 'id' => $id]);
    }

    public static function recentLogins(int $limit = 10): array
    {
        $stmt = Database::connection()->prepare('SELECT id, username, email, access_tier, role, data_ultimo_login, data_registro FROM users ORDER BY COALESCE(data_ultimo_login, data_registro) DESC, id DESC LIMIT :l');
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
