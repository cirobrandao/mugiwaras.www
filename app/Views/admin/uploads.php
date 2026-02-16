<?php
use App\Core\View;
ob_start();
?>
<?php
$categoryMap = [];
foreach (($categories ?? []) as $c) {
    $categoryMap[(int)$c['id']] = (string)$c['name'];
}

$shortFileName = static function (string $name): string {
    $name = trim($name);
    if ($name === '') {
        return '';
    }
    $pos = strrpos($name, '.');
    if ($pos === false || $pos === 0) {
        return mid_ellipsis($name, 32, 7);
    }
    $base = substr($name, 0, $pos);
    $ext = substr($name, $pos + 1);
    $extPart = $ext !== '' ? '.' . $ext : '';
    $max = 32;
    $tail = 6;
    $trimmedBase = mid_ellipsis($base, $max - mb_strlen($extPart), $tail);
    return $trimmedBase . $extPart;
};
?>
<div class="admin-uploads">
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">
            <i class="bi bi-cloud-arrow-up me-2"></i>Gerenciador de Arquivos
        </h1>
    </div>
    <?php if (!empty($total)): ?>
        <div class="d-flex gap-3">
            <div class="admin-uploads-stat">
                <i class="bi bi-files"></i>
                <div>
                    <div class="stat-value"><?= (int)$total ?></div>
                    <div class="stat-label">Uploads</div>
                </div>
            </div>
            <div class="admin-uploads-stat">
                <i class="bi bi-hdd"></i>
                <div>
                    <div class="stat-value"><?= isset($totalSize) ? number_format($totalSize / (1024*1024*1024), 2, ',', '.') : '0' ?></div>
                    <div class="stat-label">GB enviados</div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<form method="get" action="<?= base_path('/admin/uploads') ?>" class="row g-2 align-items-end mb-3">
    <div class="col-sm-4 col-md-3">
        <label class="form-label"><i class="bi bi-person me-1"></i>Usuário</label>
        <input class="form-control" type="text" name="user" value="<?= View::e((string)($filterUser ?? '')) ?>" placeholder="ID ou nome">
    </div>
    <div class="col-sm-4 col-md-3">
        <label class="form-label"><i class="bi bi-folder me-1"></i>Categoria</label>
        <select class="form-select" name="category">
            <option value="0">Todas</option>
            <?php foreach (($categories ?? []) as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= ((int)($filterCategory ?? 0) === (int)$c['id']) ? 'selected' : '' ?>>
                    <?= View::e((string)$c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-sm-4 col-md-3">
        <label class="form-label"><i class="bi bi-collection me-1"></i>Série</label>
        <select class="form-select" name="series">
            <option value="0">Todas</option>
            <?php foreach (($seriesByCategory ?? []) as $catId => $seriesList): ?>
                <?php $catLabel = $categoryMap[(int)$catId] ?? ('Categoria #' . (int)$catId); ?>
                <optgroup label="<?= View::e($catLabel) ?>">
                    <?php foreach ($seriesList as $s): ?>
                        <option value="<?= (int)$s['id'] ?>" <?= ((int)($filterSeries ?? 0) === (int)$s['id']) ? 'selected' : '' ?>>
                            <?= View::e((string)$s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-sm-4 col-md-2">
        <label class="form-label"><i class="bi bi-funnel me-1"></i>Status</label>
        <select class="form-select" name="status">
            <option value="">Todos</option>
            <option value="pending" <?= ($filterStatus ?? '') === 'pending' ? 'selected' : '' ?>>Pendentes</option>
            <option value="queued" <?= ($filterStatus ?? '') === 'queued' ? 'selected' : '' ?>>Na fila</option>
            <option value="processing" <?= ($filterStatus ?? '') === 'processing' ? 'selected' : '' ?>>Processando</option>
            <option value="done" <?= ($filterStatus ?? '') === 'done' ? 'selected' : '' ?>>Liberado</option>
            <option value="failed" <?= ($filterStatus ?? '') === 'failed' ? 'selected' : '' ?>>Falhou</option>
        </select>
    </div>
    <div class="col-sm-12 col-md-1 d-flex gap-1">
        <input type="hidden" name="perPage" value="<?= (int)($perPage ?? 50) ?>">
        <button class="btn btn-primary" type="submit" title="Filtrar"><i class="bi bi-search"></i></button>
        <a class="btn btn-outline-secondary" href="<?= base_path('/admin/uploads') ?>" title="Limpar"><i class="bi bi-x-circle"></i></a>
    </div>
</form>
<div class="d-flex gap-2 justify-content-end mb-2">
    <form id="bulkApproveForm" method="post" action="<?= base_path('/admin/uploads/approve-multiple') ?>" class="m-0">
    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
    <input type="hidden" name="page" value="<?= (int)($page ?? 1) ?>">
    <input type="hidden" name="perPage" value="<?= (int)($perPage ?? 50) ?>">
    <input type="hidden" name="user" value="<?= View::e((string)($filterUser ?? '')) ?>">
    <input type="hidden" name="category" value="<?= (int)($filterCategory ?? 0) ?>">
    <input type="hidden" name="series" value="<?= (int)($filterSeries ?? 0) ?>">
    <input type="hidden" name="status" value="<?= View::e((string)($filterStatus ?? '')) ?>">
        <button class="btn btn-success" type="submit" id="bulkApproveBtn" title="Liberar pendentes selecionados">
            <i class="bi bi-check2-all me-1"></i>Liberar selecionados
        </button>
    </form>
    <form id="bulkDeleteForm" method="post" action="<?= base_path('/admin/uploads/delete-multiple') ?>" class="m-0">
        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
        <input type="hidden" name="page" value="<?= (int)($page ?? 1) ?>">
        <input type="hidden" name="perPage" value="<?= (int)($perPage ?? 50) ?>">
        <input type="hidden" name="user" value="<?= View::e((string)($filterUser ?? '')) ?>">
        <input type="hidden" name="category" value="<?= (int)($filterCategory ?? 0) ?>">
        <input type="hidden" name="series" value="<?= (int)($filterSeries ?? 0) ?>">
        <input type="hidden" name="status" value="<?= View::e((string)($filterStatus ?? '')) ?>">
        <button class="btn btn-danger" type="submit" id="bulkDeleteBtn" title="Remover selecionados">
            <i class="bi bi-trash me-1"></i>Remover selecionados
        </button>
    </form>
</div>
<div class="admin-uploads-table">
    <table class="table table-hover align-middle mb-0" style="table-layout: fixed;">
        <thead>
        <tr>
            <th scope="col" style="width: 32px;">
                <input class="form-check-input" type="checkbox" id="selectAllBulk" aria-label="Selecionar todos" title="Selecionar todos">
            </th>
            <th scope="col" style="width: 40px;"></th>
            <th scope="col" style="width: 220px;"><i class="bi bi-file-earmark me-1"></i>Arquivo</th>
            <th scope="col" style="width: 160px;"><i class="bi bi-folder me-1"></i>Categoria</th>
            <th scope="col" style="width: 200px;"><i class="bi bi-collection me-1"></i>Série</th>
            <th scope="col" style="width: 150px;"><i class="bi bi-calendar me-1"></i>Data</th>
            <th scope="col" style="width: 140px;"><i class="bi bi-person me-1"></i>Usuário</th>
            <th scope="col" class="text-end" style="width: 160px;"><i class="bi bi-gear me-1"></i>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php $modals = []; ?>
        <?php foreach (($uploads ?? []) as $u): ?>
            <?php
            $st = (string)($u['status'] ?? '');
            $jobStatus = (string)($u['job_status'] ?? '');
            if ($jobStatus === 'failed') {
                $st = 'failed';
            }
            $createdAtRaw = (string)($u['created_at'] ?? '');
            $createdAtTs = $createdAtRaw !== '' ? strtotime($createdAtRaw) : false;
            $isStaleQueue = ($st === 'queued' || $st === 'pending') && $createdAtTs !== false && (time() - $createdAtTs) >= (3 * 86400);
            $isFailed = $st === 'failed';
            $fileName = (string)($u['original_name'] ?? '');
            $fileLabel = $shortFileName($fileName);
            ?>
            <tr data-select-row class="<?= $isStaleQueue ? 'table-warning' : '' ?>">
                <td>
                    <input class="form-check-input bulk-select-checkbox" type="checkbox" name="ids[]" value="<?= (int)$u['id'] ?>" data-label="<?= View::e($fileLabel) ?>" aria-label="Selecionar upload" form="bulkDeleteForm">
                    <?php if (($u['status'] ?? '') === 'pending'): ?>
                        <input class="form-check-input bulk-pending-checkbox" type="checkbox" name="ids[]" value="<?= (int)$u['id'] ?>" data-label="<?= View::e($fileLabel) ?>" aria-label="Selecionar pendente" form="bulkApproveForm">
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                    $icon = 'bi-circle-fill'; $cls = 'text-secondary';
                    if ($st === 'queued' || $st === 'pending') { $icon = 'bi-clock'; $cls = 'text-muted'; }
                    elseif ($st === 'processing') { $icon = 'bi-arrow-repeat'; $cls = 'text-primary'; }
                    elseif ($st === 'done' || $st === 'completed') { $icon = 'bi-check-circle-fill'; $cls = 'text-success'; }
                    elseif ($st === 'failed') { $icon = 'bi-exclamation-triangle-fill'; $cls = 'text-danger'; }
                    if ($isStaleQueue) { $icon = 'bi-exclamation-triangle-fill'; $cls = 'text-warning'; }
                    ?>
                    <i class="bi <?= $icon ?> <?= $cls ?>" title="<?= View::e($isStaleQueue ? ($st . ' (3+ dias)') : $st) ?>"></i>
                </td>
                <td>
                    <span class="badge bg-light text-dark" title="<?= View::e($fileName) ?>">
                        <?= View::e($fileLabel) ?>
                    </span>
                </td>
                <td>
                    <?php
                    $catName = (string)($u['category_name'] ?? '');
                    $catId = (int)($u['category_id'] ?? 0);
                    $catLabel = $catName !== '' ? $catName : ($catId > 0 ? ('#' . $catId) : '—');
                    ?>
                    <span class="text-truncate d-inline-block" style="max-width: 160px;" title="<?= View::e($catLabel) ?>"><?= View::e($catLabel) ?></span>
                </td>
                <td>
                    <?php
                    $seriesName = (string)($u['series_name'] ?? '');
                    $categoryNameForSeries = (string)($u['category_name'] ?? '');
                    $seriesId = (int)($u['series_id'] ?? 0);
                    $seriesLabel = $seriesName !== '' ? $seriesName : ($seriesId > 0 ? ('#' . $seriesId) : '—');
                    $categorySlugForSeries = $categoryNameForSeries !== '' ? \App\Models\Category::generateSlug($categoryNameForSeries) : '';
                    $seriesUrl = ($seriesId > 0 && $categorySlugForSeries !== '')
                        ? base_path('/lib/' . rawurlencode($categorySlugForSeries) . '/' . $seriesId)
                        : '';
                    ?>
                    <div class="d-inline-flex align-items-center gap-1">
                        <span class="badge bg-secondary" title="<?= View::e($seriesLabel) ?>">
                            <?= View::e(mid_ellipsis($seriesLabel, 28, 6)) ?>
                        </span>
                        <?php if ($seriesUrl !== ''): ?>
                            <a class="btn btn-sm btn-outline-secondary" href="<?= View::e($seriesUrl) ?>" target="_blank" rel="noopener" title="Abrir série">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="text-nowrap" style="width: 150px;">
                    <?= View::e((string)$u['created_at']) ?>
                    <?php if ($isStaleQueue): ?>
                        <div><span class="badge bg-warning text-dark">3+ dias na fila</span></div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php $userLabel = $u['username_display'] ?? ('#' . (int)$u['user_id']); ?>
                    <span class="text-truncate d-inline-block" style="max-width: 140px;" title="<?= View::e((string)$userLabel) ?>"><?= View::e((string)$userLabel) ?></span>
                </td>
                <td class="text-end">
                    <?php if (($u['status'] ?? '') === 'pending'): ?>
                        <form method="post" action="<?= base_path('/admin/uploads/approve') ?>" class="d-inline">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                            <input type="hidden" name="page" value="<?= (int)($page ?? 1) ?>">
                            <input type="hidden" name="perPage" value="<?= (int)($perPage ?? 50) ?>">
                            <input type="hidden" name="user" value="<?= View::e((string)($filterUser ?? '')) ?>">
                            <input type="hidden" name="category" value="<?= (int)($filterCategory ?? 0) ?>">
                            <input type="hidden" name="series" value="<?= (int)($filterSeries ?? 0) ?>">
                            <input type="hidden" name="status" value="<?= View::e((string)($filterStatus ?? '')) ?>">
                            <button class="btn btn-sm btn-success me-1" type="submit" title="Aprovar">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-secondary me-1" type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#editUploadModal<?= (int)$u['id'] ?>">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" type="button" title="Excluir" data-bs-toggle="modal" data-bs-target="#deleteUploadModal<?= (int)$u['id'] ?>">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            <?php
            ob_start();
            ?>
            <div class="modal fade admin-uploads-modal" id="editUploadModal<?= (int)$u['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-gradient text-white">
                            <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar categoria/série</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <form method="post" action="<?= base_path('/admin/uploads/update') ?>">
                            <div class="modal-body">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                <input type="hidden" name="page" value="<?= (int)($page ?? 1) ?>">
                                <input type="hidden" name="perPage" value="<?= (int)($perPage ?? 50) ?>">
                                <input type="hidden" name="user" value="<?= View::e((string)($filterUser ?? '')) ?>">
                                <input type="hidden" name="category" value="<?= (int)($filterCategory ?? 0) ?>">
                                <input type="hidden" name="series" value="<?= (int)($filterSeries ?? 0) ?>">
                                <input type="hidden" name="status" value="<?= View::e((string)($filterStatus ?? '')) ?>">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label"><i class="bi bi-folder me-1"></i>Categoria</label>
                                        <select class="form-select" name="category_id">
                                            <option value="0">Sem categoria</option>
                                            <?php foreach (($categories ?? []) as $c): ?>
                                                <option value="<?= (int)$c['id'] ?>" <?= ((int)($u['category_id'] ?? 0) === (int)$c['id']) ? 'selected' : '' ?>>
                                                    <?= View::e((string)$c['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label"><i class="bi bi-collection me-1"></i>Série</label>
                                        <select class="form-select" name="series_id">
                                            <option value="0">Sem série</option>
                                            <?php foreach (($seriesByCategory ?? []) as $catId => $seriesList): ?>
                                                <?php $catLabel = $categoryMap[(int)$catId] ?? ('Categoria #' . (int)$catId); ?>
                                                <optgroup label="<?= View::e($catLabel) ?>">
                                                    <?php foreach ($seriesList as $s): ?>
                                                        <option value="<?= (int)$s['id'] ?>" <?= ((int)($u['series_id'] ?? 0) === (int)$s['id']) ? 'selected' : '' ?>>
                                                            <?= View::e((string)$s['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label"><i class="bi bi-plus-circle me-1"></i>Nova série (opcional)</label>
                                        <input class="form-control" type="text" name="series_new" placeholder="Digite para criar nova série">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Cancelar</button>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade admin-uploads-modal" id="deleteUploadModal<?= (int)$u['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-gradient text-white">
                            <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Confirmar exclusão</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-2">Tem certeza que deseja excluir este arquivo?</p>
                            <div class="small text-muted">
                                <strong>Arquivo:</strong> <?= View::e((string)($u['original_name'] ?? '')) ?>
                                <span class="ms-2">#<?= (int)$u['id'] ?></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Cancelar</button>
                            <form method="post" action="<?= base_path('/admin/uploads/delete') ?>">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                <input type="hidden" name="confirm" value="1">
                                <input type="hidden" name="page" value="<?= (int)($page ?? 1) ?>">
                                <input type="hidden" name="perPage" value="<?= (int)($perPage ?? 50) ?>">
                                <input type="hidden" name="user" value="<?= View::e((string)($filterUser ?? '')) ?>">
                                <input type="hidden" name="category" value="<?= (int)($filterCategory ?? 0) ?>">
                                <input type="hidden" name="series" value="<?= (int)($filterSeries ?? 0) ?>">
                                <input type="hidden" name="status" value="<?= View::e((string)($filterStatus ?? '')) ?>">
                                <button class="btn btn-danger" type="submit"><i class="bi bi-trash me-1"></i>Excluir</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $modals[] = ob_get_clean();
            ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php if (!empty($modals)): ?>
    <?php foreach ($modals as $m): ?>
        <?= $m ?>
    <?php endforeach; ?>
<?php endif; ?>
<div class="modal fade admin-uploads-modal" id="bulkActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient text-white">
                <h5 class="modal-title" id="bulkActionTitle"><i class="bi bi-list-check me-2"></i>Confirmar ação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div id="bulkActionMessage" class="mb-2"></div>
                <ul class="list-group list-group-flush" id="bulkActionList"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Cancelar</button>
                <button type="button" class="btn btn-primary" id="bulkActionConfirm"><i class="bi bi-check-circle me-1"></i>Confirmar</button>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($total)): ?>
    <?php
    $pages = (int)ceil($total / ($perPage ?? 50));
    $curr = (int)($page ?? 1);
    $curr = $curr < 1 ? 1 : $curr;
    $start = max(1, $curr - 2);
    $end = min($pages, $curr + 2);
    $base = '/admin/uploads?perPage=' . (int)($perPage ?? 50)
        . '&user=' . urlencode((string)($filterUser ?? ''))
        . '&category=' . (int)($filterCategory ?? 0)
        . '&series=' . (int)($filterSeries ?? 0)
        . '&status=' . urlencode((string)($filterStatus ?? ''))
        . '&page=';
    ?>
    <nav aria-label="pag" class="mt-3">
        <ul class="pagination mb-0 justify-content-end">
            <li class="page-item <?= $curr <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= base_path($base . '1') ?>" aria-label="Primeira"><i class="bi bi-chevron-double-left"></i></a>
            </li>
            <li class="page-item <?= $curr <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= base_path($base . ($curr - 1)) ?>" aria-label="Anterior"><i class="bi bi-chevron-left"></i></a>
            </li>
            <?php if ($start > 1): ?>
                <li class="page-item"><a class="page-link" href="<?= base_path($base . '1') ?>">1</a></li>
                <?php if ($start > 2): ?>
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                <?php endif; ?>
            <?php endif; ?>
            <?php for ($p = $start; $p <= $end; $p++): ?>
                <li class="page-item <?= ($p === $curr) ? 'active' : '' ?>"><a class="page-link" href="<?= base_path($base . $p) ?>"><?= $p ?></a></li>
            <?php endfor; ?>
            <?php if ($end < $pages): ?>
                <?php if ($end < $pages - 1): ?>
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                <?php endif; ?>
                <li class="page-item"><a class="page-link" href="<?= base_path($base . $pages) ?>"><?= $pages ?></a></li>
            <?php endif; ?>
            <li class="page-item <?= $curr >= $pages ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= base_path($base . ($curr + 1)) ?>" aria-label="Próxima"><i class="bi bi-chevron-right"></i></a>
            </li>
            <li class="page-item <?= $curr >= $pages ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= base_path($base . $pages) ?>" aria-label="Última"><i class="bi bi-chevron-double-right"></i></a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
</div>
<script>
    (function () {
    })();
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
