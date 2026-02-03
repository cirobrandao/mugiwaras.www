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
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php $supportUrl = (!empty($currentUser) && in_array($currentUser['role'], ['admin','superadmin','moderator'], true)) ? base_path('/admin/support') : base_path('/support'); ?>
                    <?php $supportBadge = 0; ?>
                    <?php if (!empty($currentUser) && in_array($currentUser['role'], ['none','uploader'], true)): ?>
                        <?php $supportBadge = \App\Models\SupportReply::countPendingForUser((int)$currentUser['id']); ?>
                    <?php endif; ?>
                    <?php if (!empty($currentUser) && ($currentUser['access_tier'] ?? '') !== 'restrito'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_path('/libraries') ?>">Bibliotecas</a></li>
                    <?php endif; ?>
                    <?php if (!empty($currentUser) && in_array($currentUser['role'], ['superadmin','admin','moderator','uploader'], true)): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_path('/upload') ?>">Enviar arquivo</a></li>
                    <?php endif; ?>
                    <?php if (!empty($currentUser) && ($currentUser['access_tier'] ?? '') !== 'restrito' && $currentUser['access_tier'] !== 'vitalicio' && !in_array($currentUser['role'], ['admin','superadmin'], true)): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_path('/payments') ?>">Compra Créditos</a></li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $supportUrl ?>">
                            Suporte
                            <?php if (!empty($supportBadge)): ?>
                                <span class="badge bg-danger ms-1"><?= (int)$supportBadge ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if (!empty($currentUser) && in_array($currentUser['role'], ['admin','superadmin','moderator'], true)): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_path('/admin') ?>">Admin</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_path('/logout') ?>">Sair</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_path('/') ?>">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_path('/register') ?>">Registrar</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_path('/support') ?>">Suporte</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
 </nav>
<?php endif; ?>
<main class="container py-4">
    <?= $content ?? '' ?>
</main>
<script src="https://cdn.zone.net.br/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('assets/js/phone-mask.js') ?>"></script>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
