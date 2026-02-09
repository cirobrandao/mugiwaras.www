<?php
use App\Core\View;
$metaRobots = 'noindex, nofollow, noarchive, nosnippet';
$hideHeader = true;
ob_start();
?>
<div class="row">
    <div class="col-12">
        <h1 class="h4 mb-3">Entrar</h1>
        <?php if (!empty($_GET['registered'])): ?>
            <div class="alert alert-success">Cadastro realizado com sucesso. Faça login.</div>
        <?php endif; ?>
        <?php if (!empty($_GET['reset'])): ?>
            <div class="alert alert-success">Senha atualizada. Faça login.</div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= View::e($error) ?></div>
        <?php endif; ?>
        <form method="post" action="<?= base_path('/login') ?>">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
            <div class="mb-3">
                <label class="form-label" for="login-username">Usuario ou email</label>
                <input id="login-username" type="text" name="username" class="form-control" required autocapitalize="none" oninput="this.value = this.value.toLowerCase()">
                <div class="form-text">Voce pode entrar com usuario ou email.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="login-password">Senha</label>
                <input id="login-password" type="password" name="password" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Lembrar de mim</label>
                </div>
                <a class="small" href="<?= base_path('/support') ?>">Esqueceu a senha?</a>
            </div>
            <button class="btn btn-primary w-100" type="submit">Entrar</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
