<?php
use App\Core\View;
ob_start();

$formatAgo = static function (?string $dt): string {
	if (!$dt) {
		return '-';
	}
	$ts = strtotime($dt);
	if ($ts === false) {
		return '-';
	}
	$diff = time() - $ts;
	if ($diff < 60) {
		return 'agora';
	}
	$mins = (int)floor($diff / 60);
	if ($mins < 60) {
		return $mins . ' min';
	}
	$hours = (int)floor($mins / 60);
	if ($hours < 24) {
		return $hours . ' h';
	}
	$days = (int)floor($hours / 24);
	return $days . ' d';
};

$formatSub = static function (?string $dt): string {
	if (!$dt) {
		return '-';
	}
	$ts = strtotime($dt);
	if ($ts === false) {
		return '-';
	}
	$diff = $ts - time();
	if ($diff <= 0) {
		return 'expirada';
	}
	$days = (int)floor($diff / 86400);
	if ($days > 0) {
		return $days . ' d';
	}
	$hours = (int)floor($diff / 3600);
	return $hours . ' h';
};

$tierLabels = [
	'trial' => 'Trial',
	'assinante' => 'Assinante',
	'restrito' => 'Restrito',
	'vitalicio' => 'Vitalício',
	'user' => 'User',
];
$tierColors = [
	'trial' => '#0dcaf0',
	'assinante' => '#198754',
	'restrito' => '#dc3545',
	'vitalicio' => '#6f42c1',
	'user' => '#6c757d',
];
$tiersInUse = [];
foreach (($users ?? []) as $u) {
	$tier = (string)($u['access_tier'] ?? 'user');
	$tiersInUse[$tier] = true;
}
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
	<h1 class="h4 mb-0">Gerenciamento de Usuarios</h1>
	<div class="d-flex flex-wrap gap-2">
		<a class="btn btn-outline-secondary" href="<?= base_path('/admin/users/import') ?>">Importar JSON</a>
		<a class="btn btn-outline-primary" href="<?= base_path('/admin/team') ?>">Gerenciar Equipes</a>
	</div>
</div>

<hr class="text-success" />

<?php
$filters = (array)($filters ?? []);
$filterQ = (string)($filters['q'] ?? '');
$filterTier = (string)($filters['tier'] ?? '');
$filterStatus = (string)($filters['status'] ?? '');
$filterPackage = (string)($filters['package'] ?? '');
$pages = (int)max(1, ($pages ?? 1));
$page = (int)max(1, ($page ?? 1));
$perPage = (int)max(10, ($perPage ?? 25));
$page = min($page, $pages);
?>

<form method="get" action="<?= base_path('/admin/users') ?>" class="row g-2 align-items-end mb-3">
	<div class="col-md-4">
		<label class="form-label">Buscar</label>
		<input class="form-control" name="q" value="<?= View::e($filterQ) ?>" placeholder="ID, usuario, email ou telefone">
	</div>
	<div class="col-md-2">
		<label class="form-label">Tier</label>
		<select class="form-select" name="tier">
			<option value="">Todos</option>
			<?php foreach (['user','trial','assinante','restrito','vitalicio'] as $tier): ?>
				<option value="<?= $tier ?>" <?= $filterTier === $tier ? 'selected' : '' ?>><?= $tier ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="col-md-2">
		<label class="form-label">Status</label>
		<select class="form-select" name="status">
			<option value="">Todos</option>
			<option value="active" <?= $filterStatus === 'active' ? 'selected' : '' ?>>Ativo</option>
			<option value="locked" <?= $filterStatus === 'locked' ? 'selected' : '' ?>>Bloqueado</option>
		</select>
	</div>
	<div class="col-md-3">
		<label class="form-label">Pacote</label>
		<select class="form-select" name="package">
			<option value="">Todos</option>
			<option value="none" <?= $filterPackage === 'none' ? 'selected' : '' ?>>Sem pacote</option>
			<?php foreach (($packages ?? []) as $pkg): ?>
				<option value="<?= (int)$pkg['id'] ?>" <?= (string)($pkg['id'] ?? '') === $filterPackage ? 'selected' : '' ?>><?= View::e((string)($pkg['title'] ?? '')) ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="col-md-1">
		<label class="form-label">Por pagina</label>
		<select class="form-select" name="perPage">
			<?php foreach ([10,25,50,100,200] as $pp): ?>
				<option value="<?= $pp ?>" <?= (int)$perPage === $pp ? 'selected' : '' ?>><?= $pp ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="col-12 d-flex gap-2">
		<button class="btn btn-primary" type="submit">Filtrar</button>
		<a class="btn btn-outline-secondary" href="<?= base_path('/admin/users') ?>">Limpar</a>
	</div>
