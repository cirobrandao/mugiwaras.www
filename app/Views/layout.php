<?php
use App\Core\View;
if (!function_exists('time_ago_compact')) {
    function time_ago_compact(?string $datetime): string
    {
        if (empty($datetime)) {
            return '-';
        }
        try {
            $dt = new DateTimeImmutable($datetime);
        } catch (Exception $e) {
            return '-';
        }
        $now = new DateTimeImmutable('now');
        $diff = $now->getTimestamp() - $dt->getTimestamp();
        if ($diff < 0) {
            $diff = 0;
        }
        $days = (int)floor($diff / 86400);
        $hours = (int)floor(($diff % 86400) / 3600);
        $mins = (int)floor(($diff % 3600) / 60);
        if ($days > 0) {
            return $days . 'd ' . $hours . 'h';
        }
        if ($hours > 0) {
            return $hours . 'h ' . $mins . 'm';
        }
        return $mins . 'm';
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $systemName = \App\Models\Setting::get('system_name', 'Mugiwaras'); ?>
    <?php $systemLogo = \App\Models\Setting::get('system_logo', ''); ?>
    <?php $systemFavicon = \App\Models\Setting::get('system_favicon', ''); ?>
    <title><?= View::e($title ?? $systemName) ?></title>
    <?php if (!empty($metaRobots)): ?>
        <meta name="robots" content="<?= View::e((string)$metaRobots) ?>">
    <?php endif; ?>
    <?php if (!empty($systemFavicon)): ?>
        <link rel="icon" href="<?= base_path('/' . ltrim((string)$systemFavicon, '/')) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= base_path('/assets/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= url('assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= base_path('/assets/category-tags.css') ?>">
    <style>
        html,
        body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        main {
            flex: 1 0 auto;
        }
        footer {
            margin-top: auto;
            flex-shrink: 0;
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            font-weight: 600;
            font-size: 0.85rem;
            background: #ffffff;
            color: #000000;
            border: 2px solid #ffffff;
            border-radius: 999px;
            box-sizing: border-box;
        }
        .user-menu-toggle {
            border: 1px solid rgba(59, 130, 246, 0.85);
            border-radius: 0.5rem;
            padding: 0.25rem 0.6rem;
            background: rgba(59, 130, 246, 0.18);
        }
        .user-menu-toggle:hover,
        .user-menu-toggle:focus {
            border-color: rgba(59, 130, 246, 1);
        }
        #botoes-acao {
            border: none;
            outline: none;
            cursor: pointer;
            transition: background 0.2s, opacity 0.2s;
        }
        #botoes-acao:hover,
        #botoes-acao:focus {
            background: rgba(13, 110, 253, 0.95) !important;
            opacity: 1 !important;
        }
    </style>
