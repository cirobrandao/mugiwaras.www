<?php
use App\Core\View;
ob_start();
?>
<?php if (!empty($error)): ?>
    <div class="alert alert-warning"><?= View::e($error) ?></div>
<?php endif; ?>
<form method="get" action="<?= base_path('/libraries/search') ?>" class="mb-3">
    <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Pesquisar" value="<?= View::e((string)($q ?? '')) ?>">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        <?php if (!empty($q)): ?>
            <a class="btn btn-outline-secondary" href="<?= base_path('/libraries') ?>">Limpar</a>
        <?php endif; ?>
    </div>
</form>
<?php if (empty($categories)): ?>
    <div class="alert alert-secondary">Nenhuma biblioteca encontrada.</div>
<?php else: ?>
    <div class="library-grid-wrapper mx-auto">
    <div class="row g-3">
        <?php foreach ($categories as $cat): ?>
            <?php $banner = !empty($cat['banner_path']) ? base_path('/' . ltrim((string)$cat['banner_path'], '/')) : ''; ?>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 library-card">
                    <div class="card-img-wrap">
                        <?php if ($banner): ?>
                            <a href="<?= base_path('/libraries/' . rawurlencode((string)$cat['name']) . (!empty($iosTest) ? '?ios_test=1' : '')) ?>">
                                <img src="<?= $banner ?>" alt="<?= View::e((string)$cat['name']) ?>">
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="fw-semibold text-dark text-center w-100">
                            <a href="<?= base_path('/libraries/' . rawurlencode((string)$cat['name']) . (!empty($iosTest) ? '?ios_test=1' : '')) ?>" class="stretched-link text-decoration-none text-dark"><?= View::e((string)$cat['name']) ?></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
