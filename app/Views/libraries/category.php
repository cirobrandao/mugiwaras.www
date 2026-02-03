<?php
use App\Core\View;
ob_start();
?>
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_path('/libraries') ?>">Bibliotecas</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= View::e($category['name'] ?? 'Categoria') ?></li>
    </ol>
</nav>
<?php if (!empty($error)): ?>
    <div class="alert alert-warning"><?= View::e($error) ?></div>
<?php endif; ?>
<?php if (empty($series)): ?>
    <div class="alert alert-secondary">Nenhuma série encontrada.</div>
<?php else: ?>
    <div class="list-group">
        <?php foreach ($series as $s): ?>
            <?php $seriesId = (int)($s['id'] ?? 0); ?>
            <?php $isFav = !empty($favoriteSeries) && in_array($seriesId, $favoriteSeries, true); ?>
            <?php $cbzCount = (int)($s['cbz_count'] ?? 0); ?>
            <?php $pdfCount = (int)($s['pdf_count'] ?? 0); ?>
            <?php $entries = []; ?>
            <?php if ($cbzCount > 0): $entries[] = ['format' => 'cbz', 'count' => $cbzCount, 'tag' => '']; endif; ?>
            <?php if ($pdfCount > 0): $entries[] = ['format' => 'pdf', 'count' => $pdfCount, 'tag' => 'PDF']; endif; ?>
            <?php foreach ($entries as $entry): ?>
            <div class="list-group-item">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <form method="post" action="<?= base_path('/libraries/series/favorite') ?>">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                        <input type="hidden" name="id" value="<?= $seriesId ?>">
                        <input type="hidden" name="action" value="<?= $isFav ? 'remove' : 'add' ?>">
                        <button class="btn btn-sm <?= $isFav ? 'btn-warning' : 'btn-outline-warning' ?>" type="submit" aria-label="<?= $isFav ? 'Remover favorito' : 'Favoritar' ?>">
                            <?= $isFav ? '★' : '☆' ?>
                        </button>
                    </form>

                    <div class="flex-grow-1">
                        <a class="text-decoration-none fw-semibold" href="<?= base_path('/libraries/' . rawurlencode((string)$category['name']) . '/' . rawurlencode((string)$s['name']) . '?format=' . $entry['format'] . (!empty($iosTest) ? '&ios_test=1' : '')) ?>">
                            <?= View::e((string)$s['name']) ?>
                        </a>
                        <?php if ($entry['tag'] !== ''): ?>
                            <span class="badge bg-warning text-dark ms-2">PDF</span>
                        <?php endif; ?>
                        <div class="small text-muted">Capítulos: <?= (int)$entry['count'] ?></div>
                    </div>

                    <?php if (!empty($user) && in_array($user['role'], ['superadmin','admin','moderator'], true)): ?>
                        <form method="post" action="<?= base_path('/libraries/series/delete') ?>" onsubmit="return confirm('Remover série?');">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                            <input type="hidden" name="id" value="<?= $seriesId ?>">
                            <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if (!empty($user) && in_array($user['role'], ['superadmin','admin','moderator'], true)): ?>
                    <details class="mt-2">
                        <summary>Editar</summary>
                        <form method="post" action="<?= base_path('/libraries/series/update') ?>" class="mt-2">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                            <input type="hidden" name="id" value="<?= $seriesId ?>">
                            <div class="input-group input-group-sm" style="max-width: 420px;">
                                <input class="form-control" type="text" name="name" value="<?= View::e((string)$s['name']) ?>" required>
                                <button class="btn btn-outline-primary" type="submit">Salvar</button>
                            </div>
                        </form>
                    </details>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
