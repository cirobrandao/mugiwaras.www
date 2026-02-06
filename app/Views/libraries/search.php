<?php
use App\Core\View;
ob_start();
?>
<?php if (!empty($error)): ?>
    <div class="alert alert-warning"><?= View::e($error) ?></div>
<?php endif; ?>

<?php if (empty($q)): ?>
    <div class="alert alert-secondary">Digite um termo para pesquisar.</div>
<?php else: ?>
    <?php if (empty($seriesResults)): ?>
        <div class="alert alert-secondary">Nenhuma série encontrada.</div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($seriesResults as $s): ?>
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="<?= base_path('/libraries/' . rawurlencode((string)($s['category_name'] ?? '')) . '/' . rawurlencode((string)$s['name'])) ?>">
                    <div>
                        <div class="fw-semibold"><?= View::e((string)$s['name']) ?></div>
                        <div class="small text-muted"><?= View::e((string)($s['category_name'] ?? '')) ?></div>
                    </div>
                    <span class="badge bg-secondary"><?= (int)($s['chapter_count'] ?? 0) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
