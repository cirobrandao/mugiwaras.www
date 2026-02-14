<?php
use App\Core\View;
ob_start();
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-warning mb-3"><?= View::e($error) ?></div>
<?php endif; ?>

<div class="portal-container">
<div class="library-catalog">
    <div class="library-catalog-header">
        <div class="library-header-content">
            <h1 class="library-title">
                <i class="bi bi-collection-fill"></i>
                Bibliotecas
            </h1>
            <p class="library-subtitle">Explore nosso catálogo de conteúdo</p>
        </div>
    </div>
    
    <?php if (empty($categories)): ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <div class="empty-title">Nenhuma biblioteca disponível</div>
            <div class="empty-text">As categorias aparecerão aqui quando estiverem disponíveis.</div>
        </div>
    <?php else: ?>
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
                <?php 
                    $banner = !empty($cat['banner_path']) ? base_path('/' . ltrim((string)$cat['banner_path'], '/')) : ''; 
                    $categoryName = View::e((string)$cat['name']);
                    $categorySlug = !empty($cat['slug']) ? (string)$cat['slug'] : \App\Models\Category::generateSlug((string)$cat['name']);
                    $categoryUrl = base_path('/lib/' . rawurlencode($categorySlug) . (!empty($iosTest) ? '?ios_test=1' : ''));
                    $hasRequirements = !empty($cat['requires_subscription']);
                    $tagColor = !empty($cat['tag_color']) ? (string)$cat['tag_color'] : '';
                ?>
                <a href="<?= $categoryUrl ?>" class="category-card">
                    <?php if ($hasRequirements): ?>
                        <div class="category-badge">
                            <i class="bi bi-star-fill"></i>
                            <span>Premium</span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($banner): ?>
                        <div class="category-cover">
                            <img src="<?= $banner ?>" alt="<?= $categoryName ?>" loading="lazy">
                        </div>
                    <?php else: ?>
                        <div class="category-cover category-cover-placeholder"<?= $tagColor ? ' style="background: linear-gradient(135deg, ' . View::e($tagColor) . 'dd 0%, ' . View::e($tagColor) . '88 100%);"' : '' ?>>
                            <i class="bi bi-collection"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="category-content">
                        <h3 class="category-title"><?= $categoryName ?></h3>
                        <div class="category-action">
                            <span>Explorar</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';

