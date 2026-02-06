<?php
use App\Core\View;
$hideHeader = true;
ob_start();
?>
<h1 class="h4 mb-3">Redefinir senha</h1>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= View::e($error) ?></div>
<?php endif; ?>
<form method="post" action="<?= base_path('/reset') ?>">
    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
    <input type="hidden" name="token" value="<?= View::e($token ?? '') ?>">
    <div class="mb-3">
        <label class="form-label">Nova senha</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Confirmar senha</label>
        <input type="password" name="password_confirm" class="form-control" required>
    </div>
    <button class="btn btn-primary" type="submit">Atualizar</button>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
