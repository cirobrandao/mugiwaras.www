<?php
/**
 * Topbar - Search, notifications, avatar
 * 
 * Variables available:
 * - $user: Current user object
 * - $notificationCount: Count of unread notifications
 */

$userName = $user['name'] ?? 'Usuário';
$userRole = $user['role'] ?? 'free';
$userAvatar = $user['avatar'] ?? 'https://i.pravatar.cc/150?img=3';
$hasNotifications = ($notificationCount ?? 0) > 0;

// Role display names
$roleDisplayNames = [
    'admin' => 'Administrador',
    'premium' => 'Premium',
    'vip' => 'VIP',
    'free' => 'Gratuito'
];
$userRoleDisplay = $roleDisplayNames[$userRole] ?? 'Usuário';
?>

<header class="app-topbar">
    <!-- Mobile Toggle -->
    <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>
    
    <!-- Search -->
    <div class="topbar-search">
        <i class="bi bi-search"></i>
        <input type="search" 
               name="q" 
               placeholder="Buscar séries, categorias..." 
               aria-label="Buscar conteúdo"
               autocomplete="off" />
    </div>
    
    <!-- Actions -->
    <div class="topbar-actions">
        <!-- Notifications -->
        <button class="topbar-icon-btn" 
                title="Notificações"
                aria-label="Notificações"
                onclick="window.location.href='<?= base_path('/notifications') ?>'">
            <i class="bi bi-bell-fill"></i>
            <?php if ($hasNotifications): ?>
                <span class="badge"></span>
            <?php endif; ?>
        </button>
        
        <!-- Theme Toggle (Optional) -->
        <button class="topbar-icon-btn" 
                title="Alternar tema"
                aria-label="Alternar tema"
                id="themeToggle">
            <i class="bi bi-moon-fill"></i>
        </button>
        
        <!-- User Avatar & Dropdown -->
        <a href="<?= base_path('/profile') ?>" class="topbar-avatar">
            <img src="<?= View::e($userAvatar) ?>" 
                 alt="Avatar de <?= View::e($userName) ?>">
            <div class="topbar-avatar-info">
                <div class="topbar-avatar-name"><?= View::e($userName) ?></div>
                <div class="topbar-avatar-role"><?= View::e($userRoleDisplay) ?></div>
            </div>
        </a>
    </div>
</header>
