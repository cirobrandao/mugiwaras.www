<?php
use App\Core\View;

$displayName = (string)($currentUser['username'] ?? 'Usuário');
$initial = $displayName !== '' ? mb_strtoupper(mb_substr($displayName, 0, 1)) : 'U';
$userAvatar = (string)($currentUser['avatar_path'] ?? '');
$isAdminOrSuper = in_array((string)($currentUser['role'] ?? ''), ['admin', 'superadmin'], true);
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$basePath = base_path('');
$isLibraries = str_contains($currentPath, '/lib');
$isLoja = str_contains($currentPath, '/loja');
$isSuporte = str_contains($currentPath, '/support');
?>
<nav class="navbar navbar-expand-lg fixed-top app-navbar" role="banner">
    <div class="container-fluid navbar-container">
        <div class="navbar-brand-wrapper">
            <?php if ($isLoggedIn): ?>
                <div class="collapse navbar-collapse" id="navbarPortalMenu">
                    <div class="navbar-nav navbar-nav-portal">
                        <a class="nav-link <?= $isLibraries ? 'active' : '' ?>" href="<?= base_path('/lib') ?>">
                            <i class="bi bi-collection"></i>
                            <span>Bibliotecas</span>
                        </a>
                        <a class="nav-link <?= $isLoja ? 'active' : '' ?>" href="<?= base_path('/loja') ?>">
                            <i class="bi bi-bag"></i>
                            <span>Loja</span>
                        </a>
                        <a class="nav-link <?= $isSuporte ? 'active' : '' ?>" href="<?= base_path('/support') ?>">
                            <i class="bi bi-headset"></i>
                            <span>Suporte</span>
                        </a>
                        <div class="navbar-mobile-user d-lg-none">
                            <hr class="navbar-mobile-divider">
                            <a class="nav-link" href="<?= base_path('/perfil') ?>"><span>Editar perfil</span></a>
                            <?php if ($isAdminOrSuper): ?>
                                <hr class="navbar-mobile-divider">
                                <a class="nav-link" href="<?= base_path('/admin') ?>"><span>Painel Administrativo</span></a>
                                <hr class="navbar-mobile-divider">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <form class="navbar-search" method="get" action="<?= base_path('/search') ?>" role="search" aria-label="Buscar nas bibliotecas">
                    <button type="button" class="navbar-search-close" data-mobile-search-close aria-label="Fechar busca">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <i class="bi bi-search navbar-search-icon" aria-hidden="true"></i>
                    <input type="text" class="form-control form-control-sm" name="q" placeholder="Buscar" autocomplete="off" aria-label="Campo de busca">
                </form>
            <?php endif; ?>
        </div>
        <div class="navbar-actions" role="toolbar" aria-label="Ações do usuário">
        <?php if ($isLoggedIn): ?>
            <button class="btn btn-icon navbar-search-btn d-lg-none" type="button" data-mobile-search-toggle aria-label="Abrir busca">
                <i class="bi bi-search"></i>
            </button>
            <button class="btn btn-icon theme-toggle-btn" type="button" data-theme-toggle aria-label="Alternar tema">
                <i class="fa-solid fa-moon"></i>
            </button>
            <div class="dropdown d-none d-lg-block">
                <button class="btn btn-ghost dropdown-toggle d-inline-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php if ($userAvatar !== ''): ?>
                        <img src="<?= base_path('/' . ltrim($userAvatar, '/')) ?>" alt="Avatar" class="navbar-avatar">
                    <?php else: ?>
                        <div class="navbar-avatar-placeholder"><?= View::e($initial) ?></div>
                    <?php endif; ?>
                    <span class="d-none d-md-inline"><?= View::e($displayName) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= base_path('/perfil') ?>">Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= base_path('/logout') ?>">Sair</a></li>
                </ul>
            </div>
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPortalMenu" aria-controls="navbarPortalMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-bars" aria-hidden="true">☰</span>
            </button>
        <?php else: ?>
            <button class="btn btn-icon theme-toggle-btn" type="button" data-theme-toggle aria-label="Alternar tema">
                <i class="fa-solid fa-moon"></i>
            </button>
            <a class="btn btn-ghost" href="<?= base_path('/') ?>">Login</a>
            <a class="btn btn-dark" href="<?= base_path('/register') ?>">Registrar</a>
        <?php endif; ?>
        </div>
    </div>
</nav>
