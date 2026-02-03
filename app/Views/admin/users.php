<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Usuários</h1>

<?php if (!empty($resetToken) && !empty($resetUserId)): ?>
	<div class="alert alert-info">
		Link de reset (usuário #<?= (int)$resetUserId ?>):
		<a href="<?= base_path('/reset?token=' . View::e((string)$resetToken)) ?>" target="_blank"><?= View::e(base_path('/reset?token=' . (string)$resetToken)) ?></a>
	</div>
<?php endif; ?>

<div class="table-responsive">
	<table class="table table-sm">
		<thead>
		<tr>
			<th>Usuário</th>
			<th>Email</th>
			<th>Role</th>
			<th>Tier</th>
			<th>Ações</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach (($users ?? []) as $u): ?>
			<?php
				$isLocked = !empty($u['lock_until']) && strtotime((string)$u['lock_until']) > time();
				$isSuper = $u['role'] === 'superadmin';
				$currentRole = $currentUser['role'] ?? 'none';
				$isSelf = !empty($currentUser) && (int)$currentUser['id'] === (int)$u['id'];
				$canEdit = !$isSuper && ($currentRole === 'superadmin' || !in_array($u['role'], ['admin', 'superadmin'], true));
				$rowClass = $isLocked ? 'text-muted' : '';
			?>
			<tr class="<?= $rowClass ?>" style="<?= $isLocked ? 'text-decoration: line-through;' : '' ?>">
				<td><?= View::e($u['username']) ?></td>
				<td><?= View::e($u['email']) ?></td>
				<td>
					<form method="post" action="<?= base_path('/admin/users/update') ?>" class="d-flex gap-2">
						<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
						<input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
						<?php
							$roles = [
								'none' => 'sem cargo',
								'admin' => 'admin',
								'moderator' => 'moderator',
								'uploader' => 'uploader',
								'superadmin' => 'superadmin',
							];
						?>
						<select name="role" class="form-select form-select-sm" <?= $canEdit ? '' : 'disabled' ?>>
							<?php foreach ($roles as $value => $label): ?>
								<?php
									$disableSuper = ($value === 'superadmin');
								?>
								<option value="<?= $value ?>" <?= $u['role'] === $value ? 'selected' : '' ?> <?= $disableSuper ? 'disabled' : '' ?>><?= View::e($label) ?></option>
							<?php endforeach; ?>
						</select>
				</td>
				<td>
					<?php if ($u['role'] !== 'none'): ?>
						<span class="text-muted">N/A</span>
					<?php else: ?>
						<select name="access_tier" class="form-select form-select-sm" <?= $canEdit ? '' : 'disabled' ?>>
							<?php foreach (['user','trial','assinante','restrito','vitalicio'] as $tier): ?>
								<option value="<?= $tier ?>" <?= $u['access_tier'] === $tier ? 'selected' : '' ?>><?= $tier ?></option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
				</td>
				<td class="d-flex gap-2 justify-content-end">
						<button class="btn btn-sm btn-primary" type="submit" <?= $canEdit ? '' : 'disabled' ?>>Salvar</button>
					</form>

					<?php if ($isLocked): ?>
						<form method="post" action="<?= base_path('/admin/users/unlock') ?>">
							<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
							<input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
							<button class="btn btn-sm btn-secondary" type="submit" <?= $isSuper ? 'disabled' : '' ?>>Desbloquear</button>
						</form>
					<?php else: ?>
						<form method="post" action="<?= base_path('/admin/users/lock') ?>">
							<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
							<input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
							<input type="hidden" name="lock_until" value="2099-12-31 00:00:00">
							<button class="btn btn-sm btn-warning" type="submit" <?= $isSuper ? 'disabled' : '' ?>>Bloquear</button>
						</form>
					<?php endif; ?>

					<form method="post" action="<?= base_path('/admin/users/reset') ?>">
						<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
						<input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
						<button class="btn btn-sm btn-outline-primary" type="submit" <?= $isSuper ? 'disabled' : '' ?>>Reset senha</button>
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
