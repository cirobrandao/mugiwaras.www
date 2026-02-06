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
    <?php $orientation = (string)($category['display_orientation'] ?? 'vertical'); ?>
    <?php $listClass = $orientation === 'horizontal' ? 'list-group list-group-horizontal flex-wrap' : 'list-group'; ?>
    <?php $allowCbz = !empty($category['content_cbz']); ?>
    <?php $allowPdf = !empty($category['content_pdf']); ?>
    <?php $allowEpub = !empty($category['content_epub']); ?>
    <div class="<?= $listClass ?>">
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
            <div class="list-group-item<?= $isPinned ? ' bg-warning-subtle' : '' ?>">
                <div class="d-flex justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                    <form method="post" action="<?= base_path('/libraries/series/favorite') ?>">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                        <input type="hidden" name="id" value="<?= $seriesId ?>">
                        <input type="hidden" name="action" value="<?= $isFav ? 'remove' : 'add' ?>">
                        <button class="btn btn-sm <?= $isFav ? 'btn-warning' : 'btn-outline-warning' ?>" type="submit" aria-label="<?= $isFav ? 'Remover favorito' : 'Favoritar' ?>">
                            <?= $isFav ? '★' : '☆' ?>
                        </button>
                    </form>

                        <div>
                            <?php $isAdultSeries = !empty($s['adult_only']); ?>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <a class="text-decoration-none fw-semibold" href="<?= base_path('/libraries/' . rawurlencode((string)$category['name']) . '/' . rawurlencode((string)$s['name']) . '?format=' . $entry['format'] . (!empty($iosTest) ? '&ios_test=1' : '')) ?>">
                                    <?= View::e((string)$s['name']) ?>
                                </a>
                                <?php if ($isAdultSeries): ?>
                                    <span class="badge bg-danger ms-2">18+</span>
                                <?php endif; ?>
                                <?php if ($entry['tag'] !== ''): ?>
                                    <span class="badge bg-warning text-dark ms-2"><?= View::e($entry['tag']) ?></span>
                                <?php endif; ?>
                                <?php if ($isPinned): ?>
                                    <span class="badge bg-primary ms-2">Em destaque</span>
                                <?php endif; ?>
                                <?php if ($entry['format'] === 'empty'): ?>
                                    <span class="badge bg-secondary ms-2">Sem conteúdo</span>
                                <?php elseif ($entry['format'] === 'pending'): ?>
                                    <span class="badge bg-info text-dark ms-2">Aguardando conversão</span>
                                <?php endif; ?>
                                <?php if ($pendingCount > 0 && $canPin && $entry['format'] !== 'pending'): ?>
                                    <span class="badge bg-info text-dark ms-2">Em conversão: <?= $pendingCount ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="small text-muted">Capítulos: <?= (int)$entry['count'] ?></div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <?php if ($canPin || $canManage): ?>
                            <span class="text-muted">|</span>
                        <?php endif; ?>
                        <?php if ($canPin): ?>
                            <form method="post" action="<?= base_path('/libraries/series/pin') ?>" class="d-flex align-items-center gap-1">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                <input type="hidden" name="id" value="<?= $seriesId ?>">
                                <input class="form-control form-control-sm" type="number" name="pin_order" value="<?= (int)($s['pin_order'] ?? 0) ?>" style="width: 70px;" min="0">
                                <button class="btn btn-sm btn-outline-primary" type="submit" title="Salvar ordem" aria-label="Salvar ordem">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                        <?php if ($canManage): ?>
                            <form method="post" action="<?= base_path('/libraries/series/adult') ?>">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                <input type="hidden" name="id" value="<?= $seriesId ?>">
                                <input type="hidden" name="adult_only" value="<?= $isAdultSeries ? 0 : 1 ?>">
                                <button class="btn btn-sm <?= $isAdultSeries ? 'btn-danger' : 'btn-outline-secondary' ?>" type="submit" title="<?= $isAdultSeries ? 'Remover 18+' : 'Definir 18+' ?>" aria-pressed="<?= $isAdultSeries ? 'true' : 'false' ?>">
                                    <i class="fa-solid <?= $isAdultSeries ? 'fa-circle-check' : 'fa-circle' ?>"></i>
                                    <span class="badge bg-light text-danger ms-1">18+</span>
                                </button>
                            </form>
                            <?php $editModalId = 'edit-series-' . $seriesId; ?>
                            <button class="btn btn-sm btn-outline-secondary" type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#<?= $editModalId ?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <?php $deleteModalId = 'delete-series-' . $seriesId; ?>
                            <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#<?= $deleteModalId ?>" title="Excluir" aria-label="Excluir">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($canManage): ?>
                    <div class="modal fade" id="<?= $editModalId ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar série</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <form method="post" action="<?= base_path('/libraries/series/update') ?>">
                                    <div class="modal-body">
                                        <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                        <input type="hidden" name="id" value="<?= $seriesId ?>">
                                        <label class="form-label" for="series-name-<?= $seriesId ?>">Nome</label>
                                        <input class="form-control" id="series-name-<?= $seriesId ?>" type="text" name="name" value="<?= View::e((string)$s['name']) ?>" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button class="btn btn-primary" type="submit">Salvar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="<?= $deleteModalId ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Remover série</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <div class="modal-body">
                                    Tem certeza que deseja remover a série <strong><?= View::e((string)$s['name']) ?></strong> e seus conteúdos?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <form method="post" action="<?= base_path('/libraries/series/delete') ?>" class="m-0">
                                        <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                        <input type="hidden" name="id" value="<?= $seriesId ?>">
                                        <button class="btn btn-danger" type="submit">Confirmar remoção</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
