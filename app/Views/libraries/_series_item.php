<?php
use App\Core\View;

// Este arquivo é incluído por category.php e espera que $s esteja definido
$seriesId = (int)($s['id'] ?? 0);
$canPin = !empty($user) && (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user) || \App\Core\Auth::isEquipe($user));
$canManage = !empty($user) && (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user));
$isFav = !empty($favoriteSeries) && in_array($seriesId, $favoriteSeries, true);
$cbzCount = (int)($s['cbz_count'] ?? 0);
$pdfCount = (int)($s['pdf_count'] ?? 0);
$epubCount = (int)($s['epub_count'] ?? 0);
$entries = [];
$pendingCount = (int)($pendingCounts[$seriesId] ?? 0);

if ($allowCbz && $cbzCount > 0): $entries[] = ['format' => 'cbz', 'count' => $cbzCount, 'tag' => '']; endif;
if ($allowEpub && $epubCount > 0): $entries[] = ['format' => 'epub', 'count' => $epubCount, 'tag' => 'EPUB']; endif;
if ($allowPdf && $pdfCount > 0): $entries[] = ['format' => 'pdf', 'count' => $pdfCount, 'tag' => 'PDF']; endif;

if (empty($entries) && $canPin):
    if ($pendingCount > 0):
        $entries[] = ['format' => 'pending', 'count' => 0, 'tag' => ''];
    else:
        $entries[] = ['format' => 'empty', 'count' => 0, 'tag' => ''];
    endif;
endif;

foreach ($entries as $entry):
    $isPinned = (int)($s['pin_order'] ?? 0) > 0;
    $editModalId = 'edit-series-' . $seriesId;
    $deleteModalId = 'delete-series-' . $seriesId;
?>
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
                $seriesUrl = base_path('/lib/' . rawurlencode($categorySlug) . '/' . (int)($s['id'] ?? 0) . '?format=' . $entry['format'] . (!empty($iosTest) ? '&ios_test=1' : ''));
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

<?php if ($canManage): ?>
    <!-- Edit Series Modal -->
    <div class="modal fade" id="<?= $editModalId ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar série</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form method="post" action="<?= base_path('/lib/series/update') ?>">
                    <div class="modal-body">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                        <input type="hidden" name="id" value="<?= $seriesId ?>">
                        <label class="form-label" for="name-<?= $seriesId ?>">Nome da Série</label>
                        <input class="form-control" id="name-<?= $seriesId ?>" type="text" name="name" value="<?= View::e((string)$s['name']) ?>" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary" type="submit">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Series Modal -->
    <div class="modal fade" id="<?= $deleteModalId ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Excluir série</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja excluir a série <strong><?= View::e((string)$s['name']) ?></strong>? Todos os conteúdos serão removidos permanentemente.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="post" action="<?= base_path('/lib/series/delete') ?>" class="m-0">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                        <input type="hidden" name="id" value="<?= $seriesId ?>">
                        <button class="btn btn-danger" type="submit">Confirmar exclusão</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php endforeach; ?>
