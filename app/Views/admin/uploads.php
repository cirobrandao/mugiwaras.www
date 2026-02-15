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
<div class="uploads-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0 fw-bold">Gerenciador de Arquivos</h1>
            <p class="text-muted mb-0">Controle de uploads e aprovações</p>
        </div>
        <?php if (!empty($total)): ?>
            <div class="uploads-stats">
                <div class="stat-item">
                    <i class="bi bi-file-earmark-arrow-up text-primary"></i>
                    <div>
                        <div class="stat-value"><?= (int)$total ?></div>
                        <div class="stat-label">Uploads</div>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="bi bi-hdd text-success"></i>
                    <div>
                        <div class="stat-value"><?= isset($totalSize) ? number_format($totalSize / (1024*1024*1024), 2, ',', '.') : '0' ?></div>
                        <div class="stat-label">GB enviados</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<form method="get" action="<?= base_path('/admin/uploads') ?>" class="row g-2 align-items-end mb-2 uploads-filter-form">
    <div class="col-sm-4 col-md-3">
        <label class="form-label">Usuário (ID ou nome)</label>
        <input class="form-control" type="text" name="user" value="<?= View::e((string)($filterUser ?? '')) ?>" placeholder="ex: 42 ou joao">
    </div>
    <div class="col-sm-4 col-md-3">
        <label class="form-label">Categoria</label>
        <select class="form-select" name="category">
            <option value="0">Todas</option>
            <?php foreach (($categories ?? []) as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= ((int)($filterCategory ?? 0) === (int)$c['id']) ? 'selected' : '' ?>>
                    <?= View::e((string)$c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-sm-4 col-md-4">
        <label class="form-label">Série</label>
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
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
            <option value="">Todos</option>
            <option value="pending" <?= ($filterStatus ?? '') === 'pending' ? 'selected' : '' ?>>Pendentes</option>
            <option value="queued" <?= ($filterStatus ?? '') === 'queued' ? 'selected' : '' ?>>Na fila</option>
            <option value="processing" <?= ($filterStatus ?? '') === 'processing' ? 'selected' : '' ?>>Processando</option>
            <option value="done" <?= ($filterStatus ?? '') === 'done' ? 'selected' : '' ?>>Liberado</option>
            <option value="failed" <?= ($filterStatus ?? '') === 'failed' ? 'selected' : '' ?>>Falhou</option>
        </select>
    </div>
    <div class="col-sm-12 col-md-2 d-flex gap-2">
        <input type="hidden" name="perPage" value="<?= (int)($perPage ?? 50) ?>">
        <button class="btn btn-sm btn-primary" type="submit">Filtrar</button>
        <a class="btn btn-sm btn-outline-secondary" href="<?= base_path('/admin/uploads') ?>">Limpar</a>
    </div>
</form>
<div class="d-flex flex-wrap justify-content-end uploads-bulk-actions">
    <form id="bulkApproveForm" method="post" action="<?= base_path('/admin/uploads/approve-multiple') ?>" class="m-0">
    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
    <input type="hidden" name="page" value="<?= (int)($page ?? 1) ?>">
    <input type="hidden" name="perPage" value="<?= (int)($perPage ?? 50) ?>">
    <input type="hidden" name="user" value="<?= View::e((string)($filterUser ?? '')) ?>">
    <input type="hidden" name="category" value="<?= (int)($filterCategory ?? 0) ?>">
    <input type="hidden" name="series" value="<?= (int)($filterSeries ?? 0) ?>">
    <input type="hidden" name="status" value="<?= View::e((string)($filterStatus ?? '')) ?>">
        <button class="btn btn-sm btn-outline-success" type="submit" id="bulkApproveBtn" title="Liberar pendentes selecionados">
            <i class="bi bi-check2-all"></i>
            <span class="ms-1">Liberar pendentes selecionados</span>
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
        <button class="btn btn-sm btn-outline-danger" type="submit" id="bulkDeleteBtn" title="Remover selecionados">
            <i class="bi bi-trash"></i>
            <span class="ms-1">Remover selecionados</span>
        </button>
    </form>
</div>
<div class="table-responsive">
    <table class="table table-hover align-middle uploads-table-compact" style="table-layout: fixed;">
        <thead class="table-light">
        <tr>
            <th scope="col" style="width: 32px;">
                <input class="form-check-input" type="checkbox" id="selectAllBulk" aria-label="Selecionar todos" title="Selecionar todos">
            </th>
            <th scope="col" style="width: 40px;"></th>
            <th scope="col" style="width: 220px;">Arquivo</th>
            <th scope="col" style="width: 160px;">Categoria</th>
            <th scope="col" style="width: 200px;">Série</th>
            <th scope="col" style="width: 150px;">Data</th>
            <th scope="col" style="width: 140px;">Usuário</th>
            <th scope="col" class="text-end" style="width: 140px;">Ações</th>
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
                    <span class="uploads-pill uploads-pill-file" title="<?= View::e($fileName) ?>">
                        <?= View::e($fileLabel) ?>
                    </span>
                </td>
                <td>
                    <?php
                    $catName = (string)($u['category_name'] ?? '');
                    $catId = (int)($u['category_id'] ?? 0);
                    $catLabel = $catName !== '' ? $catName : ($catId > 0 ? ('#' . $catId) : '—');
                    ?>
                    <span class="uploads-truncate" title="<?= View::e($catLabel) ?>"><?= View::e($catLabel) ?></span>
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
                    <div class="d-inline-flex align-items-center gap-1 uploads-series-wrap">
                        <span class="uploads-pill uploads-pill-series" title="<?= View::e($seriesLabel) ?>">
                            <?= View::e(mid_ellipsis($seriesLabel, 28, 6)) ?>
                        </span>
                        <?php if ($seriesUrl !== ''): ?>
                            <a class="btn btn-sm btn-outline-secondary uploads-action-btn" href="<?= View::e($seriesUrl) ?>" target="_blank" rel="noopener" title="Abrir série em nova aba">
                                <i class="bi bi-box-arrow-up-right"></i>
                                <span class="visually-hidden">Abrir série</span>
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
                    <span class="uploads-truncate" title="<?= View::e((string)$userLabel) ?>"><?= View::e((string)$userLabel) ?></span>
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
                            <button class="btn btn-sm btn-outline-success me-1 uploads-action-btn" type="submit" title="Aprovar">
                                <i class="bi bi-check-lg"></i>
                                <span class="visually-hidden">Aprovar</span>
                            </button>
                        </form>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-outline-secondary me-1 uploads-action-btn" type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#editUploadModal<?= (int)$u['id'] ?>">
                        <i class="bi bi-pencil-square"></i>
                        <span class="visually-hidden">Editar</span>
                    </button>
                    <button class="btn btn-sm btn-outline-danger uploads-action-btn" type="button" title="Excluir" data-bs-toggle="modal" data-bs-target="#deleteUploadModal<?= (int)$u['id'] ?>">
                        <i class="bi bi-trash"></i>
                        <span class="visually-hidden">Excluir</span>
                    </button>
                </td>
            </tr>
            <?php
            ob_start();
            ?>
            <div class="modal fade" id="editUploadModal<?= (int)$u['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar categoria/série</h5>
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
                                        <label class="form-label">Categoria</label>
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
                                        <label class="form-label">Série</label>
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
                                        <label class="form-label">Nova série (opcional)</label>
                                        <input class="form-control" type="text" name="series_new" placeholder="Digite para criar nova série">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="deleteUploadModal<?= (int)$u['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmar exclusão</h5>
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
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
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
                                <button class="btn btn-danger" type="submit">Excluir</button>
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
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionTitle">Confirmar acao</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div id="bulkActionMessage" class="mb-2"></div>
                <ul class="list-group list-group-flush" id="bulkActionList"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="bulkActionConfirm">Confirmar</button>
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
    <nav aria-label="pag" class="mb-3">
        <ul class="pagination pagination-sm mb-0 justify-content-end">
            <li class="page-item <?= $curr <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= base_path($base . '1') ?>" aria-label="Primeira">«</a>
            </li>
            <li class="page-item <?= $curr <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= base_path($base . ($curr - 1)) ?>" aria-label="Anterior">‹</a>
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
                <a class="page-link" href="<?= base_path($base . ($curr + 1)) ?>" aria-label="Próxima">›</a>
            </li>
            <li class="page-item <?= $curr >= $pages ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= base_path($base . $pages) ?>" aria-label="Última">»</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
<script>
    (function () {
    })();
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
