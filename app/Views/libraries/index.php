<?php
use App\Core\View;
ob_start();
?>
<?php if (!empty($error)): ?>
    <div class="alert alert-warning"><?= View::e($error) ?></div>
<?php endif; ?>
<div class="libraries-header mb-4">
    <div class="d-flex align-items-center justify-content-between gap-3 mb-2">
        <div>
            <h1 class="h3 mb-1 fw-bold">Bibliotecas</h1>
            <p class="text-muted small mb-0">Explore as categorias disponíveis</p>
        </div>
        <?php if (!empty($categories)): ?>
            <div class="badge bg-primary-subtle text-primary px-3 py-2">
                <i class="bi bi-collection me-1"></i>
                <?= count($categories) ?> <?= count($categories) === 1 ? 'categoria' : 'categorias' ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($categories)): ?>
    <div class="alert alert-secondary">
        <i class="bi bi-inbox me-2"></i>
        Nenhuma biblioteca encontrada.
    </div>
<?php else: ?>
    <div class="library-categories-grid">
        <?php foreach ($categories as $cat): ?>
            <?php 
                $banner = !empty($cat['banner_path']) ? base_path('/' . ltrim((string)$cat['banner_path'], '/')) : ''; 
                $categoryName = View::e((string)$cat['name']);
                $categoryUrl = base_path('/libraries/' . rawurlencode((string)$cat['name']) . (!empty($iosTest) ? '?ios_test=1' : ''));
                $hasRequirements = !empty($cat['requires_subscription']);
            ?>
            <a href="<?= $categoryUrl ?>" class="library-category-card text-decoration-none">
                <div class="library-category-banner">
                    <?php if ($banner): ?>
                        <img src="<?= $banner ?>" alt="<?= $categoryName ?>" loading="lazy">
                        <div class="library-category-overlay"></div>
                    <?php else: ?>
                        <div class="library-category-placeholder">
                            <i class="bi bi-collection"></i>
                        </div>
                    <?php endif; ?>
                    <?php if ($hasRequirements): ?>
                        <div class="library-category-badge">
                            <i class="bi bi-star-fill"></i>
                            <span>Premium</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="library-category-content">
                    <h3 class="library-category-title"><?= $categoryName ?></h3>
                    <div class="library-category-action">
                        <span>Explorar</span>
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
