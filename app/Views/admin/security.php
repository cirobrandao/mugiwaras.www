<?php
use App\Core\View;
ob_start();
$status = (string)($_GET['status'] ?? '');
$testEmail = (string)($testEmail ?? '');
$testResult = $testResult ?? null;
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
	<div>
		<h1 class="h4 mb-1">Email blocklist</h1>
		<div class="text-muted small">Bloqueie dominios temporarios e teste regras.</div>
	</div>
	<a class="btn btn-sm btn-outline-secondary" href="<?= base_path('/admin/security/user-blocklist') ?>">Blocklist de usuarios</a>
</div>

<?php if ($status === 'created'): ?>
	<div class="alert alert-success">Dominio adicionado.</div>
<?php elseif ($status === 'exists'): ?>
	<div class="alert alert-warning">Dominio ja existe.</div>
<?php elseif ($status === 'invalid'): ?>
	<div class="alert alert-danger">Dominio invalido. Use algo como exemplo.com ou email@exemplo.com.</div>
<?php elseif ($status === 'removed'): ?>
	<div class="alert alert-success">Dominio removido.</div>
<?php endif; ?>

<div class="row g-3">
	<div class="col-lg-5">
		<div class="card h-100">
			<div class="card-body">
				<h2 class="h6">Adicionar dominio</h2>
				<form method="post" action="<?= base_path('/admin/security/email-blocklist/add') ?>" class="row g-2">
					<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
					<div class="col-12">
						<label class="form-label">Dominio ou email</label>
						<input class="form-control" name="domain" placeholder="exemplo.com" required>
						<div class="form-text">Aceita dominio (ex: tempmail.com) ou email (ex: user@tempmail.com).</div>
					</div>
					<div class="col-12 d-grid">
						<button class="btn btn-primary" type="submit">Adicionar a blocklist</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-lg-7">
		<div class="card h-100">
			<div class="card-body">
				<h2 class="h6">Testar email</h2>
				<form method="get" action="<?= base_path('/admin/security/email-blocklist') ?>" class="row g-2">
					<div class="col-md-8">
						<input class="form-control" name="test_email" value="<?= View::e($testEmail) ?>" placeholder="teste@dominio.com">
					</div>
					<div class="col-md-4 d-grid">
						<button class="btn btn-outline-primary" type="submit">Testar</button>
					</div>
				</form>
				<?php if ($testEmail !== ''): ?>
					<div class="mt-3">
						<?php if ($testResult === true): ?>
							<div class="alert alert-danger mb-0">Bloqueado pela blocklist.</div>
						<?php elseif ($testResult === false): ?>
							<div class="alert alert-success mb-0">Permitido (nao esta na blocklist).</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<div class="card mt-3">
	<div class="card-body">
		<div class="d-flex align-items-center justify-content-between mb-2">
			<h2 class="h6 mb-0">Dominios bloqueados</h2>
			<span class="badge bg-light text-muted border"><?= count($emails ?? []) ?> registros</span>
		</div>
		<div class="table-responsive">
			<table class="table table-sm align-middle mb-0">
				<thead class="table-light">
				<tr>
					<th>Dominio</th>
					<th class="text-end">Acoes</th>
				</tr>
				</thead>
				<tbody>
				<?php if (empty($emails)): ?>
					<tr><td colspan="2" class="text-muted">Nenhum dominio bloqueado.</td></tr>
				<?php else: ?>
					<?php foreach (($emails ?? []) as $e): ?>
						<tr>
							<td><?= View::e($e['domain']) ?></td>
							<td class="text-end">
								<form method="post" action="<?= base_path('/admin/security/email-blocklist/remove') ?>" class="d-inline">
									<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
									<input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
									<button class="btn btn-sm btn-outline-danger" type="submit">Remover</button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
