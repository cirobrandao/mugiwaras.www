<?php
use App\Core\View;
$metaRobots = 'noindex, nofollow, noarchive, nosnippet';
$hideHeader = true;
$systemName = \App\Models\Setting::get('system_name', 'Mugiwaras');
$systemLogo = \App\Models\Setting::get('system_logo', '');
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="text-center mb-3">
            <?php if (!empty($systemLogo)): ?>
                <img src="<?= base_path('/' . ltrim((string)$systemLogo, '/')) ?>" alt="Logo" class="login-logo">
            <?php else: ?>
                <div class="h4 mb-0"><?= View::e($systemName) ?></div>
            <?php endif; ?>
        </div>
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
                <label class="form-label">Usuário</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Lembrar de mim</label>
            </div>
            <button class="btn btn-primary w-100" type="submit">Entrar</button>
        </form>
        <div class="mt-3 d-flex justify-content-between">
            <a href="<?= base_path('/register') ?>">Registrar</a>
            <a href="<?= base_path('/support') ?>">Suporte</a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
