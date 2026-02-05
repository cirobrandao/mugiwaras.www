<?php
use App\Core\View;
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
    <link rel="stylesheet" href="https://cdn.zone.net.br/css/bootstrap.min.css">
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
            background: #8a95a3;
        }
        .user-menu-toggle {
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 0.5rem;
            padding: 0.25rem 0.6rem;
        }
        .user-menu-toggle:hover,
        .user-menu-toggle:focus {
            border-color: rgba(255, 255, 255, 0.6);
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
        @media (min-width: 992px) {
            .navbar-container {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .navbar-left,
            .navbar-center,
            .navbar-right {
                display: flex;
                align-items: center;
            }
            .navbar-center {
                flex: 1 1 auto;
                justify-content: center;
            }
            .navbar-right {
                flex: 0 0 auto;
            }
        }
    </style>
</head>
<body>
<?php if (empty($hideHeader)): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container navbar-container">
        <?php $currentUser = \App\Core\Auth::user(); ?>
        <div class="navbar-left">
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
        </div>
        <div class="navbar-center">
            <div class="collapse navbar-collapse" id="mainNav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php $supportUrl = base_path('/support'); ?>
                <?php $supportBadge = 0; ?>
                <?php if (!empty($currentUser) && !$isSupportStaff): ?>
                    <?php $supportBadge = \App\Models\SupportReply::countPendingForUser((int)$currentUser['id']); ?>
                <?php endif; ?>
                <?php $displayName = (string)($currentUser['username'] ?? 'Usuário'); ?>
                <?php $initial = mb_strtoupper(mb_substr($displayName, 0, 1)); ?>
            <?php endif; ?>
                <ul class="navbar-nav mx-lg-auto text-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_path('/libraries') ?>">Bibliotecas</a></li>
                        <?php if (!empty($currentUser) && \App\Core\Auth::canUpload($currentUser)): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= base_path('/upload') ?>">Enviar arquivo</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_path('/loja') ?>">Loja</a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $supportUrl ?>">
                                Suporte
                                <?php if (!empty($supportBadge)): ?>
                                    <span class="badge bg-danger ms-1"><?= (int)$supportBadge ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php if (!empty($currentUser) && (\App\Core\Auth::isAdmin($currentUser) || \App\Core\Auth::isModerator($currentUser))): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= base_path('/admin') ?>">Admin</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_path('/') ?>">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_path('/register') ?>">Registrar</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_path('/support') ?>">Suporte</a></li>
                    <?php endif; ?>
                </ul>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <ul class="navbar-nav ms-lg-3">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 user-menu-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center user-avatar">
                                    <?= View::e($initial) ?>
                                </span>
                                <span><?= View::e($displayName) ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                <li><a class="dropdown-item" href="<?= base_path('/perfil') ?>">Ver meu perfil</a></li>
                                <li><a class="dropdown-item" href="<?= base_path('/perfil/editar') ?>">Editar meu perfil</a></li>
                                <li><a class="dropdown-item" href="<?= base_path('/perfil/senha') ?>">Editar senha</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_path('/logout') ?>">Sair</a></li>
                            </ul>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
 </nav>
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
<script src="https://cdn.zone.net.br/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('assets/js/phone-mask.js') ?>"></script>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
