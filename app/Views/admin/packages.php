<?php
use App\Core\View;
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
	<h1 class="h4 mb-0">Pacotes</h1>
	<button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#packageCreateModal">Adicionar</button>
</div>

<?php if (($error ?? '') === 'has_payments'): ?>
	<div class="alert alert-warning">Não é possível excluir pacotes com pagamentos registrados.</div>
<?php endif; ?>

<div class="modal fade" id="packageCreateModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Adicionar pacote</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?= base_path('/admin/packages/create') ?>" class="row g-2">
					<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
					<div class="col-md-4">
						<label class="form-label">Título</label>
						<input class="form-control" name="title" required>
					</div>
					<div class="col-md-4">
						<label class="form-label">Descrição</label>
						<input class="form-control" name="description">
					</div>
					<div class="col-md-2">
						<label class="form-label">Ordem</label>
						<input class="form-control" name="sort_order" type="number" min="0" value="0">
					</div>
					<div class="col-md-2">
						<label class="form-label">Preço</label>
						<input class="form-control" name="price" type="number" step="0.01">
					</div>
					<div class="col-md-2">
						<label class="form-label">Bônus</label>
						<input class="form-control" name="bonus_credits" type="number">
					</div>
					<div class="col-md-2">
						<label class="form-label">Dias</label>
						<input class="form-control" name="subscription_days" type="number">
					</div>
					<div class="col-md-8">
						<label class="form-label">Categorias do pacote</label>
						<select class="form-select" name="categories[]" multiple>
							<?php foreach (($categories ?? []) as $c): ?>
								<option value="<?= (int)$c['id'] ?>"><?= View::e((string)$c['name']) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-4 d-grid align-self-end">
						<button class="btn btn-primary" type="submit">Criar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="table-responsive">
	<table class="table table-sm">
		<thead>
		<tr>
			<th>Título</th>
			<th>Descrição</th>
			<th>Ordem</th>
			<th>Categorias</th>
			<th>Preço</th>
			<th>Bônus</th>
			<th>Dias</th>
			<th class="text-end">Ações</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach (($packages ?? []) as $p): ?>
			<?php
			$selectedCats = $packageCategories[(int)$p['id']] ?? [];
			$hasPayments = !empty(($packagePayments ?? [])[(int)$p['id']]);
			?>
			<tr>
				<td>
					<input class="form-control form-control-sm" form="pkg-<?= (int)$p['id'] ?>" name="title" value="<?= View::e($p['title']) ?>">
				</td>
				<td>
					<input class="form-control form-control-sm" form="pkg-<?= (int)$p['id'] ?>" name="description" value="<?= View::e((string)$p['description']) ?>">
				</td>
				<td>
					<input class="form-control form-control-sm" form="pkg-<?= (int)$p['id'] ?>" name="sort_order" type="number" min="0" value="<?= (int)($p['sort_order'] ?? 0) ?>">
				</td>
				<td>
					<select class="form-select form-select-sm" form="pkg-<?= (int)$p['id'] ?>" name="categories[]" multiple>
						<?php foreach (($categories ?? []) as $c): ?>
							<?php $cid = (int)$c['id']; ?>
							<option value="<?= $cid ?>" <?= in_array($cid, $selectedCats, true) ? 'selected' : '' ?>>
								<?= View::e((string)$c['name']) ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>
					<input class="form-control form-control-sm" form="pkg-<?= (int)$p['id'] ?>" name="price" type="number" step="0.01" value="<?= View::e((string)$p['price']) ?>">
				</td>
				<td>
					<input class="form-control form-control-sm" form="pkg-<?= (int)$p['id'] ?>" name="bonus_credits" type="number" value="<?= (int)$p['bonus_credits'] ?>">
				</td>
				<td>
					<input class="form-control form-control-sm" form="pkg-<?= (int)$p['id'] ?>" name="subscription_days" type="number" value="<?= (int)$p['subscription_days'] ?>">
				</td>
				<td class="text-end">
					<form id="pkg-<?= (int)$p['id'] ?>" method="post" action="<?= base_path('/admin/packages/update') ?>" class="d-inline">
						<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
						<input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
						<button class="btn btn-sm btn-primary" type="submit">Salvar</button>
					</form>
					<form method="post" action="<?= base_path('/admin/packages/delete') ?>" class="d-inline">
						<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
						<input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
						<button class="btn btn-sm btn-outline-danger" type="submit" <?= $hasPayments ? 'disabled' : '' ?> title="<?= $hasPayments ? 'Possui pagamentos' : 'Excluir' ?>">Excluir</button>
					</form>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
