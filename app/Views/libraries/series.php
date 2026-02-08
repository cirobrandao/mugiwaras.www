<?php
use App\Core\View;
ob_start();
$itemCount = is_array($items ?? null) ? count($items) : 0;
?>
<?php
$seriesBaseUrl = base_path('/libraries/' . rawurlencode((string)($category['name'] ?? '')) . '/' . rawurlencode((string)($series['name'] ?? '')));
$baseQuery = [];
if (!empty($format)) $baseQuery[] = 'format=' . urlencode((string)$format);
if (!empty($iosTest)) $baseQuery[] = 'ios_test=1';
if (!empty($order)) $baseQuery[] = 'order=' . urlencode((string)$order);
$baseQueryString = empty($baseQuery) ? '' : '?' . implode('&', $baseQuery);
$nextOrder = ($order ?? 'asc') === 'asc' ? 'desc' : 'asc';
$orderLabel = ($order ?? 'asc') === 'asc' ? 'Crescente' : 'Decrescente';
$orderBtnLabel = ($order ?? 'asc') === 'asc' ? 'Inverter para decrescente' : 'Inverter para crescente';
$orderQuery = $baseQuery;
foreach ($orderQuery as $i => $entry) {
    if (str_starts_with($entry, 'order=')) {
        unset($orderQuery[$i]);
    }
}
$orderQuery[] = 'order=' . $nextOrder;
$orderUrl = $seriesBaseUrl . (empty($orderQuery) ? '' : '?' . implode('&', $orderQuery));
?>
<?php if (!empty($error)): ?>
    <div class="alert alert-warning"><?= View::e($error) ?></div>
<?php endif; ?>
<?php if (!empty($pending)): ?>
    <section class="section-card app-card mb-3">
        <div class="news-title-box">
            <div class="section-title">Aguardando conversão</div>
        </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($pending as $p): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><?= View::e((string)($p['title'] ?? $p['original_name'] ?? 'Arquivo')) ?></span>
                        <span class="badge bg-secondary">Em fila</span>
                    </li>
                <?php endforeach; ?>
            </ul>
    </section>
<?php endif; ?>
<?php if (empty($items)): ?>
    <div class="alert alert-secondary">Nenhum arquivo encontrado.</div>
