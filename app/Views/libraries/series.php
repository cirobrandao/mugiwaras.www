<?php
use App\Core\View;
ob_start();
?>
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_path('/libraries') ?>">Bibliotecas</a></li>
        <li class="breadcrumb-item"><a href="<?= base_path('/libraries/' . rawurlencode((string)($category['name'] ?? ''))) ?>"><?= View::e((string)($category['name'] ?? '')) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= View::e((string)($series['name'] ?? '')) ?></li>
    </ol>
</nav>
<?php
$seriesBaseUrl = base_path('/libraries/' . rawurlencode((string)($category['name'] ?? '')) . '/' . rawurlencode((string)($series['name'] ?? '')));
$baseQuery = [];
if (!empty($format)) $baseQuery[] = 'format=' . urlencode((string)$format);
if (!empty($iosTest)) $baseQuery[] = 'ios_test=1';
$baseQueryString = empty($baseQuery) ? '' : '?' . implode('&', $baseQuery);
?>
<?php if (!empty($error)): ?>
    <div class="alert alert-warning"><?= View::e($error) ?></div>
<?php endif; ?>
<?php if (!empty($pending)): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h6 mb-2">Aguardando conversão</h2>
            <ul class="list-group">
                <?php foreach ($pending as $p): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><?= View::e((string)($p['title'] ?? $p['original_name'] ?? 'Arquivo')) ?></span>
                        <span class="badge bg-secondary">Em fila</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>
<?php if (empty($items)): ?>
    <div class="alert alert-secondary">Nenhum arquivo encontrado.</div>
<?php else: ?>
    <?php $hasPdf = false; ?>
    <div class="list-group">
        <?php foreach ($items as $item): ?>
            <?php $itemPath = (string)($item['cbz_path'] ?? ''); ?>
            <?php $itemExt = strtolower(pathinfo($itemPath, PATHINFO_EXTENSION)); ?>
            <?php $isPdf = $itemExt === 'pdf'; ?>
            <?php $iosOnlyDownload = !empty($isIos); ?>
            <?php $downloadToken = !empty($downloadTokens[(int)$item['id']]) ? (string)$downloadTokens[(int)$item['id']] : ''; ?>
            <?php if ($isPdf) { $hasPdf = true; } ?>
            <div class="list-group-item" data-series-title="<?= View::e((string)($series['name'] ?? '')) ?>" data-item-title="<?= View::e((string)($item['title'] ?? '')) ?>">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
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
                            <a href="<?= $isPdf ? ($iosOnlyDownload ? base_path('/download/' . (int)$item['id'] . '?token=' . urlencode($downloadToken)) : base_path('/download/' . (int)$item['id'] . '?inline=1&token=' . urlencode($downloadToken))) : base_path('/reader/' . (int)$item['id']) ?>" <?= $isPdf && !$iosOnlyDownload ? 'data-open-pdf' : '' ?> <?= $isPdf && !$iosOnlyDownload ? 'data-url="' . base_path('/download/' . (int)$item['id'] . '?inline=1&token=' . urlencode($downloadToken)) . '"' : '' ?>>
                                <?= View::e((string)$item['title']) ?>
                            </a>
                            <?php if ($isPdf): ?>
                                <span class="badge bg-warning text-dark ms-2">PDF</span>
                                <?php if ($iosOnlyDownload): ?>
                                    <span class="badge bg-secondary ms-1">Somente download no iOS</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (!empty($read) && in_array((int)$item['id'], $read, true)): ?>
                                <span class="badge bg-success ms-2">Lido</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <?php if (!$isPdf && !empty($progress) && isset($progress[(int)$item['id']]) && $progress[(int)$item['id']] > 0): ?>
                            <a class="btn btn-sm btn-outline-primary" href="<?= base_path('/reader/' . (int)$item['id'] . '?page=' . (int)$progress[(int)$item['id']]) ?>">Voltar à leitura</a>
                        <?php endif; ?>
                        <?php if ($isPdf): ?>
                            <a class="btn btn-sm btn-outline-primary" href="<?= base_path('/download/' . (int)$item['id'] . '?token=' . urlencode($downloadToken)) ?>" title="Baixar PDF">
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
                        <?php if (!empty($user) && in_array($user['role'] ?? 'none', ['superadmin','admin','moderator'], true)): ?>
                            <details>
                                <summary class="btn btn-sm btn-outline-secondary" title="Editar"><i class="fa-solid fa-pen-to-square"></i></summary>
                                <form method="post" action="<?= base_path('/libraries/content/update') ?>" class="mt-2">
                                    <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                    <input class="form-control form-control-sm mb-2" type="text" name="title" value="<?= View::e((string)$item['title']) ?>" required>
                                    <button class="btn btn-sm btn-primary" type="submit">Salvar</button>
                                </form>
                            </details>
                            <form method="post" action="<?= base_path('/libraries/content/delete') ?>" onsubmit="return confirm('Excluir arquivo?');">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
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
