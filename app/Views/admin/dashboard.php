<?php
use App\Core\Auth;
ob_start();
$user = Auth::user();
$role = $user['role'] ?? 'user';
$isAdmin = \App\Core\Auth::isAdmin($user);
$isModerator = \App\Core\Auth::isModerator($user);
?>
<h1 class="h4 mb-3">Admin</h1>

<?php if ($isAdmin): ?>
    <div class="mb-4">
        <h2 class="h6 text-uppercase text-muted">Acesso e segurança</h2>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <h3 class="h6">Usuários</h3>
                    <p class="text-muted small">Acesso e tiers.</p>
                    <a class="btn btn-sm btn-primary" href="<?= base_path('/admin/users') ?>">Abrir</a>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <h3 class="h6">Equipe</h3>
                    <p class="text-muted small">Admins, mods, uploaders e suporte.</p>
                    <a class="btn btn-sm btn-primary" href="<?= base_path('/admin/team') ?>">Abrir</a>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <h3 class="h6">Segurança</h3>
                    <p class="text-muted small">Blocklists e políticas.</p>
                    <a class="btn btn-sm btn-primary" href="<?= base_path('/admin/security/email-blocklist') ?>">Abrir</a>
                </div></div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <h2 class="h6 text-uppercase text-muted">Financeiro</h2>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <h3 class="h6">Pacotes</h3>
                    <p class="text-muted small">Planos e créditos.</p>
                    <a class="btn btn-sm btn-primary" href="<?= base_path('/admin/packages') ?>">Abrir</a>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <h3 class="h6">Pagamentos</h3>
                    <p class="text-muted small">Aprovar/rejeitar PIX.</p>
                    <a class="btn btn-sm btn-primary" href="<?= base_path('/admin/payments') ?>">Abrir</a>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <h3 class="h6">Vouchers</h3>
                    <p class="text-muted small">Chaves e dias de acesso.</p>
                    <a class="btn btn-sm btn-primary" href="<?= base_path('/admin/vouchers') ?>">Abrir</a>
                </div></div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <h2 class="h6 text-uppercase text-muted">Conteúdo</h2>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <h3 class="h6">Categorias</h3>
                    <p class="text-muted small">Bibliotecas e banners.</p>
                    <a class="btn btn-sm btn-primary" href="<?= base_path('/admin/categories') ?>">Abrir</a>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <h3 class="h6">Uploads</h3>
                    <p class="text-muted small">Envios e filas.</p>
                    <a class="btn btn-sm btn-primary" href="<?= base_path('/admin/uploads') ?>">Abrir</a>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <h3 class="h6">Notícias</h3>
                    <p class="text-muted small">Publicação e edição.</p>
                    <a class="btn btn-sm btn-primary" href="<?= base_path('/admin/news') ?>">Abrir</a>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <h3 class="h6">Configurações</h3>
                    <p class="text-muted small">Identidade do sistema.</p>
                    <a class="btn btn-sm btn-primary" href="<?= base_path('/admin/settings') ?>">Abrir</a>
                </div></div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <h2 class="h6 text-uppercase text-muted">Suporte</h2>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <h3 class="h6">Suporte</h3>
                    <p class="text-muted small">Mensagens e status.</p>
                    <a class="btn btn-sm btn-primary" href="<?= base_path('/admin/support') ?>">Abrir</a>
                </div></div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($isModerator): ?>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body">
                <h3 class="h6">Uploads</h3>
                <p class="text-muted">Revisar uploads pendentes.</p>
                <a class="btn btn-sm btn-primary" href="<?= base_path('/admin/uploads') ?>">Abrir</a>
            </div></div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
