<?php
use App\Core\View;
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
	<h1 class="h4 mb-0">Pacotes</h1>
	<button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#packageCreateModal">Adicionar</button>
</div>
<hr class="text-success" />
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
				<form method="post" action="<?= base_path('/admin/packages/create') ?>" class="row g-3">
					<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
					<div class="col-9">
						<label class="form-label">Título</label>
						<input class="form-control" name="title" placeholder="Ex: Premium Mensal" required>
					</div>
					<div class="col-3">
						<label class="form-label">Ordem</label>
						<input class="form-control" name="sort_order" type="number" min="0" value="0">
					</div>
					<div class="col-12">
						<label class="form-label">Descrição</label>
						<input class="form-control" name="description" placeholder="Descrição curta do pacote">
					</div>
					<div class="col-4">
						<label class="form-label">Preço</label>
						<input class="form-control" name="price" type="number" step="0.01">
					</div>
					<div class="col-4">
						<label class="form-label">Bônus</label>
						<input class="form-control" name="bonus_credits" type="number">
					</div>
					<div class="col-4">
						<label class="form-label">Dias</label>
						<input class="form-control" name="subscription_days" type="number">
					</div>
					<div class="col-12">
						<label class="form-label">Categorias do pacote</label>
						<select class="form-select" name="categories[]" multiple size="6">
							<?php foreach (($categories ?? []) as $c): ?>
								<option value="<?= (int)$c['id'] ?>"><?= View::e((string)$c['name']) ?></option>
							<?php endforeach; ?>
						</select>
						<div class="form-text">Use Ctrl/Cmd para selecionar várias categorias.</div>
					</div>
					<div class="col-12 d-flex justify-content-end gap-2 mt-2">
						<button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
						<button class="btn btn-primary" type="submit">Criar pacote</button>
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
			<th>Categorias</th>
			<th>Preço</th>
			<th>Dias</th>
			<th class="text-end">Ações</th>
		</tr>
		</thead>
		<tbody>
		<?php if (empty($packages)): ?>
			<tr>
				<td colspan="5" class="text-muted">Nenhum pacote cadastrado.</td>
			</tr>
		<?php endif; ?>
		<?php foreach (($packages ?? []) as $p): ?>
			<?php
			$selectedCats = $packageCategories[(int)$p['id']] ?? [];
			$hasPayments = !empty(($packagePayments ?? [])[(int)$p['id']]);
			$selectedCatsCsv = implode(',', array_map('intval', $selectedCats));
			$categoryNames = [];
			foreach (($categories ?? []) as $c) {
				$cid = (int)($c['id'] ?? 0);
				if (in_array($cid, $selectedCats, true)) {
					$categoryNames[] = (string)($c['name'] ?? '');
				}
			}
			?>
			<tr>
				<td><?= View::e((string)$p['title']) ?></td>
				<td>
					<?php if (empty($categoryNames)): ?>
						<span class="text-muted">Sem categorias</span>
					<?php else: ?>
						<div class="d-flex flex-wrap gap-1">
							<?php foreach ($categoryNames as $catName): ?>
								<span class="badge bg-secondary"><?= View::e($catName) ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</td>
				<td><?= View::e(format_brl((float)($p['price'] ?? 0))) ?></td>
				<td>
					<?php $days = (int)($p['subscription_days'] ?? 0); ?>
					<?php $bonus = (int)($p['bonus_credits'] ?? 0); ?>
					<?= $days ?><?= $bonus >= 1 ? ('(+' . $bonus . ')') : '' ?>
				</td>
				<td class="text-end">
					<button
						class="btn btn-sm btn-primary btn-edit-package"
						type="button"
						data-bs-toggle="modal"
						data-bs-target="#packageEditModal"
						data-id="<?= (int)$p['id'] ?>"
						data-title="<?= View::e((string)$p['title']) ?>"
						data-sort-order="<?= (int)($p['sort_order'] ?? 0) ?>"
						data-description="<?= View::e((string)($p['description'] ?? '')) ?>"
						data-price="<?= View::e((string)$p['price']) ?>"
						data-bonus-credits="<?= (int)$p['bonus_credits'] ?>"
						data-subscription-days="<?= (int)$p['subscription_days'] ?>"
						data-categories="<?= View::e($selectedCatsCsv) ?>"
					>Editar</button>
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

<div class="modal fade" id="packageEditModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Editar pacote</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?= base_path('/admin/packages/update') ?>" class="row g-3" id="packageEditForm">
					<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
					<input type="hidden" name="id" value="">
					<div class="col-9">
						<label class="form-label">Título</label>
						<input class="form-control" name="title" placeholder="Ex: Premium Mensal" required>
					</div>
					<div class="col-3">
						<label class="form-label">Ordem</label>
						<input class="form-control" name="sort_order" type="number" min="0" value="0">
					</div>
					<div class="col-12">
						<label class="form-label">Descrição</label>
						<input class="form-control" name="description" placeholder="Descrição curta do pacote">
					</div>
					<div class="col-4">
						<label class="form-label">Preço</label>
						<input class="form-control" name="price" type="number" step="0.01">
					</div>
					<div class="col-4">
						<label class="form-label">Bônus</label>
						<input class="form-control" name="bonus_credits" type="number">
					</div>
					<div class="col-4">
						<label class="form-label">Dias</label>
						<input class="form-control" name="subscription_days" type="number">
					</div>
					<div class="col-12">
						<label class="form-label">Categorias do pacote</label>
						<select class="form-select" name="categories[]" multiple size="6">
							<?php foreach (($categories ?? []) as $c): ?>
								<option value="<?= (int)$c['id'] ?>"><?= View::e((string)$c['name']) ?></option>
							<?php endforeach; ?>
						</select>
						<div class="form-text">Use Ctrl/Cmd para selecionar várias categorias.</div>
					</div>
					<div class="col-12 d-flex justify-content-end gap-2 mt-2">
						<button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
						<button class="btn btn-primary" type="submit">Salvar alterações</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const editModal = document.getElementById('packageEditModal');
	if (!editModal) {
		return;
	}

	const editForm = document.getElementById('packageEditForm');
	if (!editForm) {
		return;
	}

	const categorySelect = editForm.querySelector('select[name="categories[]"]');

	document.querySelectorAll('.btn-edit-package').forEach(function (button) {
		button.addEventListener('click', function () {
			editForm.querySelector('input[name="id"]').value = button.getAttribute('data-id') || '';
			editForm.querySelector('input[name="title"]').value = button.getAttribute('data-title') || '';
			editForm.querySelector('input[name="sort_order"]').value = button.getAttribute('data-sort-order') || '0';
			editForm.querySelector('input[name="description"]').value = button.getAttribute('data-description') || '';
			editForm.querySelector('input[name="price"]').value = button.getAttribute('data-price') || '';
			editForm.querySelector('input[name="bonus_credits"]').value = button.getAttribute('data-bonus-credits') || '';
			editForm.querySelector('input[name="subscription_days"]').value = button.getAttribute('data-subscription-days') || '';

			const categories = (button.getAttribute('data-categories') || '')
				.split(',')
				.map(function (item) { return item.trim(); })
				.filter(function (item) { return item.length > 0; });

			if (categorySelect) {
				Array.from(categorySelect.options).forEach(function (option) {
					option.selected = categories.includes(option.value);
				});
			}
		});
	});
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
