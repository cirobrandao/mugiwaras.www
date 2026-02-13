<?php
use App\Core\View;
use App\Core\Auth;

ob_start();
$userName = (string)($user['username'] ?? 'usuário');
$canUpload = Auth::canUpload($user ?? null);
?>
<div class="dashboard2-layout">
    <aside class="dashboard2-sidebar dashboard2-sidebar-left">
        <div class="dashboard2-sidebar-title">Menu do usuário</div>
        <div class="list-group">
            <a class="list-group-item list-group-item-action" href="<?= base_path('/libraries') ?>">Bibliotecas</a>
            <?php if ($canUpload): ?>
                <a class="list-group-item list-group-item-action" href="<?= upload_url('/upload') ?>">Enviar arquivo</a>
            <?php endif; ?>
            <a class="list-group-item list-group-item-action" href="<?= base_path('/loja') ?>">Loja</a>
            <a class="list-group-item list-group-item-action" href="<?= base_path('/support') ?>">Suporte</a>
            <a class="list-group-item list-group-item-action" href="<?= base_path('/perfil') ?>">Meu perfil</a>
        </div>
    </aside>

    <section class="dashboard2-main">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <h1 class="h4 mb-1">Bem-vindo, <?= View::e($userName) ?></h1>
                <div class="text-muted small">Dashboard 2 (layout experimental)</div>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-primary btn-sm" href="#">Ação teste</a>
                <a class="btn btn-primary btn-sm" href="#">Ação principal</a>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Status</div>
                        <div class="h5 mb-1">Acesso ativo</div>
                        <div class="small text-muted">Exemplo de status</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Favoritos</div>
                        <div class="h5 mb-1">12 séries</div>
                        <div class="small text-muted">Exemplo de métrica</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Leituras</div>
                        <div class="h5 mb-1">48 capítulos</div>
                        <div class="small text-muted">Exemplo de métrica</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="h6 mb-2">Área principal</h2>
                <p class="mb-0 text-muted">Use este espaço para widgets, gráficos e conteúdos do dashboard.</p>
            </div>
        </div>
    </section>

    <aside class="dashboard2-sidebar dashboard2-sidebar-right">
        <div class="dashboard2-sidebar-title">Menu administrador</div>
        <?php if (!empty($isStaff)): ?>
            <div class="list-group">
                <a class="list-group-item list-group-item-action" href="<?= base_path('/admin') ?>">Admin</a>
                <a class="list-group-item list-group-item-action" href="<?= base_path('/admin/users') ?>">Usuários</a>
                <a class="list-group-item list-group-item-action" href="<?= base_path('/admin/payments') ?>">Pagamentos</a>
                <a class="list-group-item list-group-item-action" href="<?= base_path('/admin/support') ?>">Suporte</a>
                <a class="list-group-item list-group-item-action" href="<?= base_path('/admin/uploads') ?>">Uploads</a>
                <a class="list-group-item list-group-item-action" href="<?= base_path('/admin/settings') ?>">Configurações</a>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary mb-0">Sem acesso administrativo.</div>
        <?php endif; ?>
    </aside>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
