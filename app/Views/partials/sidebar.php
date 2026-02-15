<?php
use App\Core\View;
use App\Core\Auth;

$currentUser = Auth::user();
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $currentUser ? Auth::isAdmin($currentUser) : false;
$isModerator = $currentUser ? Auth::isModerator($currentUser) : false;
$isUploader = $currentUser ? Auth::isUploader($currentUser) : false;
$isSupportStaff = $currentUser ? Auth::isSupportStaff($currentUser) : false;
$isEquipe = $currentUser ? Auth::isEquipe($currentUser) : false;
$isRestricted = $currentUser && ($currentUser['access_tier'] ?? '') === 'restrito';
$canUseSideMenu = $isAdmin || $isModerator || $isUploader || $isSupportStaff;

$supportUrl = base_path('/support');
$supportBadge = 0;
$pendingPayments = 0;
$pendingSupport = 0;
$pendingUploads = 0;

if ($isLoggedIn && !empty($currentUser)) {
    if (!$isSupportStaff) {
        $supportBadge = \App\Models\SupportReply::countPendingForUser((int)$currentUser['id']);
    }
    if ($isAdmin) {
        $pendingPayments = \App\Models\Payment::countPending();
    }
    if ($isSupportStaff) {
        $pendingSupport = \App\Models\SupportMessage::countOpenForStaff();
    }
    if ($isAdmin || $isUploader || $isModerator) {
        $pendingUploads = \App\Models\Upload::countPending();
    }
}

$activePage = $activePage ?? '';
$displaySystemName = trim(preg_replace('/\s*\[DEV\]\s*/i', '', (string)$systemName));
?>
<aside class="app-sidebar">
    <a class="sidebar-brand" href="<?= base_path('/home') ?>">
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
    </a>
    <nav class="sidebar-nav">
        <?php if ($isLoggedIn): ?>
            <div class="nav-section">Navegacao</div>
            <?php if (!$isRestricted): ?>
                <a class="nav-link <?= $activePage === 'libraries' ? 'active' : '' ?>" href="<?= base_path('/lib') ?>">
                    <i class="bi bi-collection"></i>
                    <span>Bibliotecas</span>
                </a>
                <a class="nav-link <?= $activePage === 'loja' ? 'active' : '' ?>" href="<?= base_path('/loja') ?>">
                    <i class="bi bi-bag"></i>
                    <span>Loja</span>
                </a>
            <?php endif; ?>
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
                    <span>Painel</span>
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
                    <span>Upload Manager</span>
                    <?php if (!empty($pendingUploads)): ?>
                        <span class="badge bg-danger ms-auto"><?= (int)$pendingUploads ?></span>
                    <?php endif; ?>
                </a>
                <a class="nav-link" href="<?= upload_url('/upload') ?>">
                    <i class="bi bi-cloud-arrow-up"></i>
                    <span>Enviar arquivo</span>
                </a>
            <?php endif; ?>
            <?php if ($isAdmin): ?>
                <a class="nav-link" href="<?= base_path('/admin/news') ?>">
                    <i class="bi bi-megaphone"></i>
                    <span>Noticias</span>
                </a>
                <a class="nav-link" href="<?= base_path('/admin/notifications') ?>">
                    <i class="bi bi-bell"></i>
                    <span>Notificacoes</span>
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
