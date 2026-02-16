<?php
use App\Core\View;
use App\Core\SimpleCache;
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    // Cache system settings (1 hour TTL)
    $systemName = SimpleCache::remember('system_name', 3600, function() {
        return \App\Models\Setting::get('system_name', 'Mugiwaras');
    });
    $systemLogo = SimpleCache::remember('system_logo', 3600, function() {
        return \App\Models\Setting::get('system_logo', '');
    });
    $systemFavicon = SimpleCache::remember('system_favicon', 3600, function() {
        return \App\Models\Setting::get('system_favicon', '');
    });
    ?>
    <title><?= View::e($title ?? $systemName) ?></title>
    <?php if (!empty($metaRobots)): ?>
        <meta name="robots" content="<?= View::e((string)$metaRobots) ?>">
    <?php endif; ?>
    <?php if (!empty($systemFavicon)): ?>
        <link rel="icon" href="<?= base_path('/' . ltrim((string)$systemFavicon, '/')) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= asset('/assets/bootstrap.min.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('/assets/css/theme.css') ?>">
    <?php if (!empty($loadDashboardSidebarCss)): ?>
        <link rel="stylesheet" href="<?= asset('/assets/css/sidebar.css') ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= asset('/assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= asset('/assets/category-tags.css') ?>">
</head>
<body class="app-body">
<?php
$currentUser = \App\Core\Auth::user();

$displaySystemName = trim(preg_replace('/\s*\[DEV\]\s*/i', '', (string)$systemName));
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $currentUser ? \App\Core\Auth::isAdmin($currentUser) : false;
$isModerator = $currentUser ? \App\Core\Auth::isModerator($currentUser) : false;
$isUploader = $currentUser ? \App\Core\Auth::isUploader($currentUser) : false;
$isSupportStaff = $currentUser ? \App\Core\Auth::isSupportStaff($currentUser) : false;
$isEquipe = $currentUser ? \App\Core\Auth::isEquipe($currentUser) : false;
$isRestricted = $currentUser && ($currentUser['access_tier'] ?? '') === 'restrito';
$canUseSideMenu = $isAdmin || $isModerator || $isUploader || $isSupportStaff;

$supportUrl = base_path('/support');
$supportBadge = 0;
$pendingPayments = 0;
$pendingSupport = 0;
$pendingUploads = 0;
$recentUsers = [];

if ($isLoggedIn && !empty($currentUser)) {
    if (!$isSupportStaff) {
        $supportBadge = \App\Models\SupportReply::countPendingForUser((int)$currentUser['id']);
    }
    if ($isAdmin) {
        $recentUsers = \App\Models\User::recentLogins(10);
        $pendingPayments = \App\Models\Payment::countPending();
    }
    if ($isSupportStaff) {
        $pendingSupport = \App\Models\SupportMessage::countOpenForStaff();
    }
    if ($isAdmin || $isUploader || $isModerator) {
        $pendingUploads = \App\Models\Upload::countPending();
    }
}

$displayName = (string)($currentUser['username'] ?? 'Usuário');
$initial = $displayName !== '' ? mb_strtoupper(mb_substr($displayName, 0, 1)) : 'U';
$userAvatar = (string)($currentUser['avatar_path'] ?? '');
$activePage = $activePage ?? '';
$uploadEntryUrl = upload_url('/upload');

$loadLabel = 'indisponivel';
$loadPercent = null;
if (function_exists('sys_getloadavg')) {
    $loads = sys_getloadavg();
    $load1 = is_array($loads) ? (float)($loads[0] ?? 0) : 0.0;
    $cpuCores = (int)(getenv('NUMBER_OF_PROCESSORS') ?: 0);
    if ($cpuCores <= 0 && is_readable('/proc/cpuinfo')) {
        $cpuInfo = file_get_contents('/proc/cpuinfo') ?: '';
        $cpuCores = max(0, substr_count($cpuInfo, 'processor'));
    }
    if ($cpuCores > 0) {
        $loadPercent = (int)round(min(100, ($load1 / $cpuCores) * 100));
        if ($loadPercent < 50) {
            $loadLabel = 'baixa';
        } elseif ($loadPercent < 75) {
            $loadLabel = 'media';
        } else {
            $loadLabel = 'alta';
        }
    }
}
?>

