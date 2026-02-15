<?php
ob_start();
?>
<h1 class="h4 mb-3">Upload Admin · Login</h1>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= \App\Core\View::e((string)$error) ?></div>
<?php endif; ?>
<div class="alert alert-info small">
    Use as mesmas credenciais da plataforma principal. Se quiser um login fixo dedicado, configure <code>UPLOAD_ADMIN_USER</code> e <code>UPLOAD_ADMIN_PASS</code>.
</div>
<form method="post" action="<?= base_path('/login') ?>" class="section-card p-3" style="max-width: 460px;">
    <input type="hidden" name="_csrf" value="<?= \App\Core\View::e($csrf ?? '') ?>">
    <div class="mb-3">
        <label class="form-label">Usuário</label>
        <input type="text" name="username" class="form-control" autocomplete="username" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Senha</label>
        <input type="password" name="password" class="form-control" autocomplete="current-password" required>
    </div>
    <button type="submit" class="btn btn-primary">Entrar</button>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
