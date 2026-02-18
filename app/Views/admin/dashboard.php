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

// Calcular o valor máximo dos pagamentos mensais
$paymentsValues = [];
foreach ($paymentsSeries as $row) {
    $paymentsValues[] = $parseNumber((string)($row['value'] ?? '0'));
}
$maxPayments = !empty($paymentsValues) ? max($paymentsValues) : 1.0;
if ($maxPayments <= 0) {
    $maxPayments = 1.0;
}

// Calcular o valor máximo dos uploads semanais
$uploadsValues = [];
foreach ($uploadsSeries as $row) {
    $uploadsValues[] = (int)round($parseNumber((string)($row['value'] ?? '0')));
}
$maxUploads = !empty($uploadsValues) ? max($uploadsValues) : 1;
if ($maxUploads <= 0) {
    $maxUploads = 1;
}

$recentUsers = $isAdmin ? User::recentLogins(10) : [];
?>
<div class="admin-dashboard">
<div class="admin-dashboard-header">
	<div class="d-flex align-items-center justify-content-between">
		<div class="d-flex align-items-center gap-3">
			<div class="dashboard-icon-lg">
				<i class="bi bi-speedometer2"></i>
			</div>
			<div>
				<h1 class="h3 mb-1 fw-bold">Dashboard Admin</h1>
				<p class="text-muted mb-0" style="font-size: 0.875rem;">Visão geral e controle do sistema</p>
			</div>
		</div>
		<div class="badge bg-danger text-white px-3 py-2" style="font-size: 0.875rem;">
			<i class="bi bi-shield-fill-check me-1"></i>Administrador
		</div>
	</div>
</div>

