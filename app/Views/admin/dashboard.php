<?php
use App\Core\Auth;
use App\Core\View;
use App\Models\User;
ob_start();

$user = Auth::user();
$role = $user['role'] ?? 'user';
$isAdmin = \App\Core\Auth::isAdmin($user);
$isModerator = \App\Core\Auth::isModerator($user);
$stats = (array)($stats ?? []);
$server = (array)($server ?? []);
$charts = (array)($charts ?? []);
$dbInfo = (array)($dbInfo ?? []);
$loginFailAttempts = (array)($loginFailAttempts ?? []);
$formatNumber = static function (int $value): string {
    return number_format($value, 0, ',', '.');
};
$formatBytes = static function (int $bytes): string {
    if ($bytes <= 0) {
        return '0 B';
    }
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $pow = (int)floor(log($bytes, 1024));
    $pow = min($pow, count($units) - 1);
    $value = $bytes / (1024 ** $pow);
    return number_format($value, $value >= 100 ? 0 : 1, ',', '.') . ' ' . $units[$pow];
};
$parseNumber = static function (string $value): float {
    $value = trim($value);
    if ($value === '') {
        return 0.0;
    }
    if (is_numeric($value)) {
        return (float)$value;
    }
    $clean = preg_replace('/[^0-9,.-]/', '', $value) ?? '';
    if ($clean === '') {
        return 0.0;
    }
    if (str_contains($clean, '.') && !str_contains($clean, ',')) {
        $parts = explode('.', $clean);
        $last = end($parts);
        $validThousands = $last !== false && strlen((string)$last) === 3;
        foreach (array_slice($parts, 1) as $part) {
            if (strlen($part) !== 3) {
                $validThousands = false;
                break;
            }
        }
        if ($validThousands) {
            $clean = str_replace('.', '', $clean);
        }
    }
    if (str_contains($clean, ',') && str_contains($clean, '.')) {
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);
    } elseif (str_contains($clean, ',')) {
        $clean = str_replace(',', '.', $clean);
    }
    return (float)$clean;
};
$parseBytes = static function (string $value): int {
    $value = strtolower(str_replace(' ', '', trim($value)));
    if ($value === '' || $value === '-1' || $value === 'n/a') {
        return 0;
    }
    if (ctype_digit($value)) {
        return (int)$value;
    }
    if (!preg_match('/^(\d+(?:\.\d+)?)(k|kb|kib|m|mb|mib|g|gb|gib|t|tb|tib)?$/', $value, $matches)) {
        return 0;
    }
    $number = (float)$matches[1];
    $unit = $matches[2] ?? '';
    $mult = match ($unit) {
        'k', 'kb', 'kib' => 1024,
        'm', 'mb', 'mib' => 1024 ** 2,
        'g', 'gb', 'gib' => 1024 ** 3,
        't', 'tb', 'tib' => 1024 ** 4,
        default => 1,
    };
    return (int)round($number * $mult);
};
$memLimitBytes = $parseBytes((string)($server['memory_limit'] ?? ''));
$memUsage = (int)($server['memory_usage'] ?? 0);
$memPercent = $memLimitBytes > 0 ? min(100, (int)round(($memUsage / $memLimitBytes) * 100)) : 0;
if ($memPercent === 0 && $memUsage > 0) {
    $memPercent = 1;
}
$systemMemTotal = (int)($server['system_mem_total'] ?? 0);
$systemMemAvailable = (int)($server['system_mem_available'] ?? 0);
$systemMemUsed = $systemMemTotal > 0 ? max(0, $systemMemTotal - $systemMemAvailable) : 0;
$systemMemPercent = $systemMemTotal > 0 ? min(100, (int)round(($systemMemUsed / $systemMemTotal) * 100)) : 0;
if ($systemMemPercent === 0 && $systemMemUsed > 0) {
    $systemMemPercent = 1;
}
$diskTotal = (int)($server['disk_total'] ?? 0);
$diskFree = (int)($server['disk_free'] ?? 0);
$diskUsed = max(0, $diskTotal - $diskFree);
$diskPercent = $diskTotal > 0 ? min(100, (int)round(($diskUsed / $diskTotal) * 100)) : 0;
if ($diskPercent === 0 && $diskUsed > 0) {
    $diskPercent = 1;
}
$paymentsSeries = (array)($charts['payments_by_month'] ?? []);
$uploadsSeries = (array)($charts['uploads_by_week'] ?? []);
$maxPayments = 1000.0;
$uploadsValues = [];
foreach ($uploadsSeries as $row) {
    $uploadsValues[] = (int)round($parseNumber((string)($row['value'] ?? '0')));
}
$maxUploads = max(15000, (!empty($uploadsValues) ? max($uploadsValues) : 0));
$recentUsers = $isAdmin ? User::recentLogins(10) : [];
?>
<div class="admin-dashboard-header mb-4">
    <div class="d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="admin-dashboard-icon">
                <i class="bi bi-speedometer2"></i>
            </div>
            <div>
                <h1 class="h3 mb-1 fw-bold">Painel Admin</h1>
                <p class="text-muted small mb-0">Visão geral do sistema</p>
            </div>
        </div>
        <div class="badge bg-danger-subtle text-danger px-3 py-2 fw-semibold">
            <i class="bi bi-shield-fill-check me-1"></i>
            Administrador
        </div>
    </div>
