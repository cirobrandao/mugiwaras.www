<?php
use App\Core\View;
ob_start();
$seriesCount = is_array($series ?? null) ? count($series) : 0;
?>
<?php if (!empty($error)): ?>
    <div class="alert alert-warning"><?= View::e($error) ?></div>
<?php endif; ?>
<?php if (empty($series)): ?>
    <div class="alert alert-secondary">Nenhuma série encontrada.</div>
<?php else: ?>
    <?php $allowCbz = !empty($category['content_cbz']); ?>
    <?php $allowPdf = !empty($category['content_pdf']); ?>
    <?php $allowEpub = !empty($category['content_epub']); ?>
<div class="portal-container">
    <section class="category-content">
        <div class="category-header">
            <nav aria-label="breadcrumb" class="category-breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active" aria-current="page"><?= View::e($category['name'] ?? 'Categoria') ?></li>
                </ol>
            </nav>
        </div>
        <div class="category-list">
        <?php foreach ($series as $s): ?>
            <?php $seriesId = (int)($s['id'] ?? 0); ?>
            <?php $canPin = !empty($user) && (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user) || \App\Core\Auth::isEquipe($user)); ?>
            <?php $canManage = !empty($user) && (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user)); ?>
            <?php $isFav = !empty($favoriteSeries) && in_array($seriesId, $favoriteSeries, true); ?>
            <?php $cbzCount = (int)($s['cbz_count'] ?? 0); ?>
            <?php $pdfCount = (int)($s['pdf_count'] ?? 0); ?>
            <?php $epubCount = (int)($s['epub_count'] ?? 0); ?>
            <?php $entries = []; ?>
            <?php $pendingCount = (int)($pendingCounts[$seriesId] ?? 0); ?>
            <?php if ($allowCbz && $cbzCount > 0): $entries[] = ['format' => 'cbz', 'count' => $cbzCount, 'tag' => '']; endif; ?>
            <?php if ($allowEpub && $epubCount > 0): $entries[] = ['format' => 'epub', 'count' => $epubCount, 'tag' => 'EPUB']; endif; ?>
            <?php if ($allowPdf && $pdfCount > 0): $entries[] = ['format' => 'pdf', 'count' => $pdfCount, 'tag' => 'PDF']; endif; ?>
            <?php if (empty($entries) && $canPin): ?>
                <?php if ($pendingCount > 0): ?>
                    <?php $entries[] = ['format' => 'pending', 'count' => 0, 'tag' => '']; ?>
                <?php else: ?>
                    <?php $entries[] = ['format' => 'empty', 'count' => 0, 'tag' => '']; ?>
                <?php endif; ?>
            <?php endif; ?>
            <?php foreach ($entries as $entry): ?>
            <?php $isPinned = (int)($s['pin_order'] ?? 0) > 0; ?>
            <?php $editModalId = 'edit-series-' . $seriesId; ?>
            <?php $deleteModalId = 'delete-series-' . $seriesId; ?>
            <div class="category-item<?= $isPinned ? ' is-pinned' : '' ?>">
                <div class="category-item-content">
                    <div class="category-item-fav">
                        <form method="post" action="<?= base_path('/lib/series/favorite') ?>" class="d-inline">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                            <input type="hidden" name="id" value="<?= $seriesId ?>">
                            <input type="hidden" name="action" value="<?= $isFav ? 'remove' : 'add' ?>">
                            <button type="submit" class="btn-fav <?= $isFav ? 'is-fav' : '' ?>" aria-label="<?= $isFav ? 'Remover favorito' : 'Favoritar' ?>">
                                <i class="bi bi-star<?= $isFav ? '-fill' : '' ?>"></i>
                            </button>
                        </form>
                    </div>
                    <div class="category-item-info">
                            <?php $isAdultSeries = !empty($s['adult_only']); ?>
                            <?php
                                $categorySlug = !empty($category['slug']) ? (string)$category['slug'] : \App\Models\Category::generateSlug((string)$category['name']);
                                $seriesUrl = base_path('/lib/' . rawurlencode($categorySlug) . '/' . rawurlencode((string)$s['name']) . '?format=' . $entry['format'] . (!empty($iosTest) ? '&ios_test=1' : ''));
                            ?>
                            <a class="category-item-title" href="<?= $seriesUrl ?>">
                                <?= View::e((string)$s['name']) ?>
                            </a>
                            <div class="category-item-badges">
                                    <?php if ($isAdultSeries): ?>
                                        <span class="badge bg-danger">18+</span>
                                    <?php endif; ?>
                                    <?php if ($entry['tag'] !== ''): ?>
                                        <span class="badge bg-warning text-dark"><?= View::e($entry['tag']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($isPinned): ?>
                                        <span class="badge bg-primary">Em destaque</span>
                                    <?php endif; ?>
                                    <?php if ($entry['format'] === 'empty'): ?>
                                        <span class="badge bg-secondary">Sem conteúdo</span>
                                    <?php elseif ($entry['format'] === 'pending'): ?>
                                        <span class="badge bg-info text-dark">Aguardando conversão</span>
                                    <?php endif; ?>
                                    <?php if ($pendingCount > 0 && $canPin && $entry['format'] !== 'pending'): ?>
                                        <span class="badge bg-info text-dark">Em conversão: <?= $pendingCount ?></span>
                                    <?php endif; ?>
                                </div>
                            <div class="category-item-meta">
                                <i class="bi bi-book"></i><?= (int)$entry['count'] ?> capítulos
                            </div>
                    </div>
                    <div class="category-item-actions">
                        <?php if ($canPin || $canManage): ?>
                            <div class="category-item-admin">
                        <?php endif; ?>
                        <?php if ($canPin): ?>
                            <form method="post" action="<?= base_path('/lib/series/pin') ?>" class="d-flex align-items-center gap-1">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                <input type="hidden" name="id" value="<?= $seriesId ?>">
                                <input class="form-control form-control-sm" type="number" name="pin_order" value="<?= (int)($s['pin_order'] ?? 0) ?>" style="width: 70px;" min="0">
                                <button class="btn btn-sm btn-outline-primary" type="submit" title="Salvar ordem" aria-label="Salvar ordem">
                                    <i class="bi bi-floppy"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                        <?php if ($canManage): ?>
                            <form method="post" action="<?= base_path('/lib/series/adult') ?>">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                <input type="hidden" name="id" value="<?= $seriesId ?>">
                                <input type="hidden" name="adult_only" value="<?= $isAdultSeries ? 0 : 1 ?>">
                                <button class="btn btn-sm <?= $isAdultSeries ? 'btn-danger' : 'btn-outline-secondary' ?>" type="submit" title="<?= $isAdultSeries ? 'Remover 18+' : 'Definir 18+' ?>" aria-pressed="<?= $isAdultSeries ? 'true' : 'false' ?>">
                                    <i class="bi <?= $isAdultSeries ? 'bi-check-circle-fill' : 'bi-circle' ?>"></i>
                                    <span class="badge bg-light text-danger ms-1">18+</span>
                                </button>
                            </form>
                            <button class="btn btn-sm btn-outline-secondary" type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#<?= $editModalId ?>">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#<?= $deleteModalId ?>" title="Excluir" aria-label="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        <?php endif; ?>
                        <?php if ($canPin || $canManage): ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </div>
    </section>
</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
