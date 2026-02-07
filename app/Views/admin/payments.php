<?php
use App\Core\View;
ob_start();

$statusMap = [
	'pending' => ['label' => 'Pendente', 'class' => 'bg-warning text-dark'],
	'approved' => ['label' => 'Aprovado', 'class' => 'bg-success'],
	'rejected' => ['label' => 'Rejeitado', 'class' => 'bg-danger'],
];
?>
<h1 class="h4 mb-3">Pagamentos</h1>

<div class="table-responsive">
	<table class="table table-hover align-middle">
		<thead class="table-light">
		<tr>
			<th scope="col">Usuário</th>
			<th scope="col">Pacote</th>
			<th scope="col" style="width: 90px;">Meses</th>
			<th scope="col" style="width: 140px;">Status</th>
			<th scope="col" style="width: 140px;">Comprovante</th>
			<th scope="col" style="width: 170px;">Criado</th>
			<th scope="col" class="text-end" style="width: 180px;">Ações</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach (($payments ?? []) as $p): ?>
			<?php
			$st = (string)($p['status'] ?? '');
			$stMeta = $statusMap[$st] ?? ['label' => $st !== '' ? $st : '-', 'class' => 'bg-secondary'];
			?>
			<tr>
				<td><?= View::e((string)($p['user_name'] ?? ('#' . (int)$p['user_id']))) ?></td>
				<td><?= View::e((string)($p['package_name'] ?? ('#' . (int)$p['package_id']))) ?></td>
				<td><?= (int)($p['months'] ?? 1) ?></td>
				<td>
					<span class="badge <?= View::e($stMeta['class']) ?>">
						<?= View::e($stMeta['label']) ?>
					</span>
				</td>
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
