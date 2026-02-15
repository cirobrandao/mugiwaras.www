<?php
use App\Core\View;
ob_start();

$resultCount = is_array($seriesResults ?? null) ? count($seriesResults) : 0;
?>
<section class="section-card app-card">
    <div class="news-title-box">
        <div class="section-title">Busca nas bibliotecas</div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-warning mb-0"><?= View::e($error) ?></div>
    <?php elseif (empty($q)): ?>
        <div class="alert alert-secondary mb-0">Digite um termo para pesquisar.</div>
    <?php else: ?>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="text-muted small">Resultados para: <span class="fw-semibold"><?= View::e((string)$q) ?></span></div>
            <span class="badge bg-secondary"><?= (int)$resultCount ?> encontrado(s)</span>
        </div>

        <?php if (empty($seriesResults)): ?>
            <div class="alert alert-secondary mb-0">Nenhuma série encontrada.</div>
        <?php else: ?>
            <div class="list-group list-group-flush d-none d-md-block">
                <?php foreach ($seriesResults as $s): ?>
                    <?php $seriesName = (string)($s['name'] ?? ''); ?>
                    <?php $categoryName = (string)($s['category_name'] ?? ''); ?>
                    <?php $categorySlug = !empty($s['category_slug']) ? (string)$s['category_slug'] : \App\Models\Category::generateSlug($categoryName); ?>
                    <?php $chapterCount = (int)($s['chapter_count'] ?? 0); ?>
                    <?php $seriesId = (int)($s['id'] ?? 0); ?>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="<?= base_path('/lib/' . rawurlencode($categorySlug) . '/' . $seriesId) ?>">
                        <div class="d-flex flex-column">
                            <span class="fw-semibold"><?= View::e($seriesName) ?></span>
                            <span class="small text-muted"><?= View::e($categoryName) ?></span>
                        </div>
                        <span class="badge bg-secondary"><?= $chapterCount ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="d-md-none">
                <?php foreach ($seriesResults as $s): ?>
                    <?php $seriesName = (string)($s['name'] ?? ''); ?>
                    <?php $categoryName = (string)($s['category_name'] ?? ''); ?>
                    <?php $categorySlug = !empty($s['category_slug']) ? (string)$s['category_slug'] : \App\Models\Category::generateSlug($categoryName); ?>
                    <?php $chapterCount = (int)($s['chapter_count'] ?? 0); ?>
                    <?php $seriesId = (int)($s['id'] ?? 0); ?>
                    <a class="card mb-2 library-list-card text-decoration-none" href="<?= base_path('/lib/' . rawurlencode($categorySlug) . '/' . $seriesId) ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-2 library-card-row">
                                <div>
                                    <div class="fw-semibold text-dark"><?= View::e($seriesName) ?></div>
                                    <div class="small text-muted"><?= View::e($categoryName) ?></div>
                                </div>
                                <span class="badge bg-secondary"><?= $chapterCount ?></span>
                            </div>
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
