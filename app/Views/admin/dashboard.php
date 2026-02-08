<?php
use App\Core\Auth;
use App\Core\View;
ob_start();
$user = Auth::user();
$role = $user['role'] ?? 'user';
$isAdmin = \App\Core\Auth::isAdmin($user);
$isModerator = \App\Core\Auth::isModerator($user);
$stats = (array)($stats ?? []);
$server = (array)($server ?? []);
$charts = (array)($charts ?? []);
$dbInfo = (array)($dbInfo ?? []);
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
$parseBytes = static function (string $value): int {
    $value = trim($value);
    if ($value === '' || $value === '-1' || $value === 'N/A') {
        return 0;
    }
    $unit = strtolower(substr($value, -1));
    $number = (float)preg_replace('/[^0-9.]/', '', $value);
    $mult = 1;
    if ($unit === 'g') {
        $mult = 1024 ** 3;
    } elseif ($unit === 'm') {
        $mult = 1024 ** 2;
    } elseif ($unit === 'k') {
        $mult = 1024;
    }
    return (int)round($number * $mult);
};
$memLimitBytes = $parseBytes((string)($server['memory_limit'] ?? ''));
$memUsage = (int)($server['memory_usage'] ?? 0);
$memPercent = $memLimitBytes > 0 ? min(100, (int)round(($memUsage / $memLimitBytes) * 100)) : 0;
$diskTotal = (int)($server['disk_total'] ?? 0);
$diskFree = (int)($server['disk_free'] ?? 0);
$diskUsed = max(0, $diskTotal - $diskFree);
$diskPercent = $diskTotal > 0 ? min(100, (int)round(($diskUsed / $diskTotal) * 100)) : 0;
$paymentsSeries = (array)($charts['payments_by_month'] ?? []);
$uploadsSeries = (array)($charts['uploads_by_week'] ?? []);
$maxPayments = 0;
foreach ($paymentsSeries as $row) {
    $maxPayments = max($maxPayments, (int)($row['value'] ?? 0));
}
$maxUploads = 0;
foreach ($uploadsSeries as $row) {
    $maxUploads = max($maxUploads, (int)($row['value'] ?? 0));
}
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <h1 class="h4 mb-1">Painel Admin</h1>
        <div class="text-muted small">Visao geral, metricas e atalhos de gestao.</div>
    </div>
</div>