<?php if (!empty($hideHeader)): ?>
    <div class="auth-shell">
        <div class="auth-hero">
            <div>
                <?php if (!empty($systemLogo)): ?>
                    <img src="<?= base_path('/' . ltrim((string)$systemLogo, '/')) ?>" alt="Logo" class="auth-logo">
                <?php endif; ?>
                <h1><?= View::e($authHeroTitle ?? 'Bem-vindo de volta') ?></h1>
                <p><?= View::e($authHeroText ?? 'Acesse sua conta para continuar sua leitura e gerenciar seus conteúdos.') ?></p>
                <?php if (!empty($authHeroFeatures) && is_array($authHeroFeatures)): ?>
                    <div class="auth-hero-features">
                        <?php foreach ($authHeroFeatures as $feature): ?>
                            <div class="auth-hero-feature">
                                <i class="<?= View::e($feature['icon'] ?? 'bi bi-check-circle') ?>"></i>
                                <div>
                                    <?php if (!empty($feature['title'])): ?>
                                        <div class="fw-semibold"><?= View::e($feature['title']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($feature['text'])): ?>
                                        <div><?= View::e($feature['text']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="auth-hero-footer">
                © <?= date('Y') ?> <?= View::e($displaySystemName !== '' ? $displaySystemName : $systemName) ?>
            </div>
        </div>
        <div class="auth-panel">
            <div class="auth-panel-inner">
                <?= $content ?? '' ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="app-shell">
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg fixed-top app-navbar" role="banner">
            <div class="container-fluid navbar-container">
                <div class="navbar-brand-wrapper">
                    <a class="navbar-brand" href="<?= base_path('/home') ?>" aria-label="Página inicial">
                        <?php if (!empty($systemLogo)): ?>
                            <img src="<?= base_path('/' . ltrim((string)$systemLogo, '/')) ?>" alt="<?= View::e($systemName) ?>" class="navbar-logo">
                        <?php else: ?>
                            <span class="navbar-brand-text"><?= View::e($systemName) ?></span>
                        <?php endif; ?>
                    </a>
                    <?php if ($isLoggedIn): ?>
                        <div class="collapse navbar-collapse" id="navbarPortalMenuMain">
                            <div class="navbar-nav navbar-nav-portal">
                                <?php if (!$isRestricted): ?>
                                    <a class="nav-link <?= $activePage === 'libraries' ? 'active' : '' ?>" href="<?= base_path('/lib') ?>">
                                        <i class="bi bi-collection"></i>
                                        <span>Biblioteca</span>
                                    </a>
                                    <a class="nav-link <?= $activePage === 'loja' ? 'active' : '' ?>" href="<?= base_path('/loja') ?>">
                                        <i class="bi bi-bag"></i>
                                        <span>Loja</span>
                                    </a>
                                    <a class="nav-link <?= $activePage === 'support' ? 'active' : '' ?>" href="<?= base_path('/support') ?>">
                                        <i class="bi bi-headset"></i>
                                        <span>Suporte</span>
                                    </a>
                                <?php endif; ?>
                                <div class="navbar-mobile-user d-lg-none">
                                    <hr class="navbar-mobile-divider">
                                    <a class="nav-link" href="<?= base_path('/perfil') ?>"><span>Editar perfil</span></a>
                                    <a class="nav-link" href="<?= base_path('/perfil/senha') ?>"><span>Mudar senha</span></a>
                                    <?php if ($isAdmin): ?>
                                        <hr class="navbar-mobile-divider">
                                        <a class="nav-link" href="<?= base_path('/admin') ?>"><span>Painel Administrativo</span></a>
                                        <hr class="navbar-mobile-divider">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($isLoggedIn && !$isRestricted): ?>
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
                        <?php if (!$isRestricted): ?>
                            <button class="btn btn-icon navbar-search-btn d-lg-none" type="button" data-mobile-search-toggle aria-label="Abrir busca">
                                <i class="bi bi-search"></i>
                            </button>
                        <?php endif; ?>
                        <button class="btn btn-icon theme-toggle-btn" type="button" data-theme-toggle aria-label="Alternar tema claro/escuro">
                            <i class="fa-solid fa-moon" aria-hidden="true"></i>
                        </button>
                        <div class="dropdown d-none d-lg-block">
                            <button class="btn btn-ghost dropdown-toggle d-inline-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu do usuário">
                                <?php if ($userAvatar !== ''): ?>
                                    <img src="<?= base_path('/' . ltrim($userAvatar, '/')) ?>" alt="Avatar de <?= View::e($displayName) ?>" class="navbar-avatar">
                                <?php else: ?>
                                    <span class="navbar-avatar-placeholder" aria-hidden="true"><?= View::e($initial) ?></span>
                                <?php endif; ?>
                                <span class="d-none d-md-inline"><?= View::e($displayName) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (!$isAdmin && !$isEquipe && !$isSupportStaff && !$isUploader && !$isModerator): ?>
                                    <li><a class="dropdown-item d-flex align-items-center justify-content-between" href="<?= $supportUrl ?>"><span><i class="bi bi-life-preserver me-2"></i>Suporte</span><?php if (!empty($supportBadge)): ?><span class="badge bg-danger"><?= (int)$supportBadge ?></span><?php endif; ?></a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <?php if ($canUseSideMenu): ?>
                                    <?php if ($isAdmin): ?>
                                        <li><a class="dropdown-item" href="<?= base_path('/admin') ?>"><i class="bi bi-grid me-2"></i>Painel admin</a></li>
                                    <?php endif; ?>
                                    <?php if ($isSupportStaff): ?>
                                        <li><a class="dropdown-item d-flex align-items-center justify-content-between" href="<?= base_path('/admin/support') ?>"><span><i class="bi bi-headset me-2"></i>Suporte staff</span><?php if (!empty($pendingSupport)): ?><span class="badge bg-danger"><?= (int)$pendingSupport ?></span><?php endif; ?></a></li>
                                    <?php endif; ?>
                                    <?php if ($isAdmin): ?>
                                        <li><a class="dropdown-item d-flex align-items-center justify-content-between" href="<?= base_path('/admin/payments') ?>"><span><i class="bi bi-cash-coin me-2"></i>Pagamentos</span><?php if (!empty($pendingPayments)): ?><span class="badge bg-danger"><?= (int)$pendingPayments ?></span><?php endif; ?></a></li>
                                    <?php endif; ?>
                                    <?php if ($isAdmin || $isUploader || $isModerator): ?>
                                        <li><a class="dropdown-item d-flex align-items-center justify-content-between" href="<?= base_path('/admin/uploads') ?>"><span><i class="bi bi-upload me-2"></i>Upload manager</span><?php if (!empty($pendingUploads)): ?><span class="badge bg-danger"><?= (int)$pendingUploads ?></span><?php endif; ?></a></li>
                                        <li><a class="dropdown-item" href="<?= $uploadEntryUrl ?>"><i class="bi bi-cloud-arrow-up me-2"></i>Enviar arquivo</a></li>
                                    <?php endif; ?>
                                    <?php if ($isAdmin): ?>
                                        <li><a class="dropdown-item" href="<?= base_path('/admin/news') ?>"><i class="bi bi-megaphone me-2"></i>Notícias</a></li>
                                        <li><a class="dropdown-item" href="<?= base_path('/admin/notifications') ?>"><i class="bi bi-bell me-2"></i>Notificações</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?= base_path('/perfil') ?>"><i class="bi bi-person me-2"></i>Meu perfil</a></li>
                                <li><a class="dropdown-item" href="<?= base_path('/perfil/senha') ?>"><i class="bi bi-key me-2"></i>Mudar senha</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_path('/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                            </ul>
                        </div>
                        <?php if (!$isRestricted): ?>
                            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPortalMenuMain" aria-controls="navbarPortalMenuMain" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-bars" aria-hidden="true">☰</span>
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="btn btn-icon theme-toggle-btn" type="button" data-theme-toggle aria-label="Alternar tema claro/escuro">
                            <i class="fa-solid fa-moon" aria-hidden="true"></i>
                        </button>
                        <a class="btn btn-ghost" href="<?= base_path('/') ?>">Login</a>
                        <a class="btn btn-primary" href="<?= base_path('/register') ?>">Registrar</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="app-main">
            <main role="main">
                <?= $content ?? '' ?>
            </main>
            <footer class="app-footer" role="contentinfo">
                <div class="footer-content">
                    <div class="footer-copyright">
                        <i class="bi bi-c-circle me-1" aria-hidden="true"></i>
                        <?= date('Y') ?> <?= View::e($systemName) ?>
                    </div>
                    <div class="footer-info">
                        <span class="server-status" aria-label="Carga do servidor">
                            <i class="bi bi-hdd" aria-hidden="true"></i>
                            <span class="d-none d-md-inline">Servidor:</span> 
                            <span class="status-badge status-<?= View::e($loadLabel) ?>"><?= View::e($loadLabel) ?></span>
                            <?php if ($loadPercent !== null): ?>
                                <span class="fw-semibold"><?= (int)$loadPercent ?>%</span>
                            <?php endif; ?>
                        </span>
                        <span class="separator" aria-hidden="true">·</span>
                        <span class="last-update">
                            <i class="bi bi-arrow-clockwise me-1" aria-hidden="true"></i>
                            <span data-last-sync>agora</span>
                        </span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
<?php endif; ?>

<script src="<?= base_path('/assets/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_path('/assets/js/phone-mask.js') ?>"></script>
<script src="<?= base_path('/assets/js/app.js') ?>"></script>
<script src="<?= base_path('/assets/js/theme.js') ?>"></script>
</body>
</html>
