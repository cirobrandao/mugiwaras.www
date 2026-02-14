<?php
/**
 * Layout Base - Modern Theme
 * 
 * Estrutura: Sidebar + Topbar + Content
 * 
 * Para usar este layout em suas views:
 * 
 * 1. No Controller, defina as variáveis necessárias:
 *    - $pageTitle: Título da página
 *    - $pageDescription: Meta description
 *    - $currentPath: Path atual para sidebar active state
 *    - $user: Dados do usuário logado
 * 
 * 2. Capture o conteúdo da sua view:
 *    ob_start();
 *    include 'sua-view.php';
 *    $content = ob_get_clean();
 * 
 * 3. Inclua este layout:
 *    include 'layout.php';
 */

// Ensure required variables exist
$pageTitle = $pageTitle ?? 'Dashboard';
$pageDescription = $pageDescription ?? 'Sistema Mugiwaras';
$currentPath = $currentPath ?? '/dashboard';
$user = $user ?? [];
$content = $content ?? '';
$favoritesCount = $favoritesCount ?? 0;
$unreadNotifications = $unreadNotifications ?? 0;
$notificationCount = $notificationCount ?? 0;

// Include header (DOCTYPE, <head>, opening <body>)
include __DIR__ . '/partials/header.php';
?>

<div class="app-container">
    
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    
    <main class="app-main">
        
        <?php include __DIR__ . '/partials/topbar.php'; ?>
        
        <div>
            <?= $content ?>
        </div>
        
    </main>
    
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