</form>

<?php if (!empty($resetToken) && !empty($resetUserId)): ?>
	<?php
	$baseUrl = rtrim((string)config('app.url', ''), '/');
	$resetPath = base_path('/reset?token=' . (string)$resetToken);
	$resetLink = $baseUrl !== '' ? ($baseUrl . $resetPath) : $resetPath;
	?>
	<div class="alert alert-info">
		<div class="mb-2">Link de reset (usuário <?= View::e((string)($resetUserName ?? ('#' . (int)$resetUserId))) ?>):</div>
		<div class="input-group">
			<input class="form-control" type="text" readonly id="resetLinkInput" value="<?= View::e($resetLink) ?>">
		</div>
	</div>
<?php endif; ?>

<div class="table-responsive">
	<table class="table table-sm table-hover align-middle admin-users-table small">
		<thead class="table-light">
		<tr>
			<th scope="col" style="width: 24px;"></th>
			<th scope="col">Usuário</th>
			<th scope="col" style="width: 160px;">Telefone</th>
			<th scope="col" style="width: 200px;">Observações</th>
			<th scope="col" style="width: 130px;">Último login</th>
			<th scope="col" style="width: 110px;">Assinatura</th>
			<th scope="col">Acesso por categoria</th>
			<th scope="col" class="text-end" style="width: 190px;">Ações</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach (($users ?? []) as $u): ?>
			<?php
				$isLocked = !empty($u['lock_until']) && strtotime((string)$u['lock_until']) > time();
				$isSuper = $u['role'] === 'superadmin';
				$isTeam = ($u['role'] ?? '') === 'equipe' || !empty($u['support_agent']) || !empty($u['uploader_agent']) || !empty($u['moderator_agent']);
				$currentRole = $currentUser['role'] ?? 'user';
				$isSelf = !empty($currentUser) && (int)$currentUser['id'] === (int)$u['id'];
				$canEdit = !$isSuper && \App\Core\Auth::isAdmin($currentUser);
				$rowClass = $isLocked ? 'text-muted' : '';
				$tier = (string)($u['access_tier'] ?? 'user');
				$tierColor = $tierColors[$tier] ?? '#6c757d';
			?>
			<tr class="<?= $rowClass ?>" style="<?= $isLocked ? 'text-decoration: line-through;' : '' ?>">
				<td>
					<span class="tier-dot" style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color: <?= View::e($tierColor) ?>; border:1px solid <?= View::e($tierColor) ?>;" title="<?= View::e($tierLabels[$tier] ?? $tier) ?>"></span>
				</td>
				<td>
					<a class="text-decoration-none" href="<?= base_path('/perfil/' . rawurlencode((string)($u['username'] ?? ''))) ?>">
						<?= View::e((string)($u['username'] ?? '')) ?>
					</a>
				</td>
				<td>
					<?php
					$phone = (string)($u['phone'] ?? '');
					$country = (string)($u['phone_country'] ?? '');
					$digits = preg_replace('/\D+/', '', $country . $phone) ?? '';
					?>
					<?= View::e($phone !== '' ? $phone : '-') ?>
					<?php if (!empty($u['phone_has_whatsapp']) && $digits !== ''): ?>
						<a class="ms-2 text-success" href="https://wa.me/<?= View::e($digits) ?>" target="_blank" rel="noopener" title="WhatsApp">
							<i class="fa-brands fa-whatsapp"></i>
						</a>
					<?php endif; ?>
				</td>
				<td><?= View::e((string)($u['observations'] ?? '-')) ?></td>
				<td><?= View::e($formatAgo($u['data_ultimo_login'] ?? null)) ?></td>
				<td><?= View::e($formatSub($u['subscription_expires_at'] ?? null)) ?></td>
				<td>
					<?php
					$payment = $latestPayments[(int)$u['id']] ?? null;
					$pkgId = (int)($payment['package_id'] ?? 0);
					$pkg = $packageMap[$pkgId] ?? null;
					$allowedIds = $packageCategories[$pkgId] ?? [];
					$allowedSet = array_flip(array_map('intval', $allowedIds));
					$catBadges = [];
					foreach (($categories ?? []) as $cat) {
						$catId = (int)($cat['id'] ?? 0);
						if ($catId <= 0) {
							continue;
						}
						if (!empty($cat['requires_subscription'])) {
							if ($pkgId > 0 && isset($allowedSet[$catId])) {
								$catBadges[] = '<span class="badge bg-success">' . View::e((string)$cat['name']) . '</span>';
							}
						} else {
							$catBadges[] = '<span class="badge bg-secondary">' . View::e((string)$cat['name']) . '</span>';
						}
					}
					?>
					<div class="small">
						<?php if ($pkg): ?>
							<div class="mb-1"><strong>Pacote:</strong> <?= View::e((string)($pkg['title'] ?? '')) ?></div>
						<?php else: ?>
							<div class="mb-1 text-muted">Sem pacote ativo</div>
						<?php endif; ?>
						<div class="d-flex flex-wrap gap-1">
							<?= implode(' ', $catBadges) ?>
						</div>
					</div>
				</td>
				<td class="text-end">
					<div class="d-inline-flex gap-2 admin-actions">
					<button class="btn btn-sm btn-outline-secondary px-2" type="button" data-bs-toggle="modal" data-bs-target="#editUserModal<?= (int)$u['id'] ?>" title="Editar">
						<i class="fa-solid fa-pen-to-square"></i>
						<span class="visually-hidden">Editar</span>
					</button>

					<button class="btn btn-sm btn-outline-info px-2" type="button" data-bs-toggle="modal" data-bs-target="#loginHistoryModal<?= (int)$u['id'] ?>" title="Historico de login">
						<i class="fa-solid fa-clock-rotate-left"></i>
						<span class="visually-hidden">Historico de login</span>
					</button>

					<form method="post" action="<?= base_path('/admin/users/restrict') ?>" onsubmit="return confirm('Remover acesso deste usuário?');">
						<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
						<input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
						<?php $isRestricted = ($u['access_tier'] ?? '') === 'restrito'; ?>
						<button class="btn btn-sm px-2 <?= $isRestricted ? 'btn-danger' : 'btn-outline-danger' ?>" type="submit" <?= $isSuper ? 'disabled' : '' ?> title="Restringir acesso">
							<i class="fa-solid fa-user-slash"></i>
							<span class="visually-hidden">Restringir acesso</span>
						</button>
					</form>

					<?php if ($isLocked): ?>
						<form method="post" action="<?= base_path('/admin/users/unlock') ?>">
							<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
							<input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
							<button class="btn btn-sm btn-warning px-2 lock-toggle-btn" type="submit" <?= $isSuper ? 'disabled' : '' ?> title="Desbloquear">
								<i class="fa-solid fa-lock"></i>
								<span class="visually-hidden">Desbloquear</span>
							</button>
						</form>
					<?php else: ?>
						<form method="post" action="<?= base_path('/admin/users/lock') ?>">
							<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
							<input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
							<input type="hidden" name="lock_until" value="2099-12-31 00:00:00">
							<button class="btn btn-sm btn-outline-warning px-2 lock-toggle-btn" type="submit" <?= $isSuper ? 'disabled' : '' ?> title="Bloquear">
								<i class="fa-solid fa-lock-open"></i>
								<span class="visually-hidden">Bloquear</span>
							</button>
						</form>
					<?php endif; ?>
					</div>
				</td>
			</tr>
			<?php
			ob_start();
			?>
			<div class="modal fade" id="editUserModal<?= (int)$u['id'] ?>" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog modal-lg modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Editar usuário</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
						</div>
						<form method="post" action="<?= base_path('/admin/users/update') ?>">
							<div class="modal-body">
								<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
								<input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
								<div class="row g-2">
									<div class="col-md-6">
										<label class="form-label">Usuário</label>
										<input class="form-control" type="text" name="username" value="<?= View::e((string)$u['username']) ?>" required autocapitalize="none" oninput="this.value = this.value.toLowerCase()">
									</div>
									<div class="col-md-6">
										<label class="form-label">Email</label>
										<input class="form-control" type="email" name="email" value="<?= View::e((string)$u['email']) ?>" required autocapitalize="none" oninput="this.value = this.value.toLowerCase()">
									</div>
									<div class="col-md-4">
										<label class="form-label">Telefone</label>
										<input class="form-control" type="text" name="phone" value="<?= View::e((string)$u['phone']) ?>" required>
									</div>
									<div class="col-md-4">
										<label class="form-label">País</label>
										<input class="form-control" type="text" name="phone_country" value="<?= View::e((string)$u['phone_country']) ?>" required>
									</div>
									<div class="col-md-4">
										<label class="form-label">WhatsApp</label>
										<select class="form-select" name="phone_has_whatsapp">
											<option value="1" <?= !empty($u['phone_has_whatsapp']) ? 'selected' : '' ?>>Sim</option>
											<option value="0" <?= empty($u['phone_has_whatsapp']) ? 'selected' : '' ?>>Não</option>
										</select>
									</div>
									<div class="col-md-4">
										<label class="form-label">Nascimento</label>
										<input class="form-control" type="text" name="birth_date" value="<?= View::e((string)$u['birth_date']) ?>" required>
									</div>
									<div class="col-md-8">
										<label class="form-label">Observações</label>
										<textarea class="form-control" name="observations" rows="2"><?= View::e((string)($u['observations'] ?? '')) ?></textarea>
									</div>
									<div class="col-md-4">
										<label class="form-label">Tier</label>
										<select name="access_tier" class="form-select" <?= $canEdit ? '' : 'disabled' ?>>
											<?php foreach (['user','trial','assinante','restrito','vitalicio'] as $tier): ?>
												<option value="<?= $tier ?>" <?= $u['access_tier'] === $tier ? 'selected' : '' ?>><?= $tier ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
								<?php if (!$isSuper): ?>
									<hr class="my-3">
									<div class="d-flex flex-wrap gap-2">
										<button class="btn btn-sm btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#assignPackageModal<?= (int)$u['id'] ?>">
											<i class="fa-solid fa-box"></i>
											<span class="ms-1">Definir pacote</span>
										</button>
										<button class="btn btn-sm <?= $isTeam ? 'btn-outline-secondary' : 'btn-outline-warning' ?>" type="submit" formaction="<?= base_path('/admin/users/team-toggle') ?>" formmethod="post" formnovalidate onclick="return confirm('Alterar equipe deste usuario?');">
											<i class="fa-solid fa-people-group"></i>
											<span class="ms-1"><?= $isTeam ? 'Remover equipe' : 'Tornar equipe' ?></span>
										</button>
										<button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#resetUserModal<?= (int)$u['id'] ?>">
											<i class="fa-solid fa-key"></i>
											<span class="ms-1">Trocar senha</span>
										</button>
									</div>
								<?php endif; ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
								<button class="btn btn-primary" type="submit" <?= $canEdit ? '' : 'disabled' ?>>Salvar</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<?php
			$modals[] = ob_get_clean();
			ob_start();
			?>
			<div class="modal fade" id="assignPackageModal<?= (int)$u['id'] ?>" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Definir pacote</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
						</div>
						<form method="post" action="<?= base_path('/admin/users/assign-package') ?>">
							<div class="modal-body">
								<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
								<input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
								<div class="mb-2">
									<strong><?= View::e((string)$u['username']) ?></strong>
								</div>
								<?php
								$currentPayment = $latestPayments[(int)$u['id']] ?? null;
								$currentPkgId = (int)($currentPayment['package_id'] ?? 0);
								$currentPkg = $packageMap[$currentPkgId] ?? null;
								?>
								<div class="text-muted small mb-2">
									Atual: <?= $currentPkg ? View::e((string)($currentPkg['title'] ?? '')) : 'Sem pacote ativo' ?>
								</div>
								<div class="mb-3">
									<label class="form-label">Pacote</label>
									<select class="form-select" name="package_id" required>
										<option value="">Selecione</option>
										<?php foreach (($packages ?? []) as $pkg): ?>
											<option value="<?= (int)$pkg['id'] ?>"><?= View::e((string)($pkg['title'] ?? '')) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="mb-3">
									<label class="form-label">Meses</label>
									<input class="form-control" type="number" name="months" min="1" max="12" value="1" required>
									<div class="form-text">Gera pagamento aprovado e estende a assinatura.</div>
								</div>
								<div class="form-check mb-2">
									<input class="form-check-input" type="checkbox" name="assign_only" id="assignOnly<?= (int)$u['id'] ?>" value="1">
									<label class="form-check-label" for="assignOnly<?= (int)$u['id'] ?>">
										Somente definir acesso ao pacote (nao altera assinatura/creditos)
									</label>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
								<button class="btn btn-success" type="submit">Aplicar pacote</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<?php
			$modals[] = ob_get_clean();
			ob_start();
			?>
			<div class="modal fade" id="loginHistoryModal<?= (int)$u['id'] ?>" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Historico de login</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
						</div>
						<div class="modal-body">
							<div class="mb-2"><strong><?= View::e((string)$u['username']) ?></strong></div>
							<?php
							$logs = $loginHistory[(int)$u['id']] ?? [];
							?>
							<?php if (empty($logs)): ?>
								<div class="text-muted">Sem registros de login.</div>
							<?php else: ?>
								<div class="table-responsive">
									<table class="table table-sm align-middle mb-0">
										<thead class="table-light">
										<tr>
											<th scope="col" style="width: 170px;">Data</th>
											<th scope="col" style="width: 140px;">Hora</th>
											<th scope="col">IP</th>
										</tr>
										</thead>
										<tbody>
										<?php foreach ($logs as $log): ?>
											<?php
												$dt = (string)($log['logged_at'] ?? '');
												$date = $dt !== '' ? date('Y-m-d', strtotime($dt)) : '-';
												$time = $dt !== '' ? date('H:i:s', strtotime($dt)) : '-';
											?>
											<tr>
												<td><?= View::e($date) ?></td>
												<td><?= View::e($time) ?></td>
												<td><?= View::e((string)($log['ip_address'] ?? '-')) ?></td>
											</tr>
										<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							<?php endif; ?>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
						</div>
					</div>
				</div>
			</div>
			<?php
			$modals[] = ob_get_clean();
			ob_start();
			?>
			<div class="modal fade" id="resetUserModal<?= (int)$u['id'] ?>" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Confirmar reset de senha</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
						</div>
						<div class="modal-body">
							Tem certeza que deseja gerar um link de reset para
							<strong><?= View::e((string)$u['username']) ?></strong>?
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
							<form method="post" action="<?= base_path('/admin/users/reset') ?>">
								<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
								<input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
								<button class="btn btn-primary" type="submit">Gerar link</button>
							</form>
						</div>
					</div>
				</div>
			</div>
			<?php
			$modals[] = ob_get_clean();
			?>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php
