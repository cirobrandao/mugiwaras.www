<?php
use App\Core\View;
ob_start();
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
	<div>
		<h1 class="h4 mb-0">Importacao de Usuarios</h1>
		<div class="text-muted small">Valide os registros antes de importar.</div>
	</div>
	<div class="d-flex flex-wrap gap-2">
		<a class="btn btn-outline-secondary" href="<?= base_path('/admin/users') ?>">Voltar para usuarios</a>
		<a class="btn btn-outline-primary" href="<?= base_path('/admin/team') ?>">Gerenciar Equipes</a>
	</div>
</div>

<?php if (!empty($importResult)): ?>
	<div class="alert alert-info">
		<strong>Importacao concluida.</strong>
		<span class="ms-2">Importados: <?= (int)($importResult['imported'] ?? 0) ?>.</span>
		<span class="ms-2">Ignorados: <?= (int)($importResult['skipped'] ?? 0) ?>.</span>
		<span class="ms-2">Falhas: <?= (int)($importResult['failed'] ?? 0) ?>.</span>
	</div>
<?php endif; ?>

<div id="import-json" class="card mb-4">
	<div class="card-body">
		<h2 class="h6">Importar usuarios (JSON)</h2>
		<p class="text-muted small mb-2">Cole um JSON unico ou uma lista de usuarios e valide antes de importar.</p>
		<?php if (!empty($importErrors)): ?>
			<div class="alert alert-danger">
				<?= View::e(implode(' ', (array)$importErrors)) ?>
			</div>
		<?php endif; ?>
		<form method="post" action="<?= base_path('/admin/users/import-preview') ?>">
			<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
			<textarea class="form-control" name="import_json" rows="6" placeholder='[{"username":"usuario"}]'><?= View::e((string)($importJson ?? '')) ?></textarea>
			<div class="d-flex flex-wrap gap-2 mt-2">
				<button class="btn btn-primary" type="submit">Validar JSON</button>
				<?php if (!empty($importPreview)): ?>
					<a class="btn btn-outline-secondary" href="<?= base_path('/admin/users/import#import-json') ?>">Limpar</a>
				<?php endif; ?>
			</div>
		</form>
	</div>
</div>

<?php if (!empty($importPreview)): ?>
	<?php
		$importSummary = (array)($importSummary ?? []);
		$totalRows = (int)($importSummary['total'] ?? count($importPreview));
		$validRows = (int)($importSummary['valid'] ?? 0);
		$errorRows = (int)($importSummary['errors'] ?? 0);
		$warnRows = (int)($importSummary['warnings'] ?? 0);
	?>
	<div class="alert alert-secondary">
		<strong>Preview:</strong>
		<span class="ms-2">Total: <?= $totalRows ?>.</span>
		<span class="ms-2">Validos: <?= $validRows ?>.</span>
		<span class="ms-2">Com erros: <?= $errorRows ?>.</span>
		<span class="ms-2">Com avisos: <?= $warnRows ?>.</span>
	</div>
	<form method="post" action="<?= base_path('/admin/users/import-apply') ?>" class="mb-4">
		<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
		<textarea name="import_json" class="d-none"><?= View::e((string)($importJson ?? '')) ?></textarea>
		<div class="table-responsive">
			<table class="table table-sm align-middle">
				<thead class="table-light">
				<tr>
					<th scope="col" style="width: 36px;"></th>
					<th scope="col" style="width: 110px;">Acao</th>
					<th scope="col">Usuario</th>
					<th scope="col">Email</th>
					<th scope="col" style="width: 140px;">Telefone</th>
					<th scope="col" style="width: 120px;">Nascimento</th>
					<th scope="col" style="width: 120px;">Tier</th>
					<th scope="col" style="width: 120px;">Role</th>
					<th scope="col" style="width: 170px;">Registro</th>
					<th scope="col" style="width: 170px;">Ultimo login</th>
					<th scope="col" style="width: 180px;">Destino</th>
					<th scope="col">Avisos/erros</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($importPreview as $item): ?>
					<?php
						$mapped = (array)($item['mapped'] ?? []);
						$errors = (array)($item['errors'] ?? []);
						$warnings = (array)($item['warnings'] ?? []);
						$hasErrors = !empty($errors);
						$defaultAction = (string)($item['default_action'] ?? 'skip');
						$match = $item['match'] ?? null;
					?>
					<tr class="<?= $hasErrors ? 'table-danger' : (!empty($warnings) ? 'table-warning' : '') ?>">
						<td>
							<input class="form-check-input" type="checkbox" name="import_rows[]" value="<?= (int)$item['index'] ?>" <?= $hasErrors ? '' : 'checked' ?>>
						</td>
						<td>
							<select class="form-select form-select-sm" name="import_action[<?= (int)$item['index'] ?>]">
								<option value="create" <?= $defaultAction === 'create' ? 'selected' : '' ?>>Criar</option>
								<option value="update" <?= $defaultAction === 'update' ? 'selected' : '' ?>>Atualizar</option>
								<option value="skip" <?= $defaultAction === 'skip' ? 'selected' : '' ?>>Ignorar</option>
							</select>
						</td>
						<td><?= View::e((string)($mapped['username'] ?? '-')) ?></td>
						<td><?= View::e((string)($mapped['email'] ?? '-')) ?></td>
						<td><?= View::e((string)($mapped['phone'] ?? '-')) ?></td>
						<td><?= View::e((string)($mapped['birth_date'] ?? '-')) ?></td>
						<td><?= View::e((string)($mapped['access_tier'] ?? '-')) ?></td>
						<td><?= View::e((string)($mapped['role'] ?? '-')) ?></td>
						<td><?= View::e((string)($mapped['data_registro'] ?? '-')) ?></td>
						<td><?= View::e((string)($mapped['data_ultimo_login'] ?? '-')) ?></td>
						<td>
							<?php if (!empty($match)): ?>
								#<?= (int)($match['id'] ?? 0) ?> - <?= View::e((string)($match['username'] ?? '')) ?>
							<?php else: ?>
								Novo usuario
							<?php endif; ?>
						</td>
						<td>
							<?php if ($errors): ?>
								<div class="text-danger small"><?= View::e(implode(' ', $errors)) ?></div>
							<?php endif; ?>
							<?php if ($warnings): ?>
								<div class="text-muted small"><?= View::e(implode(' ', $warnings)) ?></div>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<button class="btn btn-success" type="submit">Importar selecionados</button>
	</form>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