</head>
<body>
<?php if (empty($hideHeader)): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <?php $currentUser = \App\Core\Auth::user(); ?>
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $currentUser ? base_path('/dashboard') : base_path('/') ?>">
            <?php if (!empty($systemLogo)): ?>
                <img src="<?= base_path('/' . ltrim((string)$systemLogo, '/')) ?>" alt="Logo" class="navbar-logo">
            <?php else: ?>
                <span><?= View::e($systemName) ?></span>
            <?php endif; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php $supportUrl = base_path('/support'); ?>
                <?php $supportBadge = 0; ?>
                <?php if (!empty($currentUser) && !$isSupportStaff): ?>
                    <?php $supportBadge = \App\Models\SupportReply::countPendingForUser((int)$currentUser['id']); ?>
                <?php endif; ?>
                <?php $displayName = (string)($currentUser['username'] ?? 'Usuário'); ?>
                <?php $initial = mb_strtoupper(mb_substr($displayName, 0, 1)); ?>
                <?php $canUseSideMenu = \App\Core\Auth::isAdmin($currentUser) || \App\Core\Auth::isModerator($currentUser) || \App\Core\Auth::isUploader($currentUser) || \App\Core\Auth::isSupportStaff($currentUser); ?>
                <?php $recentUsers = \App\Core\Auth::isAdmin($currentUser) ? \App\Models\User::recentLogins(10) : []; ?>
                <?php $pendingPayments = \App\Core\Auth::isAdmin($currentUser) ? \App\Models\Payment::countPending() : 0; ?>
                <?php $pendingSupport = \App\Core\Auth::isSupportStaff($currentUser) ? \App\Models\SupportMessage::countOpenForStaff() : 0; ?>
                <?php $pendingUploads = (\App\Core\Auth::isAdmin($currentUser) || \App\Core\Auth::isUploader($currentUser) || \App\Core\Auth::isModerator($currentUser)) ? \App\Models\Upload::countPending() : 0; ?>
            <?php endif; ?>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_path('/libraries') ?>">Bibliotecas</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_path('/loja') ?>">Loja</a></li>
                        <?php if (!empty($currentUser) && !\App\Core\Auth::isAdmin($currentUser) && !\App\Core\Auth::isEquipe($currentUser) && !\App\Core\Auth::isSupportStaff($currentUser) && !\App\Core\Auth::isUploader($currentUser) && !\App\Core\Auth::isModerator($currentUser)): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $supportUrl ?>">
                                    Abrir chamado
                                    <?php if (!empty($supportBadge)): ?>
                                        <span class="badge bg-danger ms-1"><?= (int)$supportBadge ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_path('/') ?>">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_path('/register') ?>">Registrar</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_path('/support') ?>">Abrir chamado</a></li>
                <?php endif; ?>
            </ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 user-menu-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center user-avatar">
                                <?= View::e($initial) ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="<?= base_path('/perfil') ?>">Ver meu perfil</a></li>
                            <li><a class="dropdown-item" href="<?= base_path('/perfil/editar') ?>">Editar meu perfil</a></li>
                            <li><a class="dropdown-item" href="<?= base_path('/perfil/senha') ?>">Editar senha</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_path('/logout') ?>">Sair</a></li>
                        </ul>
                    </li>
                    <?php if (!empty($canUseSideMenu)): ?>
                        <li class="nav-item ms-2">
                            <button class="btn btn-outline-light btn-sm menu-side-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSideMenu" aria-controls="mobileSideMenu" aria-label="Abrir menu lateral">
                                <i class="fa-solid fa-bars"></i>
                            </button>
                        </li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php if (!empty($canUseSideMenu)): ?>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileSideMenu" aria-labelledby="mobileSideMenuLabel">
        <div class="offcanvas-header">
            <span class="offcanvas-title" id="mobileSideMenuLabel"></span>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body">
            <?php if (\App\Core\Auth::isAdmin($currentUser)): ?>
                <div class="mb-3">
                    <div class="text-muted small mb-2">Administrador</div>
                    <div class="list-group">
                        <a class="list-group-item list-group-item-action" href="<?= base_path('/admin') ?>">Dashboard</a>
                        <a class="list-group-item list-group-item-action" href="<?= base_path('/admin/users') ?>">Usuários</a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="<?= base_path('/admin/uploads') ?>">
                            <span>Gerenciador de Arquivos</span>
                            <?php if (!empty($pendingUploads)): ?>
                                <span class="badge bg-danger ms-auto"><?= (int)$pendingUploads ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (\App\Core\Auth::isAdmin($currentUser)): ?>
                <div class="mb-3">
                    <div class="text-muted small mb-2">Financeiro</div>
                    <div class="list-group">
                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="<?= base_path('/admin/payments') ?>">
                            <span>Pagamentos</span>
                            <?php if (!empty($pendingPayments)): ?>
                                <span class="badge bg-danger ms-auto"><?= (int)$pendingPayments ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (\App\Core\Auth::isModerator($currentUser)): ?>
                <div class="mb-3">
                    <div class="text-muted small mb-2">Moderador</div>
                    <div class="list-group">
                        <a class="list-group-item list-group-item-action" href="<?= base_path('/admin/uploads') ?>">Uploads</a>
                        <a class="list-group-item list-group-item-action" href="<?= base_path('/admin/categories') ?>">Categorias</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (\App\Core\Auth::isUploader($currentUser) || \App\Core\Auth::isAdmin($currentUser)): ?>
                <div class="mb-3">
                    <div class="text-muted small mb-2">Uploader</div>
                    <div class="list-group">
                        <a class="list-group-item list-group-item-action" href="<?= base_path('/upload') ?>">Enviar arquivo</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (\App\Core\Auth::isSupportStaff($currentUser)): ?>
                <div class="mb-3">
                    <div class="text-muted small mb-2">Suporte</div>
                    <div class="list-group">
                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="<?= base_path('/admin/support') ?>">
                            <span>Chamados</span>
                            <?php if (!empty($pendingSupport)): ?>
                                <span class="badge bg-danger ms-auto"><?= (int)$pendingSupport ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($recentUsers)): ?>
                <div class="mt-4">
                    <div class="text-muted small mb-2">Últimos usuários conectados</div>
                    <div class="list-group list-group-flush small">
                        <?php foreach ($recentUsers as $ru): ?>
                            <div class="list-group-item d-flex align-items-center justify-content-between py-1">
                                <span><?= View::e((string)($ru['username'] ?? '')) ?></span>
                                <?php $lastLogin = $ru['data_ultimo_login'] ?? $ru['data_registro'] ?? null; ?>
                                <span class="small text-muted"><?= View::e(time_ago_compact(is_string($lastLogin) ? $lastLogin : null)) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php endif; ?>
<main class="container py-4">
    <?= $content ?? '' ?>
</main>
<?php if (empty($hideHeader)): ?>
<footer class="bg-dark text-white-50 text-center py-3">
    <div class="container">
        <small>© <?= date('Y') ?> <?= View::e($systemName) ?>. Todos os direitos reservados.</small>
    </div>
</footer>
<?php endif; ?>
<script src="<?= base_path('/assets/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= url('assets/js/phone-mask.js') ?>"></script>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
