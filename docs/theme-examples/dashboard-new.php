<?php
/**
 * Dashboard Page - Exemplo de uso do novo layout
 * 
 * Este arquivo mostra como estruturar uma página usando o layout moderno.
 * Pode ser usado no Controller DashboardController.php
 */

// ============================================
// CONTROLLER LOGIC (DashboardController.php)
// ============================================

// 1. Buscar dados do banco
$userId = $user['id'] ?? 0;

// Favoritos do usuário
$favoritesStmt = $db->prepare("
    SELECT s.name, s.category_name, s.latest_chapter, c.tag_color
    FROM user_favorites uf
    JOIN series s ON s.id = uf.series_id
    LEFT JOIN categories c ON c.id = s.category_id
    WHERE uf.user_id = ?
    ORDER BY uf.created_at DESC
    LIMIT 6
");
$favoritesStmt->execute([$userId]);
$favorites = $favoritesStmt->fetchAll();

// Notícias recentes
$newsStmt = $db->query("
    SELECT id, title, content, published_at, category
    FROM news
    WHERE status = 'published'
    ORDER BY published_at DESC
    LIMIT 3
");
$recentNews = $newsStmt->fetchAll();

// Top 10 séries
$topSeriesStmt = $db->query("
    SELECT s.id, s.name, c.name as category_name, c.tag_color,
           COUNT(r.id) as read_count
    FROM series s
    JOIN categories c ON c.id = s.category_id
    LEFT JOIN content_reads r ON r.series_id = s.id
    GROUP BY s.id
    ORDER BY read_count DESC
    LIMIT 10
");
$topSeries = $topSeriesStmt->fetchAll();

// Últimos lançamentos
$recentContentStmt = $db->query("
    SELECT ci.id, ci.title, ci.created_at,
           s.name as series_name, c.name as category_name
    FROM content_items ci
    JOIN series s ON s.id = ci.series_id
    JOIN categories c ON c.id = s.category_id
    ORDER BY ci.created_at DESC
    LIMIT 5
");
$recentContent = $recentContentStmt->fetchAll();

// Avisos (access alerts)
$accessAlerts = [];
if (!empty($user['package_expires_at'])) {
    $expiresAt = strtotime($user['package_expires_at']);
    $daysLeft = ceil(($expiresAt - time()) / 86400);
    
    if ($daysLeft > 7) {
        $accessAlerts[] = [
            'type' => 'success',
            'icon' => 'bi-check-circle-fill',
            'title' => 'Acesso Premium Ativo',
            'text' => 'Seu plano está ativo até ' . date('d/m/Y', $expiresAt)
        ];
    } elseif ($daysLeft > 0) {
        $accessAlerts[] = [
            'type' => 'warning',
            'icon' => 'bi-exclamation-triangle-fill',
            'title' => 'Acesso Premium expira em breve',
            'text' => "Restam apenas {$daysLeft} dias até o vencimento"
        ];
    } else {
        $accessAlerts[] = [
            'type' => 'danger',
            'icon' => 'bi-x-circle-fill',
            'title' => 'Acesso Premium Expirado',
            'text' => 'Renove seu plano para continuar acessando'
        ];
    }
}

// Verificar novos capítulos nos favoritos
$newChaptersCount = 0;
// ... lógica para contar novos capítulos

if ($newChaptersCount > 0) {
    $accessAlerts[] = [
        'type' => 'info',
        'icon' => 'bi-info-circle-fill',
        'title' => 'Novos capítulos disponíveis',
        'text' => "{$newChaptersCount} séries dos seus favoritos foram atualizadas"
    ];
}

// 2. Preparar variáveis para o layout
$pageTitle = 'Dashboard';
$pageDescription = 'Painel principal do usuário';
$currentPath = '/dashboard';

// 3. Capturar o conteúdo da view
ob_start();
?>

<!-- ============================================
     VIEW CONTENT (dashboard/index.php)
     ============================================ -->

<!-- Header -->
<div class="content-header">
    <h1 class="content-title">Olá, <?= View::e($user['name'] ?? 'Usuário') ?>! 👋</h1>
    <p class="content-subtitle">Bem-vindo de volta. Aqui está o que está acontecendo hoje.</p>
</div>

<!-- Main Grid: 2 Columns -->
<div class="row g-4">
    
    <!-- LEFT COLUMN: Main Content -->
    <div class="col-12 col-xl-8">
        
        <!-- Seção: Meus Favoritos -->
        <section class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="section-title mb-0">
                    <i class="bi bi-star-fill text-warning me-2"></i>
                    Meus Favoritos
                </h2>
                <a href="<?= base_path('/favorites') ?>" class="text-decoration-none">
                    Ver todos →
                </a>
            </div>
            
            <?php if (empty($favorites)): ?>
                <div class="card-soft text-center py-5">
                    <i class="bi bi-star display-1 text-muted mb-3"></i>
                    <p class="text-muted">Você ainda não tem favoritos.</p>
                    <a href="<?= base_path('/libraries') ?>" class="btn btn-primary">
                        Explorar Biblioteca
                    </a>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($favorites as $fav): ?>
                        <?php
                        $seriesSlug = slugify($fav['name']);
                        $categorySlug = slugify($fav['category_name']);
                        $gradients = [
                            'linear-gradient(135deg, #3b82f6, #2563eb)',
                            'linear-gradient(135deg, #8b5cf6, #7c3aed)',
                            'linear-gradient(135deg, #ef4444, #dc2626)',
                            'linear-gradient(135deg, #10b981, #059669)',
                            'linear-gradient(135deg, #f59e0b, #d97706)',
                            'linear-gradient(135deg, #06b6d4, #0891b2)',
                        ];
                        $gradient = $gradients[array_rand($gradients)];
                        ?>
                        <div class="col-12 col-md-6">
                            <a href="<?= base_path("/libraries/{$categorySlug}/{$seriesSlug}") ?>" 
                               class="card-horizontal">
                                <div class="card-horizontal-icon" style="background: <?= $gradient ?>;">
                                    <i class="bi bi-book-fill"></i>
                                </div>
                                <div class="card-horizontal-content">
                                    <div class="card-horizontal-title"><?= View::e($fav['name']) ?></div>
                                    <div class="card-horizontal-subtitle">
                                        <?= View::e($fav['latest_chapter'] ?? 'N/A') ?> • 
                                        <?= View::e($fav['category_name']) ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        
        <!-- Seção: Notícias -->
        <section>
            <h2 class="section-title">
                <i class="bi bi-newspaper text-primary me-2"></i>
                Últimas Notícias
            </h2>
            
            <?php if (empty($recentNews)): ?>
                <div class="card-soft text-center py-5">
                    <i class="bi bi-newspaper display-1 text-muted mb-3"></i>
                    <p class="text-muted">Nenhuma notícia disponível no momento.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($recentNews as $news): ?>
                        <?php
                        $categoryPills = [
                            'manga' => 'pill-primary',
                            'update' => 'pill-success',
                            'announcement' => 'pill-info',
                        ];
                        $pillClass = $categoryPills[$news['category']] ?? 'pill-primary';
                        $timeAgo = time_ago($news['published_at']);
                        $excerpt = mb_substr(strip_tags($news['content']), 0, 150) . '...';
                        ?>
                        <div class="col-12">
                            <div class="card-soft">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="pill <?= $pillClass ?>">
                                        <?= View::e(ucfirst($news['category'])) ?>
                                    </span>
                                    <span class="text-muted" style="font-size: 0.875rem;">
                                        <i class="bi bi-clock me-1"></i><?= View::e($timeAgo) ?>
                                    </span>
                                </div>
                                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">
                                    <a href="<?= base_path('/news/' . $news['id']) ?>" 
                                       class="text-decoration-none text-dark">
                                        <?= View::e($news['title']) ?>
                                    </a>
                                </h3>
                                <p class="text-secondary mb-3" style="font-size: 0.9rem; line-height: 1.6;">
                                    <?= View::e($excerpt) ?>
                                </p>
                                <a href="<?= base_path('/news/' . $news['id']) ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    Ler mais →
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        
    </div>
    
    <!-- RIGHT COLUMN: Widgets -->
    <div class="col-12 col-xl-4">
        
        <!-- Widget: Avisos -->
        <?php if (!empty($accessAlerts)): ?>
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title">
                    <i class="bi bi-bell-fill"></i>
                    Avisos
                </h3>
            </div>
            
            <?php foreach ($accessAlerts as $alert): ?>
                <div class="widget-alert widget-alert-<?= $alert['type'] ?>">
                    <div class="widget-alert-icon">
                        <i class="bi <?= $alert['icon'] ?>"></i>
                    </div>
                    <div class="widget-alert-content">
                        <div class="widget-alert-title"><?= View::e($alert['title']) ?></div>
                        <div class="widget-alert-text"><?= View::e($alert['text']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Widget: Top 10 -->
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title">
                    <i class="bi bi-fire"></i>
                    Top 10 Séries
                </h3>
                <a href="<?= base_path('/top') ?>" class="widget-link">Ver tudo</a>
            </div>
            
            <?php if (empty($topSeries)): ?>
                <p class="text-muted text-center py-3">Nenhum dado disponível.</p>
            <?php else: ?>
                <ul class="widget-list">
                    <?php foreach (array_slice($topSeries, 0, 10) as $index => $series): ?>
                        <?php
                        $rank = $index + 1;
                        $seriesSlug = slugify($series['name']);
                        $categorySlug = slugify($series['category_name']);
                        ?>
                        <li class="widget-list-item">
                            <div class="widget-list-rank"><?= $rank ?></div>
                            <div class="widget-list-content">
                                <a href="<?= base_path("/libraries/{$categorySlug}/{$seriesSlug}") ?>"
                                   class="widget-list-title text-decoration-none">
                                    <?= View::e($series['name']) ?>
                                </a>
                                <div class="widget-list-subtitle">
                                    <span class="pill pill-primary" style="padding: 2px 8px;">
                                        <?= View::e($series['category_name']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="widget-list-meta">
                                <i class="bi bi-eye-fill me-1"></i><?= number_format($series['read_count']) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <!-- Widget: Últimos Lançamentos -->
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title">
                    <i class="bi bi-stars"></i>
                    Últimos Lançamentos
                </h3>
                <a href="<?= base_path('/recent') ?>" class="widget-link">Ver todos</a>
            </div>
            
            <?php if (empty($recentContent)): ?>
                <p class="text-muted text-center py-3">Nenhum lançamento recente.</p>
            <?php else: ?>
                <ul class="widget-list">
                    <?php foreach ($recentContent as $content): ?>
                        <?php $timeAgo = time_ago($content['created_at']); ?>
                        <li class="widget-list-item">
                            <div class="widget-list-icon">
                                <i class="bi bi-file-earmark-plus"></i>
                            </div>
                            <div class="widget-list-content">
                                <div class="widget-list-title"><?= View::e($content['series_name']) ?></div>
                                <div class="widget-list-subtitle">
                                    <i class="bi bi-clock"></i>
                                    <?= View::e($timeAgo) ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
    </div>
    
</div>

<?php
// 4. Capturar o conteúdo e incluir no layout
$content = ob_get_clean();
include __DIR__ . '/../layout-new.php';
?>