<?php if ($isAdmin): ?>
	<div class="dashboard-stats-grid">
		<div class="stat-card stat-primary">
			<div class="stat-icon">
				<i class="bi bi-people-fill"></i>
			</div>
			<div class="stat-content">
				<div class="stat-label">Total de Usuários</div>
				<div class="stat-value"><?= $formatNumber((int)($stats['users_total'] ?? 0)) ?></div>
				<div class="stat-meta">
					<i class="bi bi-person me-1"></i>Não staff: <?= $formatNumber((int)($stats['users_nonstaff'] ?? 0)) ?>
				</div>
			</div>
		</div>
		<div class="stat-card stat-warning">
			<div class="stat-icon">
				<i class="bi bi-credit-card-fill"></i>
			</div>
			<div class="stat-content">
				<div class="stat-label">Pagamentos</div>
				<div class="stat-value"><?= $formatNumber((int)($stats['payments_pending'] ?? 0)) ?></div>
				<div class="stat-meta">
					<a href="<?= base_path('/admin/payments') ?>"><i class="bi bi-arrow-right-circle me-1"></i>Gerenciar</a>
				</div>
			</div>
		</div>
		<div class="stat-card stat-info">
			<div class="stat-icon">
				<i class="bi bi-cloud-upload-fill"></i>
			</div>
			<div class="stat-content">
				<div class="stat-label">Uploads Pendentes</div>
				<div class="stat-value"><?= $formatNumber((int)($stats['uploads_pending'] ?? 0)) ?></div>
				<div class="stat-meta">
					<a href="<?= base_path('/admin/uploads') ?>"><i class="bi bi-arrow-right-circle me-1"></i>Revisar</a>
				</div>
			</div>
		</div>
		<div class="stat-card stat-success">
			<div class="stat-icon">
				<i class="bi bi-headset"></i>
			</div>
			<div class="stat-content">
				<div class="stat-label">Tickets Abertos</div>
				<div class="stat-value"><?= $formatNumber((int)($stats['support_open'] ?? 0)) ?></div>
				<div class="stat-meta">
					<a href="<?= base_path('/admin/support') ?>"><i class="bi bi-arrow-right-circle me-1"></i>Ver todos</a>
				</div>
			</div>
		</div>
	</div>

	<div class="dashboard-layout">
		<div class="dashboard-main">
			<div class="dashboard-card">
				<div class="card-header">
					<i class="bi bi-grid-3x3-gap-fill me-2"></i>
					<span class="card-title">Atalhos Rápidos</span>
				</div>
				<div class="card-body">
					<div class="shortcuts-grid">
						<a class="shortcut-btn" href="<?= base_path('/admin/users') ?>">
							<i class="bi bi-people-fill"></i>
							<span>Usuários</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/team') ?>">
							<i class="bi bi-shield-check"></i>
							<span>Equipe</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/categories') ?>">
							<i class="bi bi-collection-fill"></i>
							<span>Categorias</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/connectors') ?>">
							<i class="bi bi-plug-fill"></i>
							<span>Conectores</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/packages') ?>">
							<i class="bi bi-box-seam-fill"></i>
							<span>Pacotes</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/vouchers') ?>">
							<i class="bi bi-ticket-perforated-fill"></i>
							<span>Vouchers</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/news') ?>">
							<i class="bi bi-megaphone-fill"></i>
							<span>Notícias</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/notifications') ?>">
							<i class="bi bi-bell-fill"></i>
							<span>Notificações</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/images') ?>">
							<i class="bi bi-images"></i>
							<span>Imagens</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/log') ?>">
							<i class="bi bi-list-ul"></i>
							<span>Logs</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/uploads') ?>">
							<i class="bi bi-upload"></i>
							<span>Uploads</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/payments') ?>">
							<i class="bi bi-cash-coin"></i>
							<span>Pagamentos</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/support') ?>">
							<i class="bi bi-life-preserver"></i>
							<span>Suporte</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/settings') ?>">
							<i class="bi bi-gear-fill"></i>
							<span>Config</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/avatar-gallery') ?>">
							<i class="bi bi-person-square"></i>
							<span>Avatares</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/security/email-blocklist') ?>">
							<i class="bi bi-envelope-x-fill"></i>
							<span>Block Email</span>
						</a>
						<a class="shortcut-btn" href="<?= base_path('/admin/security/user-blocklist') ?>">
							<i class="bi bi-person-x-fill"></i>
							<span>Block User</span>
						</a>
					</div>
				</div>
			</div>

			<div class="dashboard-charts">
				<div class="dashboard-card">
					<div class="card-header">
						<i class="bi bi-bar-chart-fill me-2"></i>
						<span class="card-title">Pagamentos mensais</span>
					</div>
					<div class="card-body">
						<?php if (empty($paymentsSeries)): ?>
							<div class="text-muted text-center py-3">Sem dados de pagamentos</div>
						<?php else: ?>
							<div class="chart-bars">
								<?php foreach ($paymentsSeries as $row): ?>
									<?php
										$value = $parseNumber((string)($row['value'] ?? '0'));
										$percent = $maxPayments > 0 ? (int)round(($value / $maxPayments) * 100) : 0;
										if ($percent === 0 && $value > 0) {
											$percent = 1;
										}
									?>
									<div class="chart-bar-item">
										<div class="d-flex justify-content-between mb-1">
											<span class="chart-label"><?= View::e((string)($row['label'] ?? '')) ?></span>
											<span class="chart-value"><?= format_brl($value) ?></span>
										</div>
										<div class="progress">
											<div class="progress-bar bg-warning" data-progress="<?= $percent ?>" style="width: 0%;"></div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="dashboard-card">
					<div class="card-header">
						<i class="bi bi-cloud-arrow-up-fill me-2"></i>
						<span class="card-title">Uploads semanais</span>
					</div>
					<div class="card-body">
						<?php if (empty($uploadsSeries)): ?>
							<div class="text-muted text-center py-3">Sem dados de uploads</div>
						<?php else: ?>
							<div class="chart-bars">
								<?php foreach ($uploadsSeries as $idx => $row): ?>
									<?php
										$value = $uploadsValues[$idx] ?? (int)round($parseNumber((string)($row['value'] ?? '0')));
										$percent = $maxUploads > 0 ? (int)round(($value / $maxUploads) * 100) : 0;
										if ($percent === 0 && $value > 0) {
											$percent = 1;
										}
									?>
									<div class="chart-bar-item">
										<div class="d-flex justify-content-between mb-1">
											<span class="chart-label"><?= View::e((string)($row['label'] ?? '')) ?></span>
											<span class="chart-value"><?= $formatNumber($value) ?></span>
										</div>
										<div class="progress">
											<div class="progress-bar bg-info" data-progress="<?= $percent ?>" style="width: 0%;"></div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="dashboard-card">
				<div class="card-header">
					<i class="bi bi-shield-exclamation me-2"></i>
					<span class="card-title">Falhas de login</span>
				</div>
				<div class="card-body">
					<?php if (empty($loginFailAttempts)): ?>
						<div class="text-muted text-center py-2">Sem tentativas</div>
					<?php else: ?>
						<div class="fail-list">
							<?php foreach ($loginFailAttempts as $fail): ?>
								<?php
								$username = (string)($fail['username'] ?? '');
								$ip = (string)($fail['ip'] ?? '');
								$when = (string)($fail['created_at'] ?? '');
								$username = $username !== '' ? $username : 'desconhecido';
								?>
								<div class="fail-item">
									<div class="fail-icon">
										<i class="bi bi-x-circle text-danger"></i>
									</div>
									<div class="fail-username">
										<span class="fw-medium"><?= View::e($username) ?></span>
									</div>
									<div class="fail-ip">
										<i class="bi bi-router me-1 text-muted"></i>
										<span class="text-muted"><?= View::e($ip !== '' ? $ip : 'N/A') ?></span>
									</div>
									<div class="fail-country">
										<span class="country-flag" title="País de origem">🌐</span>
									</div>
									<div class="fail-time">
										<span class="text-muted"><?= View::e(time_ago($when !== '' ? $when : null)) ?></span>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="dashboard-sidebar">
			<div class="dashboard-card">
				<div class="card-header">
					<i class="bi bi-clock-history me-2"></i>
					<span class="card-title">Últimos logins</span>
				</div>
				<div class="card-body">
					<?php if (empty($recentUsers)): ?>
						<div class="text-muted text-center py-2">Sem registros</div>
					<?php else: ?>
						<div class="user-list">
							<?php foreach ($recentUsers as $ru): ?>
								<?php
									$userId = (int)($ru['id'] ?? 0);
									$username = (string)($ru['username'] ?? '');
									$role = (string)($ru['role'] ?? 'user');
									$accessTier = (string)($ru['access_tier'] ?? 'user');
									$lastLogin = $ru['data_ultimo_login'] ?? $ru['data_registro'] ?? null;
									
									// Determinar cor do ícone
									$iconColor = 'text-primary'; // azul padrão
									if ($role === 'superadmin') {
										$iconColor = 'text-danger'; // vermelho
									} elseif ($role === 'admin' || $role === 'equipe') {
										$iconColor = 'text-warning'; // amarelo
									} elseif ($accessTier === 'restrito') {
										$iconColor = 'text-purple'; // roxo
									} elseif ($accessTier === 'assinante' || $accessTier === 'vitalicio') {
										$iconColor = 'text-success'; // verde
									}
									
									// Calcular tempo desde o login
									$timeDisplay = 'agora';
									if (is_string($lastLogin) && $lastLogin !== '') {
										$loginTime = strtotime($lastLogin);
										if ($loginTime !== false) {
											$minutesAgo = (int)((time() - $loginTime) / 60);
											if ($minutesAgo >= 15) {
												if ($minutesAgo < 60) {
													$timeDisplay = $minutesAgo . ' min';
												} else {
													$timeDisplay = time_ago($lastLogin);
												}
											}
										}
									}
									?>
								<div class="user-item">
									<div class="user-info">
										<i class="bi bi-person-circle <?= $iconColor ?>"></i>
									<a href="/perfil/<?= View::e($username) ?>" target="_blank" class="user-name text-decoration-none"><?= View::e($username) ?></a>
									</div>
									<span class="user-time"><?= View::e($timeDisplay) ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>


			<div class="dashboard-card">
				<div class="card-header">
					<i class="bi bi-hdd-rack-fill me-2"></i>
					<span class="card-title">Sistema</span>
				</div>
				<div class="card-body">
					<div class="system-info">
						<div class="info-row">
							<span class="info-label">Servidor</span>
							<span class="info-value"><?= View::e((string)($server['server_software'] ?? '-')) ?></span>
						</div>
						<div class="info-row">
							<span class="info-label">SO</span>
							<span class="info-value"><?= View::e((string)($server['os'] ?? '-')) ?></span>
						</div>
						<div class="info-row">
							<span class="info-label">PHP</span>
							<span class="info-value"><?= View::e((string)($server['php_version'] ?? '-')) ?></span>
						</div>
						<div class="info-row">
							<span class="info-label">Horário</span>
							<span class="info-value"><?= View::e((string)($server['time'] ?? '-')) ?></span>
						</div>
					</div>
					<hr class="my-2">
					<div class="system-info">
						<div class="info-row">
							<span class="info-label">Banco</span>
							<span class="info-value"><?= View::e((string)($dbInfo['name'] ?? '-')) ?></span>
						</div>
						<div class="info-row">
							<span class="info-label">Versão</span>
							<span class="info-value"><?= View::e((string)($dbInfo['version'] ?? '-')) ?></span>
						</div>
						<div class="info-row">
							<span class="info-label">Conexões</span>
							<span class="info-value"><?= $formatNumber((int)($dbInfo['connections'] ?? 0)) ?></span>
						</div>
					</div>
					<hr class="my-2">
					<div class="resource-meter">
						<div class="meter-header">
							<span class="meter-label"><i class="bi bi-memory me-1"></i>Memória PHP</span>
							<span class="meter-value"><?= $formatBytes($memUsage) ?> / <?= View::e((string)($server['memory_limit'] ?? '-')) ?></span>
						</div>
						<div class="progress">
							<div class="progress-bar" data-progress="<?= $memPercent ?>" style="width: 0%;"></div>
						</div>
					</div>
					<div class="resource-meter">
						<div class="meter-header">
							<span class="meter-label"><i class="bi bi-server me-1"></i>Memória Servidor</span>
							<span class="meter-value"><?= $systemMemTotal > 0 ? ($formatBytes($systemMemUsed) . ' / ' . $formatBytes($systemMemTotal)) : 'N/A' ?></span>
						</div>
						<div class="progress">
							<div class="progress-bar bg-info" data-progress="<?= $systemMemPercent ?>" style="width: 0%;"></div>
						</div>
					</div>
					<div class="resource-meter">
						<div class="meter-header">
							<span class="meter-label"><i class="bi bi-hdd-fill me-1"></i>Disco</span>
							<span class="meter-value"><?= $formatBytes($diskUsed) ?> / <?= $formatBytes($diskTotal) ?></span>
						</div>
						<div class="progress">
							<div class="progress-bar bg-success" data-progress="<?= $diskPercent ?>" style="width: 0%;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php if ($isModerator && !$isAdmin): ?>
	<div class="admin-dashboard-header">
		<div class="d-flex align-items-center justify-content-between">
			<div class="d-flex align-items-center gap-3">
				<div class="dashboard-icon-lg">
					<i class="bi bi-shield-check"></i>
				</div>
				<div>
					<h1 class="h3 mb-1 fw-bold">Painel Moderador</h1>
					<p class="text-muted mb-0" style="font-size: 0.875rem;">Gestão de conteúdo e uploads</p>
				</div>
			</div>
			<div class="badge bg-info text-white px-3 py-2" style="font-size: 0.875rem;">
				<i class="bi bi-shield-check me-1"></i>Moderador
			</div>
		</div>
	</div>

	<div class="dashboard-card" style="max-width: 500px;">
		<div class="card-header">
			<i class="bi bi-cloud-upload-fill me-2"></i>
			<span class="card-title">Uploads para Revisão</span>
		</div>
		<div class="card-body text-center py-4">
			<p class="mb-3">Revisar e aprovar uploads pendentes de usuários.</p>
			<a class="btn btn-primary" href="<?= base_path('/admin/uploads') ?>">
				<i class="bi bi-box-arrow-up-right me-2"></i>Abrir Uploads
			</a>
		</div>
	</div>
<?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