$pages = (int)max(1, ($pages ?? 1));
$curr = (int)($page ?? 1);
$curr = min(max(1, $curr), $pages);
$start = max(1, $curr - 2);
$end = min($pages, $curr + 2);
$baseParams = [
	'q' => $filterQ,
	'tier' => $filterTier,
	'status' => $filterStatus,
	'package' => $filterPackage,
	'perPage' => (int)($perPage ?? 25),
];
$baseParams = array_filter($baseParams, static fn ($v) => $v !== '' && $v !== null);
$base = '/admin/users';
if (!empty($baseParams)) {
	$base .= '?' . http_build_query($baseParams) . '&page=';
} else {
	$base .= '?page=';
}
?>
<?php if ($pages > 1): ?>
	<nav class="d-flex justify-content-end">
		<ul class="pagination pagination-sm mb-0">
			<li class="page-item <?= $curr <= 1 ? 'disabled' : '' ?>">
				<a class="page-link" href="<?= base_path($base . '1') ?>" aria-label="Primeira">«</a>
			</li>
			<li class="page-item <?= $curr <= 1 ? 'disabled' : '' ?>">
				<a class="page-link" href="<?= base_path($base . ($curr - 1)) ?>" aria-label="Anterior">‹</a>
			</li>
			<?php if ($start > 1): ?>
				<li class="page-item"><a class="page-link" href="<?= base_path($base . '1') ?>">1</a></li>
				<?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
			<?php endif; ?>
			<?php for ($i = $start; $i <= $end; $i++): ?>
				<li class="page-item <?= $i === $curr ? 'active' : '' ?>"><a class="page-link" href="<?= base_path($base . $i) ?>"><?= $i ?></a></li>
			<?php endfor; ?>
			<?php if ($end < $pages): ?>
				<?php if ($end < $pages - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
				<li class="page-item"><a class="page-link" href="<?= base_path($base . $pages) ?>"><?= $pages ?></a></li>
			<?php endif; ?>
			<li class="page-item <?= $curr >= $pages ? 'disabled' : '' ?>">
				<a class="page-link" href="<?= base_path($base . ($curr + 1)) ?>" aria-label="Próxima">›</a>
			</li>
			<li class="page-item <?= $curr >= $pages ? 'disabled' : '' ?>">
				<a class="page-link" href="<?= base_path($base . $pages) ?>" aria-label="Última">»</a>
			</li>
		</ul>
	</nav>
<?php endif; ?>
<?php if (!empty($modals ?? [])): ?>
	<?php foreach ($modals as $m): ?>
		<?= $m ?>
	<?php endforeach; ?>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
