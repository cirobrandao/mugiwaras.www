<?php
use App\Core\View;

$displayName = (string)($currentUser['username'] ?? 'Usuário');
$initial = $displayName !== '' ? mb_strtoupper(mb_substr($displayName, 0, 1)) : 'U';
$userAvatar = (string)($currentUser['avatar_path'] ?? '');
?>
<header class="app-topbar">
    <div class="topbar-left">
        <?php if ($isLoggedIn): ?>
            <form class="topbar-search" method="get" action="<?= base_path('/lib/search') ?>" role="search" aria-label="Buscar nas bibliotecas">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="text" class="form-control form-control-sm" name="q" placeholder="Buscar nas bibliotecas" autocomplete="off" aria-label="Campo de busca">
            </form>
        <?php endif; ?>
    </div>
    <div class="topbar-right">
        <?php if ($isLoggedIn): ?>
            <button class="btn btn-icon theme-toggle-btn" type="button" data-theme-toggle aria-label="Alternar tema">
                <i class="fa-solid fa-moon"></i>
            </button>
            <div class="dropdown">
                <button class="btn btn-ghost dropdown-toggle d-inline-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <?php if ($userAvatar !== ''): ?>
                        <img src="<?= base_path('/' . ltrim($userAvatar, '/')) ?>" alt="Avatar" class="topbar-avatar">
                    <?php else: ?>
                        <div class="topbar-avatar-placeholder"><?= View::e($initial) ?></div>
                    <?php endif; ?>
                    <span class="d-none d-md-inline"><?= View::e($displayName) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= base_path('/perfil') ?>">Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= base_path('/logout') ?>">Sair</a></li>
                </ul>
            </div>
        <?php else: ?>
            <button class="btn btn-icon theme-toggle-btn" type="button" data-theme-toggle aria-label="Alternar tema">
                <i class="fa-solid fa-moon"></i>
            </button>
            <a class="btn btn-ghost" href="<?= base_path('/') ?>">Login</a>
            <a class="btn btn-dark" href="<?= base_path('/register') ?>">Registrar</a>
        <?php endif; ?>
    </div>
</header>
