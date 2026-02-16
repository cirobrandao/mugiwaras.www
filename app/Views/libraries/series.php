<?php
use App\Core\View;
ob_start();
$itemCount = is_array($items ?? null) ? count($items) : 0;
?>
<?php
$categorySlug = !empty($category['slug']) ? (string)$category['slug'] : \App\Models\Category::generateSlug((string)($category['name'] ?? ''));
$seriesBaseUrl = base_path('/lib/' . rawurlencode($categorySlug) . '/' . (int)($series['id'] ?? 0));
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
<div class="portal-container">
    <section class="series-content">
        <div class="series-header">
            <nav aria-label="breadcrumb" class="series-breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_path('/lib/' . rawurlencode($categorySlug)) ?>"><?= View::e((string)($category['name'] ?? '')) ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= View::e((string)($series['name'] ?? '')) ?>
                        <?php if (!empty($series['adult_only'])): ?>
                            <span class="badge bg-danger ms-2">18+</span>
                        <?php endif; ?>
                    </li>
                </ol>
            </nav>
            <div class="series-actions">
                <?php $bulkLabel = 'Acoes de leitura'; ?>
                <?php $bulkIcon = 'bi-eye'; ?>
                <?php $bulkModalId = 'bulk-read-modal-' . (int)($series['id'] ?? 0); ?>
                <button class="btn btn-sm btn-outline-secondary" type="button" title="<?= View::e($bulkLabel) ?>" aria-label="<?= View::e($bulkLabel) ?>" data-bs-toggle="modal" data-bs-target="#<?= $bulkModalId ?>">
                    <i class="bi <?= $bulkIcon ?>"></i>
                    <span class="d-none d-md-inline ms-1">Ações</span>
                </button>
                <a class="btn btn-sm btn-outline-secondary" href="<?= $orderUrl ?>" title="<?= View::e($orderBtnLabel) ?>" aria-label="<?= View::e($orderBtnLabel) ?>">
                    <i class="bi bi-sort-numeric-down"></i>
                    <span class="d-none d-md-inline ms-1"><?= View::e($orderLabel) ?></span>
                </a>
            </div>
        </div>
        <div class="modal fade" id="<?= $bulkModalId ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Acoes de leitura</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <form method="post" action="<?= base_path('/lib/series/read') ?>">
                        <div class="modal-body">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                            <input type="hidden" name="series_id" value="<?= (int)($series['id'] ?? 0) ?>">
                            <div class="mb-3">
                                <div class="fw-semibold mb-2">O que voce quer fazer?</div>
                                <div class="btn-group w-100" role="group" aria-label="Acao de leitura">
                                    <input type="radio" class="btn-check" name="action" id="bulk-action-read" value="read" checked>
                                    <label class="btn btn-outline-success" for="bulk-action-read">Marcar como lido</label>
                                    <input type="radio" class="btn-check" name="action" id="bulk-action-unread" value="unread">
                                    <label class="btn btn-outline-danger" for="bulk-action-unread">Marcar como nao lido</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="fw-semibold mb-2">Onde aplicar?</div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="scope" id="bulk-scope-all" value="all" checked>
                                    <label class="form-check-label" for="bulk-scope-all">Serie inteira</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="scope" id="bulk-scope-upto" value="upto">
                                    <label class="form-check-label" for="bulk-scope-upto">Ate o</label>
                                </div>
                                <input class="form-control" id="bulk-episode-order" type="number" name="episode_order" min="1" placeholder="Ex: 12">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button class="btn btn-primary" type="submit">Confirmar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="series-list">
        <?php foreach ($items as $item): ?>
            <?php $itemPath = (string)($item['cbz_path'] ?? ''); ?>
            <?php $itemExt = strtolower(pathinfo($itemPath, PATHINFO_EXTENSION)); ?>
            <?php $isPdf = $itemExt === 'pdf'; ?>
            <?php $isEpub = $itemExt === 'epub'; ?>
            <?php $downloadToken = !empty($downloadTokens[(int)$item['id']]) ? (string)$downloadTokens[(int)$item['id']] : ''; ?>
            <?php $pdfDownloadUrl = !empty($pdfDownloadUrls[(int)$item['id']]) ? (string)$pdfDownloadUrls[(int)$item['id']] : ''; ?>
            <?php if ($isPdf) { $hasPdf = true; } ?>
            <?php $editModalId = 'edit-content-' . (int)$item['id']; ?>
            <?php $deleteModalId = 'delete-content-' . (int)$item['id']; ?>
            <?php $isFav = !empty($favorites) && in_array((int)$item['id'], $favorites, true); ?>
            <?php $isRead = !empty($read) && in_array((int)$item['id'], $read, true); ?>
            <div class="series-item" data-series-title="<?= View::e((string)($series['name'] ?? '')) ?>" data-item-title="<?= View::e((string)($item['title'] ?? '')) ?>">
                <div class="series-item-content">
                    <div class="series-item-read-action">
                        <form method="post" action="<?= base_path('/lib/read') ?>" class="d-inline">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                            <input type="hidden" name="read" value="<?= $isRead ? '0' : '1' ?>">
                            <button class="btn btn-sm btn-outline-secondary" type="submit" title="<?= $isRead ? 'Marcar não lido' : 'Marcar lido' ?>">
                                <i class="bi <?= $isRead ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                            </button>
                        </form>
                    </div>
                    <div class="series-item-info">
                        <a class="series-item-title" href="<?= $isPdf ? base_path('/download/' . (int)$item['id'] . '?inline=1&token=' . urlencode($downloadToken)) : ($isEpub ? base_path('/epub/' . (int)$item['id']) : base_path('/reader/' . (int)$item['id'])) ?>" <?= $isPdf ? 'data-open-pdf' : '' ?> <?= $isPdf ? 'data-url="' . base_path('/download/' . (int)$item['id'] . '?inline=1&token=' . urlencode($downloadToken)) . '"' : '' ?> <?= $isPdf ? 'data-reader-url="' . base_path('/reader/pdf/' . (int)$item['id']) . '"' : '' ?>>
                            <?= View::e(str_replace('_', ' ', (string)$item['title'])) ?>
                            <?php if ($isRead): ?>
                                <span class="badge bg-success">Lido</span>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="series-item-actions">
                        <?php if ($isPdf): ?>
                            <span class="badge bg-warning text-dark">PDF</span>
                        <?php elseif ($isEpub): ?>
                            <span class="badge bg-info text-dark">EPUB</span>
                        <?php endif; ?>
                        <?php if ($pdfDownloadUrl !== ''): ?>
                            <a class="btn btn-sm btn-outline-primary" href="<?= $pdfDownloadUrl ?>" title="Download PDF">
                                <i class="bi bi-download"></i>
                            </a>
                        <?php elseif ($isPdf && $downloadToken !== ''): ?>
                            <a class="btn btn-sm btn-outline-primary" href="<?= base_path('/download/' . (int)$item['id'] . '?token=' . urlencode($downloadToken)) ?>" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($user) && (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user))): ?>
                            <div class="series-item-admin">
                                <form method="post" action="<?= base_path('/lib/content/order') ?>" class="d-inline-flex align-items-center gap-1">
                                    <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                    <input class="form-control form-control-sm" type="number" name="content_order" value="<?= (int)($item['content_order'] ?? 0) ?>" style="width: 70px;" min="0">
                                    <button class="btn btn-sm btn-outline-primary" type="submit"><i class="bi bi-check"></i></button>
                                </form>
                                <button class="btn btn-sm btn-outline-secondary" type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#<?= $editModalId ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" type="button" title="Excluir" data-bs-toggle="modal" data-bs-target="#<?= $deleteModalId ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
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
                            <form method="post" action="<?= base_path('/lib/content/update') ?>">
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
                                <form method="post" action="<?= base_path('/lib/content/delete') ?>" class="m-0">
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
</div>
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
        <?php
        // Preparar variáveis para a partial de paginação
        $currentPage = (int)($page ?? 1);
        $totalPages = (int)$pages;
        $baseUrl = $seriesBaseUrl;
        $queryParams = [];
        if (!empty($format)) $queryParams['format'] = (string)$format;
        if (!empty($iosTest)) $queryParams['ios_test'] = '1';
        if (!empty($order)) $queryParams['order'] = (string)$order;
        require __DIR__ . '/../partials/pagination.php';
        ?>
    <?php endif; ?>
<?php endif; ?>

<?php if (!empty($user) && (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user))): ?>
<script>
// Prevenir inicialização prematura dos modals
(function() {
    'use strict';
    
    // Aguardar o Bootstrap estar totalmente carregado
    if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
        console.warn('Bootstrap não carregado ainda');
        return;
    }
    
    // Aguardar o DOM estar pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initModals);
    } else {
        initModals();
    }
    
    function initModals() {
        // Corrigir possível problema de inicialização dos modals
        const triggerButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
        
        triggerButtons.forEach(function(btn) {
            const targetId = btn.getAttribute('data-bs-target');
            if (!targetId) return;
            
            const modalEl = document.querySelector(targetId);
            if (!modalEl || modalEl.id === 'pdfViewerModal') return;
            
            // Remover evento padrão e adicionar manualmente
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                try {
                    // Tentar obter instância existente ou criar nova
                    let modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalEl);
                    }
                    modalInstance.show();
                } catch (error) {
                    console.error('Erro ao abrir modal:', error);
                }
            });
        });
    }
})();
</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