<?php if ($isAdmin): ?>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Usuarios</div>
                    <div class="fs-3 fw-semibold"><?= $formatNumber((int)($stats['users_total'] ?? 0)) ?></div>
                    <div class="small text-muted">Nao staff: <?= $formatNumber((int)($stats['users_nonstaff'] ?? 0)) ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Pagamentos pendentes</div>
                    <div class="fs-3 fw-semibold"><?= $formatNumber((int)($stats['payments_pending'] ?? 0)) ?></div>
                    <a class="small" href="<?= base_path('/admin/payments') ?>">Ver pagamentos</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Uploads na fila</div>
                    <div class="fs-3 fw-semibold"><?= $formatNumber((int)($stats['uploads_pending'] ?? 0)) ?></div>
                    <a class="small" href="<?= base_path('/admin/uploads') ?>">Ver uploads</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Suporte aberto</div>
                    <div class="fs-3 fw-semibold"><?= $formatNumber((int)($stats['support_open'] ?? 0)) ?></div>
                    <a class="small" href="<?= base_path('/admin/support') ?>">Ver tickets</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 mb-3">Sistema</h2>
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
                        <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Memoria</span><span><?= $formatBytes($memUsage) ?> / <?= View::e((string)($server['memory_limit'] ?? '')) ?></span></div>
                        <div class="progress" role="progressbar" aria-label="Memoria" aria-valuenow="<?= $memPercent ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: <?= $memPercent ?>%;"></div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Disco</span><span><?= $formatBytes($diskUsed) ?> / <?= $formatBytes($diskTotal) ?></span></div>
                        <div class="progress" role="progressbar" aria-label="Disco" aria-valuenow="<?= $diskPercent ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-success" style="width: <?= $diskPercent ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                        <h2 class="h6 mb-0">Atalhos de gestao</h2>
                        <span class="badge text-bg-light">Admin</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4 d-grid">
                            <a class="btn btn-outline-primary" href="<?= base_path('/admin/users') ?>">Usuarios</a>
                        </div>
                        <div class="col-md-4 d-grid">
                            <a class="btn btn-outline-primary" href="<?= base_path('/admin/team') ?>">Equipe</a>
                        </div>
                        <div class="col-md-4 d-grid">
                            <a class="btn btn-outline-primary" href="<?= base_path('/admin/categories') ?>">Categorias</a>
                        </div>
                        <div class="col-md-4 d-grid">
                            <a class="btn btn-outline-primary" href="<?= base_path('/admin/packages') ?>">Pacotes</a>
                        </div>
                        <div class="col-md-4 d-grid">
                            <a class="btn btn-outline-primary" href="<?= base_path('/admin/vouchers') ?>">Vouchers</a>
                        </div>
                        <div class="col-md-4 d-grid">
                            <a class="btn btn-outline-primary" href="<?= base_path('/admin/news') ?>">Noticias</a>
                        </div>
                        <div class="col-md-4 d-grid">
                            <a class="btn btn-outline-primary" href="<?= base_path('/admin/uploads') ?>">Uploads</a>
                        </div>
                        <div class="col-md-4 d-grid">
                            <a class="btn btn-outline-primary" href="<?= base_path('/admin/payments') ?>">Pagamentos</a>
                        </div>
                        <div class="col-md-4 d-grid">
                            <a class="btn btn-outline-primary" href="<?= base_path('/admin/support') ?>">Suporte</a>
                        </div>
                        <div class="col-md-4 d-grid">
                            <a class="btn btn-outline-primary" href="<?= base_path('/admin/settings') ?>">Configuracoes</a>
                        </div>
                        <div class="col-md-4 d-grid">
                            <a class="btn btn-outline-primary" href="<?= base_path('/admin/security/email-blocklist') ?>">Email blocklist</a>
                        </div>
                        <div class="col-md-4 d-grid">
                            <a class="btn btn-outline-primary" href="<?= base_path('/admin/security/user-blocklist') ?>">User blocklist</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Categorias</div>
                    <div class="fs-4 fw-semibold"><?= $formatNumber((int)($stats['categories_total'] ?? 0)) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Series</div>
                    <div class="fs-4 fw-semibold"><?= $formatNumber((int)($stats['series_total'] ?? 0)) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Capitulos</div>
                    <div class="fs-4 fw-semibold"><?= $formatNumber((int)($stats['content_total'] ?? 0)) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h2 class="h6 mb-0">Pagamentos por mes</h2>
                        <span class="badge bg-light text-muted border">Ultimos <?= count($paymentsSeries) ?></span>
                    </div>
                    <?php if (empty($paymentsSeries)): ?>
                        <div class="text-muted">Sem dados.</div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($paymentsSeries as $row): ?>
                                <?php
                                    $value = (int)($row['value'] ?? 0);
                                    $percent = $maxPayments > 0 ? (int)round(($value / $maxPayments) * 100) : 0;
                                ?>
                                <div>
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-muted"><?= View::e((string)($row['label'] ?? '')) ?></span>
                                        <span><?= $formatNumber($value) ?></span>
                                    </div>
                                    <div class="progress" role="progressbar" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: <?= $percent ?>%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h2 class="h6 mb-0">Uploads por semana</h2>
                        <span class="badge bg-light text-muted border">Ultimas <?= count($uploadsSeries) ?></span>
                    </div>
                    <?php if (empty($uploadsSeries)): ?>
                        <div class="text-muted">Sem dados.</div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($uploadsSeries as $row): ?>
                                <?php
                                    $value = (int)($row['value'] ?? 0);
                                    $percent = $maxUploads > 0 ? (int)round(($value / $maxUploads) * 100) : 0;
                                ?>
                                <div>
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-muted"><?= View::e((string)($row['label'] ?? '')) ?></span>
                                        <span><?= $formatNumber($value) ?></span>
                                    </div>
                                    <div class="progress" role="progressbar" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-info" style="width: <?= $percent ?>%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($isModerator): ?>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body">
                <h3 class="h6">Uploads</h3>
                <p class="text-muted">Revisar uploads pendentes.</p>
                <a class="btn btn-sm btn-primary" href="<?= base_path('/admin/uploads') ?>">Abrir</a>
            </div></div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
