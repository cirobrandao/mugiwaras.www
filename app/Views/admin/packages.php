<?php
use App\Core\View;
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
	<h1 class="h4 mb-0"><i class="bi bi-box-seam me-2"></i>Pacotes</h1>
	<button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#packageCreateModal">
		<i class="bi bi-plus-circle me-1"></i>Adicionar
	</button>
</div>
<hr class="text-success" />
<?php if (($error ?? '') === 'has_payments'): ?>
	<div class="alert alert-warning">
		<i class="bi bi-exclamation-triangle me-2"></i>Não é possível excluir pacotes com pagamentos registrados.
	</div>
<?php endif; ?>

<div class="modal fade" id="packageCreateModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered admin-packages-modal">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Adicionar pacote</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?= base_path('/admin/packages/create') ?>" class="row g-3">
					<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
					<div class="col-9">
						<label class="form-label"><i class="bi bi-tag me-1"></i>Título</label>
						<input class="form-control" name="title" placeholder="Ex: Premium Mensal" required>
					</div>
					<div class="col-3">
						<label class="form-label"><i class="bi bi-sort-numeric-down me-1"></i>Ordem</label>
						<input class="form-control" name="sort_order" type="number" min="0" value="0">
					</div>
					<div class="col-12">
						<label class="form-label"><i class="bi bi-file-text me-1"></i>Descrição</label>
						<input class="form-control" name="description" placeholder="Descrição curta do pacote">
					</div>
					<div class="col-4">
						<label class="form-label"><i class="bi bi-currency-dollar me-1"></i>Preço</label>
						<input class="form-control" name="price" type="number" step="0.01" placeholder="0.00">
					</div>
					<div class="col-4">
						<label class="form-label"><i class="bi bi-gift me-1"></i>Bônus (dias)</label>
						<input class="form-control" name="bonus_credits" type="number" placeholder="0">
					</div>
					<div class="col-4">
						<label class="form-label"><i class="bi bi-calendar-check me-1"></i>Dias de acesso</label>
						<input class="form-control" name="subscription_days" type="number" placeholder="30">
					</div>
					<div class="col-12">
						<label class="form-label"><i class="bi bi-folder me-1"></i>Categorias do pacote</label>
						<select class="form-select" name="categories[]" multiple size="6">
							<?php foreach (($categories ?? []) as $c): ?>
								<option value="<?= (int)$c['id'] ?>"><?= View::e((string)$c['name']) ?></option>
							<?php endforeach; ?>
						</select>
						<div class="form-text">Use Ctrl/Cmd para selecionar várias categorias.</div>
					</div>
					<div class="col-12 d-flex justify-content-end gap-2 mt-2">
						<button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">
							<i class="bi bi-x-lg me-1"></i>Cancelar
						</button>
						<button class="btn btn-primary" type="submit">
							<i class="bi bi-check-lg me-1"></i>Criar pacote
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="admin-packages-table">
	<table class="table">
		<thead>
		<tr>
			<th style="width: 220px;">Título</th>
			<th style="width: 300px;">Categorias</th>
			<th style="width: 120px;">Preço</th>
			<th style="width: 140px;">Dias/Bônus</th>
			<th style="width: 80px;">Ordem</th>
			<th style="width: 200px;" class="text-end">Ações</th>
		</tr>
		</thead>
		<tbody>
		<?php if (empty($packages)): ?>
			<tr>
				<td colspan="6" class="text-muted text-center py-4">
					<i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
					Nenhum pacote cadastrado.
				</td>
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
				<td>
					<div class="package-title">
						<i class="bi bi-box-seam text-primary me-2"></i>
						<strong><?= View::e((string)$p['title']) ?></strong>
					</div>
					<?php if (!empty($p['description'])): ?>
						<div class="package-description text-muted small mt-1">
							<?= View::e((string)$p['description']) ?>
						</div>
					<?php endif; ?>
				</td>
				<td>
					<?php if (empty($categoryNames)): ?>
						<span class="text-muted small">
							<i class="bi bi-dash-circle me-1"></i>Sem categorias
						</span>
					<?php else: ?>
						<div class="d-flex flex-wrap gap-1">
							<?php foreach ($categoryNames as $catName): ?>
								<span class="badge bg-secondary"><?= View::e($catName) ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</td>
				<td>
					<div class="package-price">
						<?= View::e(format_brl((float)($p['price'] ?? 0))) ?>
					</div>
				</td>
				<td>
					<?php $days = (int)($p['subscription_days'] ?? 0); ?>
					<?php $bonus = (int)($p['bonus_credits'] ?? 0); ?>
					<div class="package-days">
						<i class="bi bi-calendar-check text-success me-1"></i>
						<span class="fw-semibold"><?= $days ?> dias</span>
						<?php if ($bonus > 0): ?>
							<div class="small text-success">
								<i class="bi bi-gift me-1"></i>+<?= $bonus ?> bônus
							</div>
						<?php endif; ?>
					</div>
				</td>
				<td>
					<span class="badge bg-secondary"><?= (int)($p['sort_order'] ?? 0) ?></span>
				</td>
				<td>
					<div class="admin-actions">
						<button
							class="btn btn-sm btn-outline-primary btn-edit-package"
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
						>
							<i class="bi bi-pencil"></i> Editar
						</button>
						<form method="post" action="<?= base_path('/admin/packages/delete') ?>" class="d-inline">
							<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
							<input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
							<button 
								class="btn btn-sm btn-outline-danger" 
								type="submit" 
								<?= $hasPayments ? 'disabled' : '' ?> 
								title="<?= $hasPayments ? 'Possui pagamentos' : 'Excluir' ?>"
								onclick="return confirm('Tem certeza que deseja excluir este pacote?')"
							>
								<i class="bi bi-trash"></i> Excluir
							</button>
						</form>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

