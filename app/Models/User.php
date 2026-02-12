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

    public static function nonStaff(int $limit = 500): array
    {
        $stmt = Database::connection()->prepare("SELECT * FROM users WHERE role = 'user' AND support_agent = 0 AND uploader_agent = 0 AND moderator_agent = 0 ORDER BY id DESC LIMIT :l");
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countNonStaff(): int
    {
        $stmt = Database::connection()->query("SELECT COUNT(*) AS c FROM users WHERE role = 'user' AND support_agent = 0 AND uploader_agent = 0 AND moderator_agent = 0");
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function countAll(): int
    {
        $stmt = Database::connection()->query("SELECT COUNT(*) AS c FROM users");
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function pagedNonStaff(int $page, int $perPage): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $stmt = Database::connection()->prepare("SELECT * FROM users WHERE role = 'user' AND support_agent = 0 AND uploader_agent = 0 AND moderator_agent = 0 ORDER BY id DESC LIMIT :l OFFSET :o");
        $stmt->bindValue('l', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('o', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function pagedAll(int $page, int $perPage): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $stmt = Database::connection()->prepare("SELECT * FROM users ORDER BY id DESC LIMIT :l OFFSET :o");
        $stmt->bindValue('l', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('o', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countFiltered(array $filters): int
    {
        $sql = 'SELECT COUNT(*) AS c FROM users u';
        $params = [];
        $conditions = [];
        $join = '';

        $package = trim((string)($filters['package'] ?? ''));
        if ($package !== '') {
            $join = " LEFT JOIN (SELECT user_id, MAX(id) AS id FROM payments WHERE status = 'approved' GROUP BY user_id) lp ON lp.user_id = u.id";
            $join .= ' LEFT JOIN payments p ON p.id = lp.id';
            if ($package === 'none') {
                $conditions[] = 'p.id IS NULL';
            } else {
                $pid = (int)$package;
                if ($pid > 0) {
                    $conditions[] = 'p.package_id = :pid';
                    $params['pid'] = $pid;
                }
            }
        }

        $q = trim((string)($filters['q'] ?? ''));
        if ($q !== '') {
            $like = '%' . $q . '%';
            $parts = ['u.username LIKE :q1', 'u.email LIKE :q2', 'u.phone LIKE :q3'];
            $params['q1'] = $like;
            $params['q2'] = $like;
            $params['q3'] = $like;
            if (ctype_digit($q)) {
                $parts[] = 'u.id = :qid';
                $params['qid'] = (int)$q;
            }
            $conditions[] = '(' . implode(' OR ', $parts) . ')';
        }

        $tier = trim((string)($filters['tier'] ?? ''));
        if ($tier !== '' && in_array($tier, ['user','trial','assinante','restrito','vitalicio'], true)) {
            $conditions[] = 'u.access_tier = :tier';
            $params['tier'] = $tier;
        }

        $status = trim((string)($filters['status'] ?? ''));
        if ($status === 'locked') {
            $conditions[] = 'u.lock_until IS NOT NULL AND u.lock_until > NOW()';
        } elseif ($status === 'active') {
            $conditions[] = '(u.lock_until IS NULL OR u.lock_until <= NOW())';
        }

        if (!empty($conditions)) {
            $sql .= $join . ' WHERE ' . implode(' AND ', $conditions);
        } else {
            $sql .= $join;
        }

        $stmt = Database::connection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function pagedFiltered(int $page, int $perPage, array $filters): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $sql = 'SELECT u.* FROM users u';
        $params = [];
        $conditions = [];
        $join = '';

        $package = trim((string)($filters['package'] ?? ''));
        if ($package !== '') {
            $join = " LEFT JOIN (SELECT user_id, MAX(id) AS id FROM payments WHERE status = 'approved' GROUP BY user_id) lp ON lp.user_id = u.id";
            $join .= ' LEFT JOIN payments p ON p.id = lp.id';
            if ($package === 'none') {
                $conditions[] = 'p.id IS NULL';
            } else {
                $pid = (int)$package;
                if ($pid > 0) {
                    $conditions[] = 'p.package_id = :pid';
                    $params['pid'] = $pid;
                }
            }
        }

        $q = trim((string)($filters['q'] ?? ''));
        if ($q !== '') {
            $like = '%' . $q . '%';
            $parts = ['u.username LIKE :q1', 'u.email LIKE :q2', 'u.phone LIKE :q3'];
            $params['q1'] = $like;
            $params['q2'] = $like;
            $params['q3'] = $like;
            if (ctype_digit($q)) {
                $parts[] = 'u.id = :qid';
                $params['qid'] = (int)$q;
            }
            $conditions[] = '(' . implode(' OR ', $parts) . ')';
        }

        $tier = trim((string)($filters['tier'] ?? ''));
        if ($tier !== '' && in_array($tier, ['user','trial','assinante','restrito','vitalicio'], true)) {
            $conditions[] = 'u.access_tier = :tier';
            $params['tier'] = $tier;
        }

        $status = trim((string)($filters['status'] ?? ''));
        if ($status === 'locked') {
            $conditions[] = 'u.lock_until IS NOT NULL AND u.lock_until > NOW()';
        } elseif ($status === 'active') {
            $conditions[] = '(u.lock_until IS NULL OR u.lock_until <= NOW())';
        }

        if (!empty($conditions)) {
            $sql .= $join . ' WHERE ' . implode(' AND ', $conditions);
        } else {
            $sql .= $join;
        }

        $sql .= ' ORDER BY u.id DESC LIMIT :l OFFSET :o';
        $stmt = Database::connection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue('l', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('o', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function byRole(string $role, int $limit = 500): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE role = :r ORDER BY id DESC LIMIT :l');
        $stmt->bindValue('r', $role);
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function moderators(int $limit = 500): array
    {
        $stmt = Database::connection()->prepare("SELECT * FROM users WHERE role = 'equipe' AND moderator_agent = 1 ORDER BY id DESC LIMIT :l");
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function uploaders(int $limit = 500): array
    {
        $stmt = Database::connection()->prepare("SELECT * FROM users WHERE role = 'equipe' AND uploader_agent = 1 ORDER BY id DESC LIMIT :l");
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function supportAgents(int $limit = 500): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE support_agent = 1 ORDER BY id DESC LIMIT :l');
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function teamMembers(int $limit = 1000): array
    {
        $stmt = Database::connection()->prepare("SELECT * FROM users WHERE role IN ('admin','equipe','superadmin') OR support_agent = 1 OR uploader_agent = 1 OR moderator_agent = 1 ORDER BY FIELD(role,'superadmin','admin','equipe'), id DESC LIMIT :l");
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

    public static function searchByLastIp(string $ip, int $limit = 200): array
    {
        $query = trim($ip);
        if ($query === '') {
            return [];
        }
        $stmt = Database::connection()->prepare(
            'SELECT id, username, email, ip_ultimo_acesso, data_ultimo_login FROM users WHERE ip_ultimo_acesso LIKE :ip ORDER BY data_ultimo_login DESC, id DESC LIMIT :l'
        );
        $stmt->bindValue('ip', '%' . $query . '%');
        $stmt->bindValue('l', max(1, min(500, $limit)), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findForRecovery(string $username, string $email, string $birthDate, string $phone): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE username = :u AND email = :e LIMIT 1');
        $stmt->execute([
            'u' => mb_strtolower(trim($username)),
            'e' => mb_strtolower(trim($email)),
        ]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $storedBirth = self::normalizeBirthDate((string)($row['birth_date'] ?? ''));
        $inputBirth = self::normalizeBirthDate($birthDate);
        if ($storedBirth === null || $inputBirth === null || $storedBirth !== $inputBirth) {
            return null;
        }

        $storedPhone = self::normalizePhone((string)($row['phone'] ?? ''));
        $inputPhone = self::normalizePhone($phone);
        if ($storedPhone === '' || $inputPhone === '' || $storedPhone !== $inputPhone) {
            return null;
        }

        return $row;
    }

    private static function normalizePhone(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    private static function normalizeBirthDate(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'Y/m/d'];
        foreach ($formats as $format) {
            $dt = \DateTimeImmutable::createFromFormat($format, $value);
            if ($dt !== false && $dt->format($format) === $value) {
                return $dt->format('Y-m-d');
            }
        }
        try {
            return (new \DateTimeImmutable($value))->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
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

    public static function updateProfile(int $id, array $data): void
    {
        $sql = 'UPDATE users SET username = :username, email = :email, phone = :phone, phone_country = :phone_country, phone_has_whatsapp = :phone_has_whatsapp, birth_date = :birth_date, observations = :observations, access_tier = :access_tier WHERE id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'phone_country' => $data['phone_country'],
            'phone_has_whatsapp' => $data['phone_has_whatsapp'],
            'birth_date' => $data['birth_date'],
            'observations' => $data['observations'],
            'access_tier' => $data['access_tier'],
            'id' => $id,
        ]);
    }

    public static function updateProfileSelf(int $id, array $data): void
    {
        $sql = 'UPDATE users SET username = :username, email = :email, phone = :phone, phone_country = :phone_country, phone_has_whatsapp = :phone_has_whatsapp, birth_date = :birth_date, avatar_path = :avatar_path WHERE id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'phone_country' => $data['phone_country'],
            'phone_has_whatsapp' => $data['phone_has_whatsapp'],
            'birth_date' => $data['birth_date'],
            'avatar_path' => $data['avatar_path'],
            'id' => $id,
        ]);
    }

    public static function setAccessTier(int $id, string $tier): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET access_tier = :t WHERE id = :id');
        $stmt->execute(['t' => $tier, 'id' => $id]);
    }

    public static function setSubscriptionExpiresAt(int $id, ?string $expiresAt): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET subscription_expires_at = :exp WHERE id = :id');
        $stmt->execute(['exp' => $expiresAt, 'id' => $id]);
    }

    public static function updateRoleFlags(int $id, string $role, int $supportAgent, int $uploaderAgent, int $moderatorAgent): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET role = :r, support_agent = :s, uploader_agent = :u, moderator_agent = :m WHERE id = :id');
        $stmt->execute([
            'r' => $role,
            's' => $supportAgent,
            'u' => $uploaderAgent,
            'm' => $moderatorAgent,
            'id' => $id,
        ]);
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

    public static function removeCredits(int $id, int $credits): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET credits = GREATEST(credits - :c, 0) WHERE id = :id');
        $stmt->execute(['c' => $credits, 'id' => $id]);
    }

    public static function extendSubscription(int $id, int $days): void
    {
        $stmt = Database::connection()->prepare('SELECT subscription_expires_at FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        $current = $row['subscription_expires_at'] ?? null;
        $base = new \DateTimeImmutable('now');
        if (!empty($current)) {
            try {
                $currentDt = new \DateTimeImmutable((string)$current);
                if ($currentDt > $base) {
                    $base = $currentDt;
                }
            } catch (\Exception $e) {
                // fallback to now
            }
        }
        $newDate = $base->modify('+' . max(0, $days) . ' days');
        $update = Database::connection()->prepare('UPDATE users SET subscription_expires_at = :exp WHERE id = :id');
        $update->execute(['exp' => $newDate->format('Y-m-d H:i:s'), 'id' => $id]);
    }

    public static function recentLogins(int $limit = 10): array
    {
        $stmt = Database::connection()->prepare('SELECT id, username, email, access_tier, role, data_ultimo_login, data_registro FROM users ORDER BY COALESCE(data_ultimo_login, data_registro) DESC, id DESC LIMIT :l');
        $stmt->bindValue('l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
