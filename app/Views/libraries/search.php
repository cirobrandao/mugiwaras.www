<?php
use App\Core\View;
ob_start();

$resultCount = is_array($seriesResults ?? null) ? count($seriesResults) : 0;
?>
<section class="search-page">
    <div class="search-header">
        <div class="search-header-content">
            <div class="search-icon-wrapper">
                <i class="bi bi-search"></i>
            </div>
            <div>
                <h1 class="search-title">Busca nas Bibliotecas</h1>
                <p class="search-subtitle">Encontre suas séries favoritas</p>
            </div>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="search-error">
            <i class="bi bi-exclamation-triangle"></i>
            <span><?= View::e($error) ?></span>
        </div>
    <?php elseif (empty($q)): ?>
        <div class="search-empty-state">
            <div class="search-empty-icon">
                <i class="bi bi-search"></i>
            </div>
            <h3>Pesquise nas bibliotecas</h3>
            <p>Use o campo de busca acima para encontrar suas séries favoritas</p>
        </div>
    <?php else: ?>
        <div class="search-meta">
            <div class="search-query">
                Resultados para: <span class="search-term"><?= View::e((string)$q) ?></span>
            </div>
            <div class="search-count">
                <i class="bi bi-collection"></i>
                <span><?= (int)$resultCount ?></span>
            </div>
        </div>

        <?php if (empty($seriesResults)): ?>
            <div class="search-no-results">
                <div class="search-no-results-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h3>Nenhum resultado encontrado</h3>
                <p>Tente usar outros termos de busca</p>
            </div>
        <?php else: ?>
            <div class="search-results">
                <?php foreach ($seriesResults as $s): ?>
                    <?php $seriesName = (string)($s['name'] ?? ''); ?>
                    <?php $categoryName = (string)($s['category_name'] ?? ''); ?>
                    <?php $categorySlug = !empty($s['category_slug']) ? (string)$s['category_slug'] : \App\Models\Category::generateSlug($categoryName); ?>
                    <?php $chapterCount = (int)($s['chapter_count'] ?? 0); ?>
                    <?php $seriesId = (int)($s['id'] ?? 0); ?>
                    <a class="search-result-card" href="<?= base_path('/lib/' . rawurlencode($categorySlug) . '/' . $seriesId) ?>">
                        <div class="search-result-icon">
                            <i class="bi bi-collection-fill"></i>
                        </div>
                        <div class="search-result-content">
                            <div class="search-result-name"><?= View::e($seriesName) ?></div>
                            <div class="search-result-category">
                                <i class="bi bi-folder2"></i>
                                <span><?= View::e($categoryName) ?></span>
                            </div>
                        </div>
                        <div class="search-result-badge">
                            <i class="bi bi-journals"></i>
                            <span><?= $chapterCount ?></span>
                        </div>
                        <div class="search-result-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
