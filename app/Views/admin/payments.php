<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Pagamentos</h1>

<div class="table-responsive">
	<table class="table table-sm">
		<thead>
		<tr>
			<th>Usuário</th>
			<th>Pacote</th>
			<th>Meses</th>
			<th>Status</th>
			<th>Comprovante</th>
			<th>Criado</th>
			<th class="text-end">Ações</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach (($payments ?? []) as $p): ?>
			<tr>
				<td><?= View::e((string)($p['user_name'] ?? ('#' . (int)$p['user_id']))) ?></td>
				<td><?= View::e((string)($p['package_name'] ?? ('#' . (int)$p['package_id']))) ?></td>
				<td><?= (int)($p['months'] ?? 1) ?></td>
				<td><?= View::e($p['status']) ?></td>
				<td>
					<?php if (!empty($p['proof_path'])): ?>
						<a href="<?= base_path('/admin/payments/proof/' . (int)$p['id']) ?>" target="_blank">Ver</a>
					<?php else: ?>
						<span class="text-muted">-</span>
					<?php endif; ?>
				</td>
				<td><?= View::e((string)$p['created_at']) ?></td>
				<td class="text-end">
					<?php if ($p['status'] === 'pending'): ?>
						<form method="post" action="<?= base_path('/admin/payments/approve') ?>" class="d-inline">
							<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
							<input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
							<button class="btn btn-sm btn-success" type="submit">Aprovar</button>
						</form>
						<form method="post" action="<?= base_path('/admin/payments/reject') ?>" class="d-inline">
							<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
							<input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
							<button class="btn btn-sm btn-outline-danger" type="submit">Rejeitar</button>
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
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
