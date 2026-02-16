<?php
use App\Core\View;
use App\Core\SimpleCache;

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
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= View::e($title ?? $systemName) ?></title>
    <?php if (!empty($metaRobots)): ?>
        <meta name="robots" content="<?= View::e((string)$metaRobots) ?>">
    <?php endif; ?>
    <?php if (!empty($systemFavicon)): ?>
        <link rel="icon" href="<?= base_path('/' . ltrim((string)$systemFavicon, '/')) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= asset('/assets/bootstrap.min.css') ?>?v=5.3">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('/assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= asset('/assets/css/theme.css') ?>">
    <link rel="stylesheet" href="<?= asset('/assets/css/z1hd.css') ?>">
    <link rel="stylesheet" href="<?= asset('/assets/category-tags.css') ?>">
    <?php if (!empty($customCSS)): ?>
        <link href="<?= asset('/assets/css/' . $customCSS) ?>" rel="stylesheet">
    <?php endif; ?>
</head>
<body class="app-body<?= !empty($bodyClass) ? ' ' . View::e($bodyClass) : '' ?>">