<div class="modal fade" id="packageEditModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered admin-packages-modal">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar pacote</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?= base_path('/admin/packages/update') ?>" class="row g-3" id="packageEditForm">
					<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
					<input type="hidden" name="id" value="">
					<div class="col-9">
						<label class="form-label"><i class="bi bi-tag me-1"></i>Título</label>
						<input class="form-control" name="title" placeholder="Ex: Premium Mensal" required>
					</div>
					<div class="col-3">
						<label class="form-label"><i class="bi bi-sort-numeric-down me-1"></i>Ordem</label>
						<input class="form-control" name="sort_order" type="number" min="0" value="0">
					</div>
					<div class="col-12">
						<label class="form-label"><i class="bi bi-file-text me-1"></i>Descrição</label>
						<input class="form-control" name="description" placeholder="Descrição curta do pacote">
					</div>
					<div class="col-4">
						<label class="form-label"><i class="bi bi-currency-dollar me-1"></i>Preço</label>
						<input class="form-control" name="price" type="number" step="0.01" placeholder="0.00">
					</div>
					<div class="col-4">
						<label class="form-label"><i class="bi bi-gift me-1"></i>Bônus (dias)</label>
						<input class="form-control" name="bonus_credits" type="number" placeholder="0">
					</div>
					<div class="col-4">
						<label class="form-label"><i class="bi bi-calendar-check me-1"></i>Dias de acesso</label>
						<input class="form-control" name="subscription_days" type="number" placeholder="30">
					</div>
					<div class="col-12">
						<label class="form-label"><i class="bi bi-folder me-1"></i>Categorias do pacote</label>
						<select class="form-select" name="categories[]" multiple size="6">
							<?php foreach (($categories ?? []) as $c): ?>
								<option value="<?= (int)$c['id'] ?>"><?= View::e((string)$c['name']) ?></option>
							<?php endforeach; ?>
						</select>
						<div class="form-text">Use Ctrl/Cmd para selecionar várias categorias.</div>
					</div>
					<div class="col-12 d-flex justify-content-end gap-2 mt-2">
						<button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">
							<i class="bi bi-x-lg me-1"></i>Cancelar
						</button>
						<button class="btn btn-primary" type="submit">
							<i class="bi bi-check-lg me-1"></i>Salvar alterações
						</button>
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