</div>
<?php if ($isAdmin): ?>
    <div class="row g-3 align-items-start admin-dashboard-layout">
        <div class="col-lg-8 admin-dashboard-main">
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="admin-stat-card stat-primary">
                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Usuários</div>
                            <div class="stat-value"><?= $formatNumber((int)($stats['users_total'] ?? 0)) ?></div>
                            <div class="stat-meta">Não staff: <?= $formatNumber((int)($stats['users_nonstaff'] ?? 0)) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="admin-stat-card stat-warning">
                        <div class="stat-icon">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Pagamentos</div>
                            <div class="stat-value"><?= $formatNumber((int)($stats['payments_pending'] ?? 0)) ?></div>
                            <div class="stat-meta">
                                <a href="<?= base_path('/admin/payments') ?>">Ver pagamentos</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="admin-stat-card stat-info">
                        <div class="stat-icon">
                            <i class="bi bi-cloud-arrow-up"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Uploads</div>
                            <div class="stat-value"><?= $formatNumber((int)($stats['uploads_pending'] ?? 0)) ?></div>
                            <div class="stat-meta">
                                <a href="<?= base_path('/admin/uploads') ?>">Ver uploads</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="admin-stat-card stat-success">
                        <div class="stat-icon">
                            <i class="bi bi-headset"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Suporte</div>
                            <div class="stat-value"><?= $formatNumber((int)($stats['support_open'] ?? 0)) ?></div>
                            <div class="stat-meta">
                                <a href="<?= base_path('/admin/support') ?>">Ver tickets</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                    <div class="row g-3 mb-4">
                        <div class="col-lg-5">
                            <div class="admin-info-card">
                                <div class="admin-card-header">
                                    <i class="bi bi-hdd-rack me-2"></i>
                                    <h2 class="admin-card-title">Sistema</h2>
                                </div>
                                <div class="admin-card-body">
                                    <div class="d-flex flex-column gap-2 small">
                                        <div class="d-flex justify-content-between"><span class="text-muted">Servidor</span><span><?= View::e((string)($server['server_software'] ?? '')) ?></span></div>
                                        <div class="d-flex justify-content-between"><span class="text-muted">SO</span><span><?= View::e((string)($server['os'] ?? '')) ?></span></div>
                                        <div class="d-flex justify-content-between"><span class="text-muted">PHP</span><span><?= View::e((string)($server['php_version'] ?? '')) ?></span></div>
                                        <div class="d-flex justify-content-between"><span class="text-muted">Horario</span><span><?= View::e((string)($server['time'] ?? '')) ?></span></div>
                                    </div>
                                    <hr class="my-3">
                                    <div class="d-flex flex-column gap-2 small">
                                        <div class="d-flex justify-content-between"><span class="text-muted">Banco</span><span><?= View::e((string)($dbInfo['name'] ?? '')) ?></span></div>
                                        <div class="d-flex justify-content-between"><span class="text-muted">Versao</span><span><?= View::e((string)($dbInfo['version'] ?? '')) ?></span></div>
                                        <div class="d-flex justify-content-between"><span class="text-muted">Conexoes</span><span><?= $formatNumber((int)($dbInfo['connections'] ?? 0)) ?></span></div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Memoria PHP</span><span><?= $formatBytes($memUsage) ?> / <?= View::e((string)($server['memory_limit'] ?? '')) ?></span></div>
                                        <div class="progress" role="progressbar" aria-label="Memoria" aria-valuenow="<?= $memPercent ?>" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar" data-progress="<?= $memPercent ?>" style="width: 0%;"></div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Memoria servidor</span><span><?= $systemMemTotal > 0 ? ($formatBytes($systemMemUsed) . ' / ' . $formatBytes($systemMemTotal)) : 'N/A' ?></span></div>
                                        <div class="progress" role="progressbar" aria-label="Memoria servidor" aria-valuenow="<?= $systemMemPercent ?>" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-info" data-progress="<?= $systemMemPercent ?>" style="width: 0%;"></div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Disco</span><span><?= $formatBytes($diskUsed) ?> / <?= $formatBytes($diskTotal) ?></span></div>
                                        <div class="progress" role="progressbar" aria-label="Disco" aria-valuenow="<?= $diskPercent ?>" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-success" data-progress="<?= $diskPercent ?>" style="width: 0%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="admin-info-card">
                                <div class="admin-card-header">
                                    <i class="bi bi-grid-3x3-gap me-2"></i>
                                    <h2 class="admin-card-title">Atalhos de gestão</h2>
                                    <span class="badge bg-danger-subtle text-danger ms-auto">Admin</span>
                                </div>
                                <div class="admin-card-body">
                                    <div class="admin-shortcuts-grid">
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/users') ?>">
                                            <i class="bi bi-people"></i>
                                            <span>Usuários</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/team') ?>">
                                            <i class="bi bi-shield-check"></i>
                                            <span>Equipe</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/categories') ?>">
                                            <i class="bi bi-collection"></i>
                                            <span>Categorias</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/packages') ?>">
                                            <i class="bi bi-box"></i>
                                            <span>Pacotes</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/vouchers') ?>">
                                            <i class="bi bi-ticket-perforated"></i>
                                            <span>Vouchers</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/news') ?>">
                                            <i class="bi bi-megaphone"></i>
                                            <span>Notícias</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/notifications') ?>">
                                            <i class="bi bi-bell"></i>
                                            <span>Notificações</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/images') ?>">
                                            <i class="bi bi-images"></i>
                                            <span>Imagens</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/log') ?>">
                                            <i class="bi bi-list-ul"></i>
                                            <span>Logs</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/uploads') ?>">
                                            <i class="bi bi-upload"></i>
                                            <span>Uploads</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/payments') ?>">
                                            <i class="bi bi-cash-coin"></i>
                                            <span>Pagamentos</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/support') ?>">
                                            <i class="bi bi-life-preserver"></i>
                                            <span>Suporte</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/settings') ?>">
                                            <i class="bi bi-gear"></i>
                                            <span>Configurações</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/avatar-gallery') ?>">
                                            <i class="bi bi-person-square"></i>
                                            <span>Avatares</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/security/email-blocklist') ?>">
                                            <i class="bi bi-envelope-x"></i>
                                            <span>Email Block</span>
                                        </a>
                                        <a class="admin-shortcut-btn" href="<?= base_path('/admin/security/user-blocklist') ?>">
                                            <i class="bi bi-person-x"></i>
                                            <span>User Block</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-lg-6">
                            <div class="admin-info-card">
                                <div class="admin-card-header">
                                    <i class="bi bi-bar-chart-fill me-2"></i>
                                    <h2 class="admin-card-title">Pagamentos por mês</h2>
                                </div>
                                <div class="admin-card-body">
                                    <?php if (empty($paymentsSeries)): ?>
                                        <div class="text-muted">Sem dados.</div>
                                    <?php else: ?>
                                        <div class="d-flex flex-column gap-3">
                                            <?php foreach ($paymentsSeries as $row): ?>
                                                <?php
                                                    $value = $parseNumber((string)($row['value'] ?? '0'));
                                                    $percent = $maxPayments > 0 ? (int)round(($value / $maxPayments) * 100) : 0;
                                                    if ($percent === 0 && $value > 0) {
                                                        $percent = 1;
                                                    }
                                                ?>
                                                <div>
                                                    <div class="d-flex justify-content-between small mb-1">
                                                        <span class="text-muted"><?= View::e((string)($row['label'] ?? '')) ?></span>
                                                        <span><?= format_brl($value) ?></span>
                                                    </div>
                                                    <div class="progress" role="progressbar" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar" data-progress="<?= $percent ?>" style="width: 0%;"></div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="admin-info-card">
                                <div class="admin-card-header">
                                    <i class="bi bi-cloud-arrow-up-fill me-2"></i>
                                    <h2 class="admin-card-title">Uploads por semana</h2>
                                </div>
                                <div class="admin-card-body">
                                    <?php if (empty($uploadsSeries)): ?>
                                        <div class="text-muted">Sem dados.</div>
                                    <?php else: ?>
                                        <div class="d-flex flex-column gap-3">
                                            <?php foreach ($uploadsSeries as $idx => $row): ?>
                                                <?php
                                                    $value = $uploadsValues[$idx] ?? (int)round($parseNumber((string)($row['value'] ?? '0')));
                                                    $percent = $maxUploads > 0 ? (int)round(($value / $maxUploads) * 100) : 0;
                                                    if ($percent === 0 && $value > 0) {
                                                        $percent = 1;
                                                    }
                                                ?>
                                                <div>
                                                    <div class="d-flex justify-content-between small mb-1">
                                                        <span class="text-muted"><?= View::e((string)($row['label'] ?? '')) ?></span>
                                                        <span><?= $formatNumber($value) ?></span>
                                                    </div>
                                                    <div class="progress" role="progressbar" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar bg-info" data-progress="<?= $percent ?>" style="width: 0%;"></div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 admin-dashboard-sidebar">
                    <div class="d-flex flex-column gap-3">
                        <div class="admin-info-card">
                            <div class="admin-card-header">
                                <i class="bi bi-clock-history me-2"></i>
                                <h2 class="admin-card-title">Últimos conectados</h2>
                            </div>
                            <div class="admin-card-body">
                                <?php if (empty($recentUsers)): ?>
                                    <div class="text-muted">Sem registros recentes.</div>
                                <?php else: ?>
                                    <div class="admin-recent-users">
                                        <?php foreach ($recentUsers as $ru): ?>
                                            <div class="admin-user-item">
                                                <div class="user-info">
                                                    <i class="bi bi-person-circle"></i>
                                                    <span class="user-name"><?= View::e((string)($ru['username'] ?? '')) ?></span>
                                                </div>
                                                <?php $lastLogin = $ru['data_ultimo_login'] ?? $ru['data_registro'] ?? null; ?>
                                                <span class="user-time"><?= View::e(time_ago(is_string($lastLogin) ? $lastLogin : null)) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="admin-info-card">
                            <div class="admin-card-header">
                                <i class="bi bi-shield-exclamation me-2"></i>
                                <h2 class="admin-card-title">Tentativas de falhas</h2>
                            </div>
                            <div class="admin-card-body">
                                <?php if (empty($loginFailAttempts)): ?>
                                    <div class="text-muted">Sem tentativas recentes.</div>
                                <?php else: ?>
                                    <div class="admin-recent-users">
                                        <?php foreach ($loginFailAttempts as $fail): ?>
                                            <?php
                                            $label = (string)($fail['username'] ?? '');
                                            $ip = (string)($fail['ip'] ?? '');
                                            $when = (string)($fail['created_at'] ?? '');
                                            $label = $label !== '' ? $label : 'usuario desconhecido';
                                            ?>
                                            <?php
                                                $fullLabel = $label;
                                                if ($ip !== '') {
                                                    $fullLabel .= ' (' . $ip . ')';
                                                }
                                                $displayLabel = mb_strimwidth($fullLabel, 0, 36, '...');
                                            ?>
                                            <div class="admin-user-item admin-fail-item" title="<?= View::e($fullLabel) ?>">
                                                <div class="user-info">
                                                    <i class="bi bi-x-circle"></i>
                                                    <span class="user-name"><?= View::e($displayLabel) ?></span>
                                                </div>
                                                <span class="user-time"><?= View::e(time_ago($when !== '' ? $when : null)) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

<?php if ($isModerator): ?>
    <div class="admin-dashboard-header mb-4">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="admin-dashboard-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h1 class="h3 mb-1 fw-bold">Painel Moderador</h1>
                    <p class="text-muted small mb-0">Gestão de conteúdo</p>
                </div>
            </div>
            <div class="badge bg-info-subtle text-info px-3 py-2 fw-semibold">
                <i class="bi bi-shield-check me-1"></i>
                Moderador
            </div>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="admin-info-card">
                <div class="admin-card-header">
                    <i class="bi bi-cloud-arrow-up me-2"></i>
                    <h2 class="admin-card-title">Uploads</h2>
                </div>
                <div class="admin-card-body">
                    <p class="text-muted mb-3">Revisar uploads pendentes.</p>
                    <a class="btn btn-primary w-100" href="<?= base_path('/admin/uploads') ?>">
                        <i class="bi bi-box-arrow-up-right me-2"></i>
                        Abrir
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
