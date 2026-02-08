<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\User;
use App\Models\Payment;
use App\Models\Upload;
use App\Models\SupportMessage;
use App\Models\Category;
use App\Models\Package;
use App\Models\AuditLog;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $stats = [
            'users_total' => User::countAll(),
            'users_nonstaff' => User::countNonStaff(),
            'payments_pending' => Payment::countPending(),
            'uploads_pending' => Upload::countPending(),
            'support_open' => SupportMessage::countOpenForStaff(),
            'categories_total' => count(Category::all()),
            'packages_total' => count(Package::all()),
            'series_total' => $this->countTable('series'),
            'content_total' => $this->countTable('content_items'),
        ];

        $charts = [
            'payments_by_month' => $this->paymentsByMonth(6),
            'uploads_by_week' => $this->uploadsByWeek(6),
        ];

        $rootPath = dirname(__DIR__, 3);
        $diskTotal = @disk_total_space($rootPath);
        $diskFree = @disk_free_space($rootPath);
        $server = [
            'php_version' => PHP_VERSION,
            'os' => php_uname('s') . ' ' . php_uname('r'),
            'server_software' => (string)($_SERVER['SERVER_SOFTWARE'] ?? 'N/A'),
            'memory_limit' => (string)(ini_get('memory_limit') ?: 'N/A'),
            'memory_usage' => memory_get_usage(true),
            'disk_total' => $diskTotal !== false ? (int)$diskTotal : 0,
            'disk_free' => $diskFree !== false ? (int)$diskFree : 0,
            'time' => date('Y-m-d H:i:s'),
        ];

        $dbInfo = $this->databaseInfo();
        $loginFailAttempts = AuditLog::recentLoginFails(10);

        echo $this->view('admin/dashboard', [
            'stats' => $stats,
            'server' => $server,
            'charts' => $charts,
            'dbInfo' => $dbInfo,
            'loginFailAttempts' => $loginFailAttempts,
        ]);
    }

    private function countTable(string $table): int
    {
        try {
            $stmt = Database::connection()->query('SELECT COUNT(*) AS c FROM ' . $table);
            $row = $stmt->fetch();
            return (int)($row['c'] ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function paymentsByMonth(int $months): array
    {
        $months = max(1, min(24, $months));
        $start = (new \DateTimeImmutable('first day of this month'))->modify('-' . ($months - 1) . ' months');
        $labels = [];
        $map = [];
        for ($i = 0; $i < $months; $i++) {
            $key = $start->modify('+' . $i . ' months')->format('Y-m');
            $labels[] = $key;
            $map[$key] = 0;
        }

        try {
            $stmt = Database::connection()->prepare(
                "SELECT DATE_FORMAT(p.created_at, '%Y-%m') AS ym, COALESCE(SUM(pk.price * p.months), 0) AS total\n"
                . "FROM payments p\n"
                . "LEFT JOIN packages pk ON pk.id = p.package_id\n"
                . "WHERE p.status = 'approved' AND p.created_at >= :start\n"
                . "GROUP BY ym\n"
                . "ORDER BY ym ASC"
            );
            $stmt->execute(['start' => $start->format('Y-m-01 00:00:00')]);
            foreach ($stmt->fetchAll() as $row) {
                $key = (string)($row['ym'] ?? '');
                if ($key !== '' && array_key_exists($key, $map)) {
                    $map[$key] = (float)($row['total'] ?? 0);
                }
            }
        } catch (\Throwable $e) {
            // keep zeros
        }

        return array_map(static function ($key) use ($map) {
            return ['label' => $key, 'value' => $map[$key] ?? 0];
        }, $labels);
    }

    private function uploadsByWeek(int $weeks): array
    {
        $weeks = max(1, min(24, $weeks));
        $start = (new \DateTimeImmutable('monday this week'))->modify('-' . ($weeks - 1) . ' weeks');
        $labels = [];
        $map = [];
        for ($i = 0; $i < $weeks; $i++) {
            $weekStart = $start->modify('+' . $i . ' weeks');
            $key = $weekStart->format('o-\WW');
            $labels[] = $key;
            $map[$key] = 0;
        }

        try {
            $stmt = Database::connection()->prepare(
                "SELECT DATE_FORMAT(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY), '%x-\\W%v') AS yw, COUNT(*) AS c\n"
                . "FROM uploads\n"
                . "WHERE created_at >= :start\n"
                . "GROUP BY yw\n"
                . "ORDER BY yw ASC"
            );
            $stmt->execute(['start' => $start->format('Y-m-d 00:00:00')]);
            foreach ($stmt->fetchAll() as $row) {
                $key = (string)($row['yw'] ?? '');
                if ($key !== '' && array_key_exists($key, $map)) {
                    $map[$key] = (int)($row['c'] ?? 0);
                }
            }
        } catch (\Throwable $e) {
            // keep zeros
        }

        return array_map(static function ($key) use ($map) {
            return ['label' => $key, 'value' => $map[$key] ?? 0];
        }, $labels);
    }

    private function databaseInfo(): array
    {
        $info = [
            'version' => 'N/A',
            'name' => 'N/A',
            'connections' => 0,
        ];
        try {
            $row = Database::connection()->query('SELECT VERSION() AS v, DATABASE() AS d')->fetch();
            if ($row) {
                $info['version'] = (string)($row['v'] ?? 'N/A');
                $info['name'] = (string)($row['d'] ?? 'N/A');
            }
            $status = Database::connection()->query("SHOW STATUS LIKE 'Threads_connected'")->fetch();
            if ($status && isset($status['Value'])) {
                $info['connections'] = (int)$status['Value'];
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return $info;
    }
}
