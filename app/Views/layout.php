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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= url('assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/z1hd.css') ?>">
    <link rel="stylesheet" href="<?= base_path('/assets/category-tags.css') ?>">
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
$activePage = $activePage ?? '';
?>

<?php if (!empty($hideHeader)): ?>
    <div class="auth-shell">
        <div class="auth-hero">
            <div>
                <?php if (!empty($systemLogo)): ?>
                    <img src="<?= base_path('/' . ltrim((string)$systemLogo, '/')) ?>" alt="Logo" class="auth-logo">
                <?php endif; ?>
                <h1 class="mt-4">Bem-vindo de volta</h1>
                <p class="text-muted">Acesse sua conta para continuar sua leitura e gerenciar seus conteúdos.</p>
                <div class="mt-4">
                    <div class="fw-semibold">Precisa de acesso?</div>
                    <a class="text-decoration-none" href="<?= base_path('/register') ?>">Cadastre-se agora.</a>
                </div>
            </div>
            <div class="text-muted small">© <?= date('Y') ?> <?= View::e($displaySystemName !== '' ? $displaySystemName : $systemName) ?></div>
        </div>
        <div class="auth-panel">
            <?= $content ?? '' ?>
        </div>
    </div>
<?php else: ?>
    <div class="app-shell">
        <aside class="app-sidebar">
            <div class="sidebar-brand">
                <div class="brand-mark">
                    <?php if (!empty($systemFavicon)): ?>
                        <img src="<?= base_path('/' . ltrim((string)$systemFavicon, '/')) ?>" alt="Favicon" class="brand-icon">
                    <?php else: ?>
                        <?= View::e(mb_strtoupper(mb_substr($systemName, 0, 1))) ?>
                    <?php endif; ?>
                </div>
                <div class="brand-text">
                    <div class="brand-title"><?= View::e($systemName) ?></div>
                    <div class="brand-sub">Biblioteca digital</div>
                </div>
            </div>
            <button class="btn btn-sm btn-ghost w-100 d-lg-none" data-sidebar-toggle>
                <i class="bi bi-layout-text-sidebar-reverse"></i>
                Menu
            </button>
            <nav class="sidebar-nav">
                <?php if ($isLoggedIn): ?>
                    <a class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>" href="<?= base_path('/dashboard') ?>">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                    <a class="nav-link <?= $activePage === 'libraries' ? 'active' : '' ?>" href="<?= base_path('/libraries') ?>">
                        <i class="bi bi-collection"></i>
                        <span>Bibliotecas</span>
                    </a>
                    <a class="nav-link <?= $activePage === 'loja' ? 'active' : '' ?>" href="<?= base_path('/loja') ?>">
                        <i class="bi bi-bag"></i>
                        <span>Loja</span>
                    </a>
                    <?php if (!$isAdmin && !$isEquipe && !$isSupportStaff && !$isUploader && !$isModerator): ?>
                        <a class="nav-link" href="<?= $supportUrl ?>">
                            <i class="bi bi-life-preserver"></i>
                            <span>Abrir chamado</span>
                            <?php if (!empty($supportBadge)): ?>
                                <span class="badge bg-danger ms-auto"><?= (int)$supportBadge ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a class="nav-link" href="<?= base_path('/') ?>">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Login</span>
                    </a>
                    <a class="nav-link" href="<?= base_path('/register') ?>">
                        <i class="bi bi-person-plus"></i>
                        <span>Registrar</span>
                    </a>
                    <a class="nav-link" href="<?= base_path('/support') ?>">
                        <i class="bi bi-life-preserver"></i>
                        <span>Suporte</span>
                    </a>
                <?php endif; ?>

                <?php if ($canUseSideMenu): ?>
                    <div class="nav-section">Administracao</div>
                    <?php if ($isAdmin): ?>
                        <a class="nav-link" href="<?= base_path('/admin') ?>">
                            <i class="bi bi-grid"></i>
                            <span>Admin</span>
                        </a>
                        <a class="nav-link" href="<?= base_path('/admin/users') ?>">
                            <i class="bi bi-people"></i>
                            <span>Usuarios</span>
                        </a>
                        <a class="nav-link" href="<?= base_path('/admin/payments') ?>">
                            <i class="bi bi-cash-coin"></i>
                            <span>Pagamentos</span>
                            <?php if (!empty($pendingPayments)): ?>
                                <span class="badge bg-danger ms-auto"><?= (int)$pendingPayments ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($isAdmin || $isUploader || $isModerator): ?>
                        <a class="nav-link" href="<?= base_path('/admin/uploads') ?>">
                            <i class="bi bi-upload"></i>
                            <span>Gerenciar upload</span>
                            <?php if (!empty($pendingUploads)): ?>
                                <span class="badge bg-danger ms-auto"><?= (int)$pendingUploads ?></span>
                            <?php endif; ?>
                        </a>
                        <a class="nav-link" href="<?= base_path('/upload') ?>">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <span>Enviar arquivo</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($isSupportStaff): ?>
                        <a class="nav-link" href="<?= base_path('/admin/support') ?>">
                            <i class="bi bi-headset"></i>
                            <span>Suporte</span>
                            <?php if (!empty($pendingSupport)): ?>
                                <span class="badge bg-danger ms-auto"><?= (int)$pendingSupport ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <a class="nav-link" href="<?= base_path('/admin/news') ?>">
                            <i class="bi bi-megaphone"></i>
                            <span>Publicar noticia</span>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($isLoggedIn): ?>
                    <div class="nav-section">Conta</div>
                    <a class="nav-link" href="<?= base_path('/perfil') ?>">
                        <i class="bi bi-person"></i>
                        <span>Perfil</span>
                    </a>
                    <a class="nav-link" href="<?= base_path('/logout') ?>">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Sair</span>
                    </a>
                <?php endif; ?>
            </nav>
        </aside>

        <div class="app-main">
            <header class="app-topbar">
                <div class="topbar-left">
                    <div>
                        <?php if (!empty($systemLogo)): ?>
                            <img src="<?= base_path('/' . ltrim((string)$systemLogo, '/')) ?>" alt="Logo" class="topbar-logo" data-sidebar-toggle>
                        <?php else: ?>
                            <div class="crumb-sub"><?= $isLoggedIn ? 'Leitura, biblioteca e administracao' : 'Acesse sua conta' ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="topbar-right">
                    <?php if ($isLoggedIn): ?>
                        <form class="topbar-search" method="get" action="<?= base_path('/libraries/search') ?>">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control form-control-sm" name="q" placeholder="Buscar nas bibliotecas">
                        </form>
                        <div class="dropdown">
                            <button class="btn btn-ghost dropdown-toggle" data-bs-toggle="dropdown">
                                <span class="d-none d-md-inline"><?= View::e($displayName) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= base_path('/perfil') ?>">Atualizar conta</a></li>
                                <li><a class="dropdown-item" href="<?= base_path('/perfil/senha') ?>">Mudar senha</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_path('/logout') ?>">Sair</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a class="btn btn-ghost" href="<?= base_path('/') ?>">Login</a>
                        <a class="btn btn-dark" href="<?= base_path('/register') ?>">Registrar</a>
                    <?php endif; ?>
                </div>
            </header>

            <main class="app-content">
                <section class="section-card app-page">
                    <?= $content ?? '' ?>
                </section>
            </main>

            <footer class="app-footer">
                <div>© <?= date('Y') ?> <?= View::e($systemName) ?></div>
                <div class="text-muted">Ultima atualizacao <span data-last-sync>agora</span></div>
            </footer>
        </div>
    </div>
<?php endif; ?>

<script src="<?= base_path('/assets/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= url('assets/js/phone-mask.js') ?>"></script>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