<?php else: ?>
    <?php $hasPdf = false; ?>
    <section class="section-card app-card">
        <div class="news-title-box d-flex align-items-center justify-content-between gap-2">
            <nav aria-label="breadcrumb" class="breadcrumb-inline border rounded px-2 py-1 bg-white">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_path('/libraries/' . rawurlencode((string)($category['name'] ?? ''))) ?>"><?= View::e((string)($category['name'] ?? '')) ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= View::e((string)($series['name'] ?? '')) ?>
                        <?php if (!empty($series['adult_only'])): ?>
                            <span class="badge bg-danger ms-2">18+</span>
                        <?php endif; ?>
                    </li>
                </ol>
            </nav>
            <a class="btn btn-sm btn-outline-secondary border" href="<?= $orderUrl ?>" title="<?= View::e($orderBtnLabel) ?>" aria-label="<?= View::e($orderBtnLabel) ?>">
                <i class="fa-solid fa-arrow-up-short-wide"></i>
            </a>
        </div>
        <div class="list-group list-group-flush d-none d-md-block">
        <?php foreach ($items as $item): ?>
            <?php $itemPath = (string)($item['cbz_path'] ?? ''); ?>
            <?php $itemExt = strtolower(pathinfo($itemPath, PATHINFO_EXTENSION)); ?>
            <?php $isPdf = $itemExt === 'pdf'; ?>
            <?php $isEpub = $itemExt === 'epub'; ?>
            <?php $iosOnlyDownload = !empty($isIos); ?>
            <?php $downloadToken = !empty($downloadTokens[(int)$item['id']]) ? (string)$downloadTokens[(int)$item['id']] : ''; ?>
            <?php if ($isPdf) { $hasPdf = true; } ?>
            <?php $editModalId = 'edit-content-' . (int)$item['id']; ?>
            <?php $deleteModalId = 'delete-content-' . (int)$item['id']; ?>
            <div class="list-group-item py-3" data-series-title="<?= View::e((string)($series['name'] ?? '')) ?>" data-item-title="<?= View::e((string)($item['title'] ?? '')) ?>">
                <div class="d-flex justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <form method="post" action="<?= base_path('/libraries/favorite') ?>">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                            <?php $isFav = !empty($favorites) && in_array((int)$item['id'], $favorites, true); ?>
                            <input type="hidden" name="action" value="<?= $isFav ? 'remove' : 'add' ?>">
                            <button class="btn btn-sm <?= $isFav ? 'btn-warning' : 'btn-outline-warning' ?>" type="submit" aria-label="<?= $isFav ? 'Remover favorito' : 'Favoritar' ?>">
                                <?= $isFav ? '★' : '☆' ?>
                            </button>
                        </form>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <a href="<?= $isPdf ? ($iosOnlyDownload ? base_path('/download/' . (int)$item['id'] . '?token=' . urlencode($downloadToken)) : base_path('/download/' . (int)$item['id'] . '?inline=1&token=' . urlencode($downloadToken))) : ($isEpub ? base_path('/epub/' . (int)$item['id']) : base_path('/reader/' . (int)$item['id'])) ?>" <?= $isPdf && !$iosOnlyDownload ? 'data-open-pdf' : '' ?> <?= $isPdf && !$iosOnlyDownload ? 'data-url="' . base_path('/download/' . (int)$item['id'] . '?inline=1&token=' . urlencode($downloadToken)) . '"' : '' ?>>
                                <?= View::e(str_replace('_', ' ', (string)$item['title'])) ?>
                            </a>
                            <?php if ($isPdf): ?>
                                <span class="badge bg-warning text-dark ms-2">PDF</span>
                                <?php if ($iosOnlyDownload): ?>
                                    <span class="badge bg-secondary ms-1">Somente download no iOS</span>
                                <?php endif; ?>
                            <?php elseif ($isEpub): ?>
                                <span class="badge bg-info text-dark ms-2">EPUB</span>
                            <?php endif; ?>
                            <?php if (!empty($read) && in_array((int)$item['id'], $read, true)): ?>
                                <span class="badge bg-success ms-2">Lido</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <?php if ($isPdf && $downloadToken !== ''): ?>
                            <a class="btn btn-sm btn-outline-primary" href="<?= base_path('/download/' . (int)$item['id'] . '?token=' . urlencode($downloadToken)) ?>" title="Download">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        <?php endif; ?>
                        <form method="post" action="<?= base_path('/libraries/read') ?>">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                            <input type="hidden" name="read" value="<?= (!empty($read) && in_array((int)$item['id'], $read, true)) ? '0' : '1' ?>">
                            <?php $isRead = !empty($read) && in_array((int)$item['id'], $read, true); ?>
                            <button class="btn btn-sm btn-outline-secondary" type="submit" title="<?= $isRead ? 'Marcar não lido' : 'Marcar lido' ?>">
                                <i class="fa-solid <?= $isRead ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                            </button>
                        </form>
                        <?php if (!empty($user) && (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user))): ?>
                            <span class="text-muted">|</span>
                            <form method="post" action="<?= base_path('/libraries/content/order') ?>" class="d-flex align-items-center gap-1">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                <input class="form-control form-control-sm" type="number" name="content_order" value="<?= (int)($item['content_order'] ?? 0) ?>" style="width: 80px;" min="0">
                                <button class="btn btn-sm btn-outline-primary" type="submit">Salvar</button>
                            </form>
                            <button class="btn btn-sm btn-outline-secondary" type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#<?= $editModalId ?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" type="button" title="Excluir" data-bs-toggle="modal" data-bs-target="#<?= $deleteModalId ?>">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <div class="d-md-none">
        <?php foreach ($items as $item): ?>
            <?php $itemPath = (string)($item['cbz_path'] ?? ''); ?>
            <?php $itemExt = strtolower(pathinfo($itemPath, PATHINFO_EXTENSION)); ?>
            <?php $isPdf = $itemExt === 'pdf'; ?>
            <?php $isEpub = $itemExt === 'epub'; ?>
            <?php $iosOnlyDownload = !empty($isIos); ?>
            <?php $downloadToken = !empty($downloadTokens[(int)$item['id']]) ? (string)$downloadTokens[(int)$item['id']] : ''; ?>
            <?php if ($isPdf) { $hasPdf = true; } ?>
            <?php $editModalId = 'edit-content-' . (int)$item['id']; ?>
            <?php $deleteModalId = 'delete-content-' . (int)$item['id']; ?>
            <?php $isRead = !empty($read) && in_array((int)$item['id'], $read, true); ?>
            <div class="card mb-2 library-list-card" data-series-title="<?= View::e((string)($series['name'] ?? '')) ?>" data-item-title="<?= View::e((string)($item['title'] ?? '')) ?>">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2 library-card-row">
                        <div class="d-flex align-items-start gap-2">
                            <form method="post" action="<?= base_path('/libraries/favorite') ?>">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                <?php $isFav = !empty($favorites) && in_array((int)$item['id'], $favorites, true); ?>
                                <input type="hidden" name="action" value="<?= $isFav ? 'remove' : 'add' ?>">
                                <button class="btn btn-sm <?= $isFav ? 'btn-warning' : 'btn-outline-warning' ?>" type="submit" aria-label="<?= $isFav ? 'Remover favorito' : 'Favoritar' ?>">
                                    <?= $isFav ? '★' : '☆' ?>
                                </button>
                            </form>
                            <div>
                                <a class="text-decoration-none fw-semibold" href="<?= $isPdf ? ($iosOnlyDownload ? base_path('/download/' . (int)$item['id'] . '?token=' . urlencode($downloadToken)) : base_path('/download/' . (int)$item['id'] . '?inline=1&token=' . urlencode($downloadToken))) : ($isEpub ? base_path('/epub/' . (int)$item['id']) : base_path('/reader/' . (int)$item['id'])) ?>" <?= $isPdf && !$iosOnlyDownload ? 'data-open-pdf' : '' ?> <?= $isPdf && !$iosOnlyDownload ? 'data-url="' . base_path('/download/' . (int)$item['id'] . '?inline=1&token=' . urlencode($downloadToken)) . '"' : '' ?>>
                                    <?= View::e(str_replace('_', ' ', (string)$item['title'])) ?>
                                </a>
                                <div class="small text-muted">Clique para abrir</div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-1 mt-2 library-card-badges">
                        <?php if ($isPdf): ?>
                            <span class="badge bg-warning text-dark">PDF</span>
                            <?php if ($iosOnlyDownload): ?>
                                <span class="badge bg-secondary">Somente download no iOS</span>
                            <?php endif; ?>
                        <?php elseif ($isEpub): ?>
                            <span class="badge bg-info text-dark">EPUB</span>
                        <?php endif; ?>
                        <?php if ($isRead): ?>
                            <span class="badge bg-success">Lido</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pt-0">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <?php if ($isPdf && $downloadToken !== ''): ?>
                            <a class="btn btn-sm btn-outline-primary" href="<?= base_path('/download/' . (int)$item['id'] . '?token=' . urlencode($downloadToken)) ?>" title="Download">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        <?php endif; ?>
                        <form method="post" action="<?= base_path('/libraries/read') ?>">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                            <input type="hidden" name="read" value="<?= $isRead ? '0' : '1' ?>">
                            <button class="btn btn-sm btn-outline-secondary" type="submit" title="<?= $isRead ? 'Marcar não lido' : 'Marcar lido' ?>">
                                <i class="fa-solid <?= $isRead ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                            </button>
                        </form>
                        <?php if (!empty($user) && (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user))): ?>
                            <form method="post" action="<?= base_path('/libraries/content/order') ?>" class="d-flex align-items-center gap-1">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                <input class="form-control form-control-sm" type="number" name="content_order" value="<?= (int)($item['content_order'] ?? 0) ?>" style="width: 80px;" min="0">
                                <button class="btn btn-sm btn-outline-primary" type="submit">Salvar</button>
                            </form>
                            <button class="btn btn-sm btn-outline-secondary" type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#<?= $editModalId ?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" type="button" title="Excluir" data-bs-toggle="modal" data-bs-target="#<?= $deleteModalId ?>">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php if (!empty($user) && (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user))): ?>
            <?php foreach ($items as $item): ?>
                <?php $editModalId = 'edit-content-' . (int)$item['id']; ?>
                <?php $deleteModalId = 'delete-content-' . (int)$item['id']; ?>
                <div class="modal fade" id="<?= $editModalId ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar conteúdo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>
                            <form method="post" action="<?= base_path('/libraries/content/update') ?>">
                                <div class="modal-body">
                                    <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                    <label class="form-label" for="title-<?= (int)$item['id'] ?>">Título</label>
                                    <input class="form-control" id="title-<?= (int)$item['id'] ?>" type="text" name="title" value="<?= View::e((string)$item['title']) ?>" required>
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
                                <h5 class="modal-title">Excluir conteúdo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>
                            <div class="modal-body">
                                Tem certeza que deseja excluir o conteúdo <strong><?= View::e((string)$item['title']) ?></strong>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <form method="post" action="<?= base_path('/libraries/content/delete') ?>" class="m-0">
                                    <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                    <button class="btn btn-danger" type="submit">Confirmar exclusão</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
    <?php if ($hasPdf): ?>
        <div id="pdfViewerModal" class="pdf-viewer-modal">
                <div id="pdfViewerDialog" class="pdf-viewer-dialog">
                <div class="pdf-viewer-header">
                    <div class="pdf-viewer-title" id="pdfViewerTitle"></div>
                    <div class="pdf-viewer-actions">
                        <button id="pdfOpenBtn" class="btn btn-sm btn-outline-primary me-2" type="button">Expandir</button>
                        <button id="pdfCloseBtn" class="btn btn-sm btn-secondary" type="button">Fechar</button>
                    </div>
                </div>
                <div class="pdf-viewer-body">
                    <iframe id="pdfViewerFrame" src="" class="pdf-viewer-frame" allowfullscreen allow="fullscreen"></iframe>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!empty($pages) && $pages > 1): ?>
        <nav class="mt-3">
            <ul class="pagination">
                <?php for ($p = 1; $p <= $pages; $p++): ?>
                    <li class="page-item <?= ($p === (int)($page ?? 1)) ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $seriesBaseUrl . '?page=' . $p . (!empty($baseQueryString) ? '&' . ltrim($baseQueryString, '?') : '') ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
