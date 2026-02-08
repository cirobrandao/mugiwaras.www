<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Editar senha</h1>
<?php if (!empty($error)): ?>
	<div class="alert alert-danger"><?= View::e((string)$error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
	<div class="alert alert-success"><?= View::e((string)$success) ?></div>
<?php endif; ?>
<div class="card">
	<div class="card-body">
		<form method="post" action="<?= base_path('/perfil/senha') ?>">
			<input type="hidden" name="_csrf" value="<?= View::e((string)($csrf ?? '')) ?>">
			<div class="mb-3">
				<label class="form-label">Senha atual</label>
				<input class="form-control" type="password" name="current_password" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Nova senha</label>
				<input class="form-control" type="password" name="password" required>
				<div class="form-text">Minimo 8 caracteres com letras e numeros.</div>
			</div>
			<div class="mb-3">
				<label class="form-label">Confirmar nova senha</label>
				<input class="form-control" type="password" name="password_confirm" required>
			</div>
			<button class="btn btn-primary" type="submit">Atualizar senha</button>
		</form>
	</div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
