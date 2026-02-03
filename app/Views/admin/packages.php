<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Pacotes</h1>

<div class="card mb-4">
	<div class="card-body">
		<h2 class="h6">Novo pacote</h2>
		<form method="post" action="<?= base_path('/admin/packages/create') ?>" class="row g-2">
			<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
			<div class="col-md-3">
				<input class="form-control" name="title" placeholder="Título" required>
			</div>
			<div class="col-md-4">
				<input class="form-control" name="description" placeholder="Descrição">
			</div>
			<div class="col-md-2">
				<input class="form-control" name="price" placeholder="Preço" type="number" step="0.01">
			</div>
			<div class="col-md-1">
				<input class="form-control" name="bonus_credits" placeholder="Bônus" type="number">
			</div>
			<div class="col-md-1">
				<input class="form-control" name="subscription_days" placeholder="Dias" type="number">
			</div>
			<div class="col-md-1 d-grid">
				<button class="btn btn-primary" type="submit">Criar</button>
			</div>
		</form>
	</div>
</div>

<div class="table-responsive">
	<table class="table table-sm">
		<thead>
		<tr>
			<th>Título</th>
			<th>Descrição</th>
			<th>Preço</th>
			<th>Bônus</th>
			<th>Dias</th>
			<th class="text-end">Ações</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach (($packages ?? []) as $p): ?>
			<tr>
				<td>
					<input class="form-control form-control-sm" form="pkg-<?= (int)$p['id'] ?>" name="title" value="<?= View::e($p['title']) ?>">
				</td>
				<td>
					<input class="form-control form-control-sm" form="pkg-<?= (int)$p['id'] ?>" name="description" value="<?= View::e((string)$p['description']) ?>">
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
						<button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
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
