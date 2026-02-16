<?php
use App\Core\View;
$metaRobots = 'noindex, nofollow, noarchive, nosnippet';
$hideHeader = true;
$authHeroTitle = 'Bem-vindo de volta!';
$authHeroText = 'Acesse sua conta para continuar lendo seus mangás favoritos e acompanhar seu progresso.';
$authHeroFeatures = [
    [
        'icon' => 'bi bi-book',
        'title' => 'Biblioteca completa',
        'text' => 'Acesso a centenas de títulos'
    ],
    [
        'icon' => 'bi bi-bookmark-check',
        'title' => 'Progresso sincronizado',
        'text' => 'Continue de onde parou'
    ],
    [
        'icon' => 'bi bi-star',
        'title' => 'Seus favoritos',
        'text' => 'Organize e acompanhe suas séries'
    ]
];
ob_start();
?>
<div class="auth-header">
    <h1 class="fw-bold">Entrar</h1>
    <p class="text-muted">Acesse sua conta e continue sua jornada</p>
</div>

<?php if (!empty($_GET['registered'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        Cadastro realizado! Faça login.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($_GET['reset'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        Senha atualizada! Faça login.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <?= View::e($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form method="post" action="<?= base_path('/login') ?>" autocomplete="on">
    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
    
    <div class="mb-3">
        <label class="form-label fw-semibold" for="login-username">
            <i class="bi bi-person me-1"></i>
            Usuário ou e-mail
        </label>
        <div class="input-group input-group-lg">
            <span class="input-group-text">
                <i class="bi bi-person"></i>
            </span>
            <input 
                id="login-username" 
                type="text" 
                name="username" 
                class="form-control" 
                placeholder="Digite seu usuário ou e-mail"
                required 
                autocomplete="username"
                autocapitalize="none" 
                oninput="this.value = this.value.toLowerCase()"
            >
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label fw-semibold d-flex justify-content-between align-items-center" for="login-password">
            <span>
                <i class="bi bi-lock me-1"></i>
                Senha
            </span>
            <a href="<?= base_path('/recover') ?>" class="text-decoration-none small">
                <i class="bi bi-question-circle me-1"></i>
                Esqueceu a senha?
            </a>
        </label>
        <div class="input-group input-group-lg">
            <span class="input-group-text">
                <i class="bi bi-lock"></i>
            </span>
            <input 
                id="login-password" 
                type="password" 
                name="password" 
                class="form-control" 
                placeholder="Digite sua senha"
                required
                autocomplete="current-password"
            >
            <button 
                type="button" 
                class="btn btn-outline-secondary password-toggle" 
                id="togglePassword" 
                aria-label="Mostrar senha"
                title="Mostrar senha"
            >
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>
    
    <div class="mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">
                Lembrar de mim neste dispositivo
            </label>
        </div>
    </div>
    
    <button class="btn btn-primary btn-lg w-100 mb-3" type="submit">
        <i class="bi bi-box-arrow-in-right me-2"></i>
        Entrar
    </button>
</form>

<div class="auth-footer">
    <div class="mb-2">
        <span class="text-muted">Ainda não tem uma conta?</span>
        <a href="<?= base_path('/register') ?>" class="fw-semibold">
            <i class="bi bi-person-plus me-1"></i>
            Cadastre-se gratuitamente
        </a>
    </div>
    <div>
        <a href="<?= base_path('/support') ?>" class="text-muted text-decoration-none">
            <i class="bi bi-headset me-1"></i>
            Precisa de ajuda?
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const pwd = document.getElementById('login-password');
    const btn = document.getElementById('togglePassword');
    if (!pwd || !btn) return;
    
    btn.addEventListener('click', function(){
        const icon = btn.querySelector('i');
        if (pwd.type === 'password'){
            pwd.type = 'text';
            icon.className = 'bi bi-eye-slash';
            btn.setAttribute('aria-label','Ocultar senha');
            btn.title = 'Ocultar senha';
        } else {
            pwd.type = 'password';
            icon.className = 'bi bi-eye';
            btn.setAttribute('aria-label','Mostrar senha');
            btn.title = 'Mostrar senha';
        }
    });
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
