<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Segurança</h1>

<div class="card mb-4">
	<div class="card-body">
		<h2 class="h6">Email blocklist</h2>
		<form method="post" action="<?= base_path('/admin/security/email-blocklist/add') ?>" class="row g-2">
			<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
			<div class="col-md-4">
				<input class="form-control" name="domain" placeholder="dominio.com" required>
			</div>
			<div class="col-md-2 d-grid">
				<button class="btn btn-primary" type="submit">Adicionar</button>
			</div>
		</form>
		<div class="table-responsive mt-3">
			<table class="table table-sm">
				<thead><tr><th>Domínio</th><th class="text-end">Ações</th></tr></thead>
				<tbody>
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
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<h2 class="h6">Blocklist de usuários</h2>
		<form method="post" action="<?= base_path('/admin/security/user-blocklist/add') ?>" class="row g-2">
			<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
			<div class="col-md-3">
				<input class="form-control" name="identifier" placeholder="usuário ou email" required>
			</div>
			<div class="col-md-5">
				<input class="form-control" name="reason" placeholder="Motivo (opcional)">
			</div>
			<div class="col-md-2 d-grid">
				<button class="btn btn-primary" type="submit">Bloquear</button>
			</div>
		</form>
		<div class="table-responsive mt-3">
			<table class="table table-sm">
				<thead><tr><th>User ID</th><th>Motivo</th><th>Status</th><th class="text-end">Ações</th></tr></thead>
				<tbody>
				<?php foreach (($userBlocks ?? []) as $b): ?>
					<tr>
						<td><?= (int)$b['user_id'] ?></td>
						<td><?= View::e((string)$b['reason']) ?></td>
						<td><?= (int)$b['active'] === 1 ? 'ativo' : 'inativo' ?></td>
						<td class="text-end">
							<?php if ((int)$b['active'] === 1): ?>
								<form method="post" action="<?= base_path('/admin/security/user-blocklist/remove') ?>" class="d-inline">
									<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
									<input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
									<button class="btn btn-sm btn-outline-danger" type="submit">Desbloquear</button>
								</form>
							<?php else: ?>
								<span class="text-muted">-</span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
