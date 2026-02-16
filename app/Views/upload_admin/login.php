<?php
ob_start();
?>
<div class="upload-admin-shell" style="max-width: 520px; margin: 4rem auto; padding: 2rem;">
    <div class="text-center mb-4">
        <h1 class="h3 mb-2">🚀 Upload Admin</h1>
        <div class="text-muted">Acesse o painel de upload bypass</div>
    </div>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= \App\Core\View::e((string)$error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="alert alert-info border-0" style="background: rgba(59, 130, 246, 0.08); color: #1e40af;">
        <i class="bi bi-info-circle me-2"></i>
        <small>Use as mesmas credenciais da plataforma principal. Para login dedicado, configure <code>UPLOAD_ADMIN_USER</code> e <code>UPLOAD_ADMIN_PASS</code>.</small>
    </div>
    
    <form method="post" action="<?= base_path('/login') ?>" class="section-card p-4">
        <input type="hidden" name="_csrf" value="<?= \App\Core\View::e($csrf ?? '') ?>">
        
        <div class="mb-3">
            <label class="form-label fw-semibold" for="upload-admin-username">
                <i class="bi bi-person me-1"></i>
                Usuário ou e-mail
            </label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input 
                    type="text" 
                    name="username" 
                    id="upload-admin-username"
                    class="form-control" 
                    placeholder="Digite seu usuário ou e-mail"
                    autocomplete="username" 
                    autocapitalize="none"
                    oninput="this.value = this.value.toLowerCase()"
                    required
                >
            </div>
        </div>
        
        <div class="mb-4">
            <label class="form-label fw-semibold" for="upload-admin-password">
                <i class="bi bi-lock me-1"></i>
                Senha
            </label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input 
                    type="password" 
                    name="password" 
                    id="upload-admin-password"
                    class="form-control" 
                    placeholder="Digite sua senha"
                    autocomplete="current-password" 
                    required
                >
                <button 
                    type="button" 
                    class="btn btn-outline-secondary" 
                    id="toggleUploadAdminPassword"
                    title="Mostrar senha"
                >
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Entrar no painel
        </button>
    </form>
    
    <div class="text-center mt-4">
        <a href="<?= base_path('/') ?>" class="text-muted text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>
            Voltar para o site principal
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const pwd = document.getElementById('upload-admin-password');
    const btn = document.getElementById('toggleUploadAdminPassword');
    if (!pwd || !btn) return;
    
    btn.addEventListener('click', function(){
        const icon = btn.querySelector('i');
        if (pwd.type === 'password'){
            pwd.type = 'text';
            icon.className = 'bi bi-eye-slash';
            btn.title = 'Ocultar senha';
        } else {
            pwd.type = 'password';
            icon.className = 'bi bi-eye';
            btn.title = 'Mostrar senha';
        }
    });
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
