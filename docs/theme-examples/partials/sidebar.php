<?php
/**
 * Sidebar Navigation
 * 
 * Variables available:
 * - $currentPath: Current path for active state
 * - $user: Current user object
 * - $unreadNotifications: Count of unread notifications
 * - $favoritesCount: Count of favorites
 */

$currentPath = $currentPath ?? '/dashboard';
$isAdmin = ($user['role'] ?? '') === 'admin';
$favoritesCount = $favoritesCount ?? 0;
$unreadNotifications = $unreadNotifications ?? 0;
?>

<aside class="app-sidebar" id="appSidebar">
    <!-- Logo -->
    <div class="sidebar-header">
        <a href="<?= base_path('/') ?>" class="sidebar-logo">
            <i class="bi bi-hexagon-fill"></i>
            <span>Mugiwaras</span>
        </a>
    </div>
    
    <!-- Navigation -->
    <nav class="sidebar-nav">
        <!-- Main Section -->
        <div class="sidebar-nav-section">
            <div class="sidebar-nav-title">Menu Principal</div>
            
            <a href="<?= base_path('/dashboard') ?>" 
               class="sidebar-nav-item <?= $currentPath === '/dashboard' ? 'active' : '' ?>">
                <i class="bi bi-house-fill"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="<?= base_path('/libraries') ?>" 
               class="sidebar-nav-item <?= str_starts_with($currentPath, '/libraries') ? 'active' : '' ?>">
                <i class="bi bi-book-fill"></i>
                <span>Biblioteca</span>
            </a>
            
            <a href="<?= base_path('/favorites') ?>" 
               class="sidebar-nav-item <?= $currentPath === '/favorites' ? 'active' : '' ?>">
                <i class="bi bi-star-fill"></i>
                <span>Favoritos</span>
                <?php if ($favoritesCount > 0): ?>
                    <span class="sidebar-nav-badge"><?= $favoritesCount ?></span>
                <?php endif; ?>
            </a>
            
            <a href="<?= base_path('/reader') ?>" 
               class="sidebar-nav-item <?= str_starts_with($currentPath, '/reader') ? 'active' : '' ?>">
                <i class="bi bi-journal-text"></i>
                <span>Leitor</span>
            </a>
            
            <a href="<?= base_path('/news') ?>" 
               class="sidebar-nav-item <?= str_starts_with($currentPath, '/news') ? 'active' : '' ?>">
                <i class="bi bi-newspaper"></i>
                <span>Notícias</span>
                <?php if ($unreadNotifications > 0): ?>
                    <span class="sidebar-nav-badge"><?= $unreadNotifications ?></span>
                <?php endif; ?>
            </a>
        </div>
        
        <!-- Account Section -->
        <div class="sidebar-nav-section">
            <div class="sidebar-nav-title">Conta</div>
            
            <a href="<?= base_path('/profile') ?>" 
               class="sidebar-nav-item <?= $currentPath === '/profile' ? 'active' : '' ?>">
                <i class="bi bi-person-fill"></i>
                <span>Perfil</span>
            </a>
            
            <a href="<?= base_path('/loja') ?>" 
               class="sidebar-nav-item <?= str_starts_with($currentPath, '/loja') ? 'active' : '' ?>">
                <i class="bi bi-cart-fill"></i>
                <span>Loja</span>
            </a>
            
            <a href="<?= base_path('/support') ?>" 
               class="sidebar-nav-item <?= str_starts_with($currentPath, '/support') ? 'active' : '' ?>">
                <i class="bi bi-headset"></i>
                <span>Suporte</span>
            </a>
        </div>
        
        <?php if ($isAdmin): ?>
        <!-- Admin Section -->
        <div class="sidebar-nav-section">
            <div class="sidebar-nav-title">Administração</div>
            
            <a href="<?= base_path('/admin') ?>" 
               class="sidebar-nav-item <?= $currentPath === '/admin' ? 'active' : '' ?>">
                <i class="bi bi-gear-fill"></i>
                <span>Painel Admin</span>
            </a>
            
            <a href="<?= base_path('/admin/users') ?>" 
               class="sidebar-nav-item <?= $currentPath === '/admin/users' ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i>
                <span>Usuários</span>
            </a>
            
            <a href="<?= base_path('/admin/content') ?>" 
               class="sidebar-nav-item <?= $currentPath === '/admin/content' ? 'active' : '' ?>">
                <i class="bi bi-files"></i>
                <span>Conteúdo</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>
    
    <!-- Footer -->
    <div class="sidebar-footer">
        <a href="<?= base_path('/logout') ?>" class="sidebar-nav-item">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sair</span>
        </a>
    </div>
</aside>
