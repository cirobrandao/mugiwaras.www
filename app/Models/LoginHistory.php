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

    /**
     * Retorna os dispositivos mais utilizados por usuários normais (exclui admin, superadmin, equipe)
     * 
     * @param int $limit Número máximo de dispositivos a retornar
     * @param int $days Período em dias para análise (padrão: 30)
     * @return array Array com ['device' => string, 'count' => int, 'icon' => string]
     */
    public static function topDevices(int $limit = 10, int $days = 30): array
    {
        $limit = max(1, min(50, $limit));
        $days = max(1, min(365, $days));
        
        try {
            $sql = "SELECT lh.user_agent, COUNT(*) AS c 
                    FROM login_history lh
                    INNER JOIN users u ON u.id = lh.user_id
                    WHERE lh.logged_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                    AND u.role NOT IN ('admin', 'superadmin', 'equipe')
                    AND lh.user_agent IS NOT NULL
                    AND lh.user_agent != ''
                    GROUP BY lh.user_agent
                    ORDER BY c DESC
                    LIMIT :limit";
            
            $stmt = Database::connection()->prepare($sql);
            $stmt->bindValue('days', $days, \PDO::PARAM_INT);
            $stmt->bindValue('limit', $limit * 3, \PDO::PARAM_INT); // Buscar mais para agregar depois
            $stmt->execute();
            $rows = $stmt->fetchAll();
            
            // Agregar por tipo de dispositivo
            $devices = [];
            foreach ($rows as $row) {
                $ua = (string)($row['user_agent'] ?? '');
                $count = (int)($row['c'] ?? 0);
                
                if ($ua === '' || $count <= 0) {
                    continue;
                }
                
                $deviceInfo = self::parseUserAgent($ua);
                $deviceKey = $deviceInfo['device'] . '|' . $deviceInfo['platform'];
                
                if (!isset($devices[$deviceKey])) {
                    $devices[$deviceKey] = [
                        'device' => $deviceInfo['display'],
                        'count' => 0,
                        'icon' => $deviceInfo['icon'],
                    ];
                }
                $devices[$deviceKey]['count'] += $count;
            }
            
            // Ordenar por contagem
            uasort($devices, static fn($a, $b) => $b['count'] <=> $a['count']);
            
            // Retornar apenas o limite solicitado
            return array_slice(array_values($devices), 0, $limit);
            
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Parseia o user agent para identificar tipo de dispositivo e plataforma
     */
    private static function parseUserAgent(string $ua): array
    {
        $ua = strtolower($ua);
        
        // Detectar tipo de dispositivo
        $isMobile = preg_match('/(mobile|android|iphone|ipod|blackberry|iemobile|opera mini)/i', $ua);
        $isTablet = preg_match('/(ipad|tablet|kindle|playbook|silk)/i', $ua);
        
        // Detectar plataforma/SO
        $platform = 'Outro';
        $icon = 'bi-laptop';
        
        if (str_contains($ua, 'windows')) {
            $platform = 'Windows';
            $icon = $isMobile ? 'bi-phone' : ($isTablet ? 'bi-tablet' : 'bi-windows');
        } elseif (str_contains($ua, 'android')) {
            $platform = 'Android';
            $icon = $isTablet ? 'bi-tablet' : 'bi-phone';
        } elseif (str_contains($ua, 'iphone') || str_contains($ua, 'ipad') || str_contains($ua, 'ipod')) {
            $platform = str_contains($ua, 'ipad') ? 'iPad' : 'iPhone';
            $icon = str_contains($ua, 'ipad') ? 'bi-tablet' : 'bi-phone';
        } elseif (str_contains($ua, 'mac')) {
            $platform = 'macOS';
            $icon = 'bi-laptop';
        } elseif (str_contains($ua, 'linux')) {
            $platform = 'Linux';
            $icon = 'bi-laptop';
        }
        
        // Determinar tipo base
        $type = 'Desktop';
        if ($isTablet) {
            $type = 'Tablet';
        } elseif ($isMobile) {
            $type = 'Mobile';
        }
        
        $display = $platform;
        if ($type !== 'Desktop') {
            $display = $platform . ' ' . $type;
        }
        
        return [
            'device' => $type,
            'platform' => $platform,
            'display' => $display,
            'icon' => $icon,
        ];
    }

    /**
     * Retorna os navegadores mais utilizados por usuários normais (exclui admin, superadmin, equipe)
     * 
     * @param int $limit Número máximo de navegadores a retornar
     * @param int $days Período em dias para análise (padrão: 30)
     * @return array Array com ['browser' => string, 'count' => int, 'icon' => string]
     */
    public static function topBrowsers(int $limit = 10, int $days = 30): array
    {
        $limit = max(1, min(50, $limit));
        $days = max(1, min(365, $days));
        
        try {
            $sql = "SELECT lh.user_agent, COUNT(*) AS c 
                    FROM login_history lh
                    INNER JOIN users u ON u.id = lh.user_id
                    WHERE lh.logged_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                    AND u.role NOT IN ('admin', 'superadmin', 'equipe')
                    AND lh.user_agent IS NOT NULL
                    AND lh.user_agent != ''
                    GROUP BY lh.user_agent
                    ORDER BY c DESC
                    LIMIT :limit";
            
            $stmt = Database::connection()->prepare($sql);
            $stmt->bindValue('days', $days, \PDO::PARAM_INT);
            $stmt->bindValue('limit', $limit * 3, \PDO::PARAM_INT); // Buscar mais para agregar depois
            $stmt->execute();
            $rows = $stmt->fetchAll();
            
            // Agregar por navegador
            $browsers = [];
            foreach ($rows as $row) {
                $ua = (string)($row['user_agent'] ?? '');
                $count = (int)($row['c'] ?? 0);
                
                if ($ua === '' || $count <= 0) {
                    continue;
                }
                
                $browserInfo = self::parseBrowser($ua);
                $browserKey = $browserInfo['browser'];
                
                if (!isset($browsers[$browserKey])) {
                    $browsers[$browserKey] = [
                        'browser' => $browserInfo['display'],
                        'count' => 0,
                        'icon' => $browserInfo['icon'],
                    ];
                }
                $browsers[$browserKey]['count'] += $count;
            }
            
            // Ordenar por contagem
            uasort($browsers, static fn($a, $b) => $b['count'] <=> $a['count']);
            
            // Retornar apenas o limite solicitado
            return array_slice(array_values($browsers), 0, $limit);
            
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Parseia o user agent para identificar o navegador
     */
    private static function parseBrowser(string $ua): array
    {
        $ua = strtolower($ua);
        
        $browser = 'Outro';
        $icon = 'bi-browser-chrome';
        
        // Detectar navegador (ordem importa!)
        if (str_contains($ua, 'edg/') || str_contains($ua, 'edge/')) {
            $browser = 'Edge';
            $icon = 'bi-browser-edge';
        } elseif (str_contains($ua, 'opr/') || str_contains($ua, 'opera/')) {
            $browser = 'Opera';
            $icon = 'bi-browser-chrome'; // Bootstrap não tem ícone específico do Opera
        } elseif (str_contains($ua, 'chrome/') || str_contains($ua, 'crios/')) {
            $browser = 'Chrome';
            $icon = 'bi-browser-chrome';
        } elseif (str_contains($ua, 'safari/') && !str_contains($ua, 'chrome')) {
            $browser = 'Safari';
            $icon = 'bi-browser-safari';
        } elseif (str_contains($ua, 'firefox/') || str_contains($ua, 'fxios/')) {
            $browser = 'Firefox';
            $icon = 'bi-browser-firefox';
        } elseif (str_contains($ua, 'msie') || str_contains($ua, 'trident/')) {
            $browser = 'Internet Explorer';
            $icon = 'bi-browser-edge';
        }
        
        return [
            'browser' => $browser,
            'display' => $browser,
            'icon' => $icon,
        ];
    }
}