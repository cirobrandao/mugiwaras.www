<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= View::e($pageDescription ?? 'Sistema Mugiwaras') ?>">
    <title><?= View::e($pageTitle ?? 'Dashboard') ?> - Mugiwaras</title>
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Theme CSS -->
    <link href="<?= base_path('assets/css/theme.css') ?>" rel="stylesheet">
    
    <!-- Custom CSS (se existir) -->
    <?php if (!empty($customCSS)): ?>
        <link href="<?= base_path('assets/css/' . $customCSS) ?>" rel="stylesheet">
    <?php endif; ?>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_path('assets/images/favicon.png') ?>">
    
    <?php if (!empty($headerExtras)): ?>
        <?= $headerExtras ?>
    <?php endif; ?>
</head>
<body <?= !empty($bodyClass) ? 'class="' . View::e($bodyClass) . '"' : '' ?>>
