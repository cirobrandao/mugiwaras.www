<?php
ob_start();
?>
<!--<h1 class="h4 mb-3">Upload</h1>-->
<?php if (!empty($_GET['ok']) || !empty($_GET['queued']) || !empty($_GET['failed'])): ?>
    <div class="alert alert-info">
        Enviados: <?= (int)($_GET['ok'] ?? 0) ?>,
        Pendentes de aprovação: <?= (int)($_GET['queued'] ?? 0) ?>,
        Falhas: <?= (int)($_GET['failed'] ?? 0) ?>.
    </div>
<?php elseif (!empty($_GET['error'])): ?>
    <?php if ($_GET['error'] === 'limit'): ?>
        <div class="alert alert-danger">O tamanho total do envio excede 200 MB.</div>
    <?php elseif ($_GET['error'] === 'category'): ?>
        <div class="alert alert-danger">Selecione uma categoria válida.</div>
    <?php elseif ($_GET['error'] === 'files'): ?>
        <div class="alert alert-danger">Máximo de 50 arquivos por envio.</div>
    <?php elseif ($_GET['error'] === 'series'): ?>
        <div class="alert alert-danger">Série é obrigatória.</div>
    <?php elseif ($_GET['error'] === 'csrf'): ?>
        <div class="alert alert-danger">Sessão expirada. Recarregue a página e tente novamente.</div>
    <?php elseif ($_GET['error'] === 'setup'): ?>
        <div class="alert alert-danger">Biblioteca não inicializada. Execute a migração 009_library_series.sql.</div>
    <?php else: ?>
        <div class="alert alert-danger">Falha no upload. Verifique o arquivo.</div>
    <?php endif; ?>
<?php endif; ?>
<?php if (!empty($setupError)): ?>
    <div class="alert alert-danger">Biblioteca não inicializada. Execute a migração 009_library_series.sql.</div>
<?php endif; ?>
<?php if (!empty($noCategories)): ?>
    <div class="alert alert-warning">Nenhuma categoria cadastrada. Crie uma categoria no painel administrativo.</div>
<?php endif; ?>
<?php
$uploadBypassUrl = upload_url('/upload-admin/login');
?>
<div id="uploadResult"></div>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <h1 class="h4 mb-1">Enviar arquivos</h1>
    </div>
</div>
<section class="section-card mb-3 upload-card">
    <div class="mb-3">
        <div class="progress" style="height: 6px;">
            <div class="progress-bar" id="limitBar" role="progressbar" style="width: 0%"></div>
        </div>
        <div class="small text-muted mt-1" id="limitInfo" data-max-bytes="209715200" data-max-files="100">0 B / 200 MB · 0 / 100 arquivos</div>
        <div class="small mt-2">
            Para arquivos acima de 200 MB, use o painel de bypass: <a href="<?= \App\Core\View::e($uploadBypassUrl) ?>">Upload Admin</a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-7">
            <form method="post" action="<?= upload_url('/upload') ?>" enctype="multipart/form-data">
        <input type="hidden" name="_csrf" value="<?= \App\Core\View::e($csrf ?? '') ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Categoria</label>
                <select name="category" class="form-select" required <?= !empty($noCategories) ? 'disabled' : '' ?>>
                    <option value="" selected disabled>Selecione uma categoria</option>
                    <?php foreach (($categories ?? []) as $cat): ?>
                        <option value="<?= \App\Core\View::e((string)$cat['name']) ?>"><?= \App\Core\View::e((string)$cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Série</label>
                <input type="text" name="series" class="form-control" placeholder="Ex: One Piece" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Arquivos</label>
            <input type="file" name="file[]" class="form-control" multiple required data-max-bytes="209715200" data-max-files="100">
            <div class="form-text">Formatos aceitos: *.epub, *.cbr, *.cbz, *.zip (imagens).</div>
            <div class="form-text">PDF: sem leitor, apenas download (aparece sinalizado na biblioteca).</div>
        </div>
        <div class="mb-3 d-none" id="uploadProgressWrap">
            <label class="form-label">Progresso</label>
            <div class="progress">
                <div class="progress-bar" id="uploadBar" role="progressbar" style="width: 0%">0%</div>
            </div>
            <div class="small text-muted mt-2 d-none" id="uploadWait">Aguarde... finalizando o upload.</div>
        </div>
            <button class="btn btn-primary" type="submit" id="uploadSubmit" <?= !empty($noCategories) ? 'disabled' : '' ?>>Enviar</button>
            </form>
        </div>
        <div class="col-lg-5">
            <div class="upload-log-box" id="uploadLogBox">
                <div class="fw-semibold mb-2">Log de envios</div>
                <div id="uploadLog" class="small text-muted"></div>
            </div>
        </div>
    </div>
</section>
<hr class="my-4">

<?php
$formatBytes = function (int $bytes): string {
    if ($bytes >= 1024 ** 3) return number_format($bytes / (1024 ** 3), 2) . ' GB';
    if ($bytes >= 1024 ** 2) return number_format($bytes / (1024 ** 2), 2) . ' MB';
    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
};

$statusBadgeMap = [
    'pending' => 'bg-warning text-dark',
    'queued' => 'bg-secondary',
    'processing' => 'bg-info text-dark',
    'done' => 'bg-success',
    'completed' => 'bg-success',
    'failed' => 'bg-danger',
];
?>

<div id="uploadHistory">
    <h2 class="h5 mb-2">Histórico de envios</h2>
    <?php if (!empty($pendingCount)): ?>
        <div class="alert alert-warning">
            Arquivos pendentes de aprovação: <?= (int)$pendingCount ?>.
        </div>
    <?php endif; ?>
    <div class="small text-muted mb-3">
        Total de arquivos: <?= (int)($totalUploads ?? 0) ?> · Total enviado: <?= $formatBytes((int)($totalSize ?? 0)) ?>
    </div>

    <?php if (empty($uploads)): ?>
        <div class="alert alert-secondary">Nenhum upload encontrado.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th scope="col">Data</th>
                    <th scope="col">Arquivo</th>
                    <th scope="col" style="width: 190px;">Status</th>
                    <th scope="col" class="text-end" style="width: 120px;">Tamanho</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach (($uploads ?? []) as $u): ?>
                    <tr>
                        <td><?= \App\Core\View::e((string)($u['created_at'] ?? '')) ?></td>
                        <td><?= \App\Core\View::e((string)($u['original_name'] ?? $u['title'] ?? '')) ?></td>
                        <td>
                            <?php
                            $st = (string)($u['status'] ?? '');
                            $label = match ($st) {
                                'pending' => 'Pendente de aprovação',
                                'queued' => 'Na fila de conversão',
                                'processing' => 'Processando',
                                'done', 'completed' => 'Liberado',
                                'failed' => 'Falhou',
                                default => $st,
                            };
                            $badgeClass = $statusBadgeMap[$st] ?? 'bg-secondary';
                            ?>
                            <span class="badge <?= \App\Core\View::e($badgeClass) ?>">
                                <?= \App\Core\View::e($label) ?>
                            </span>
                        </td>
                        <td class="text-end"><?= $formatBytes((int)($u['file_size'] ?? 0)) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($pages) && $pages > 1): ?>
            <?php
            $curr = (int)($page ?? 1);
            $curr = $curr < 1 ? 1 : $curr;
            $pages = (int)$pages;
            $start = max(1, $curr - 2);
            $end = min($pages, $curr + 2);
            $prev = max(1, $curr - 1);
            $next = min($pages, $curr + 1);
            ?>
            <nav>
                <ul class="pagination pagination-sm">
                    <li class="page-item <?= $curr <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= upload_url('/upload?page=1') ?>" aria-label="Primeira">«</a>
                    </li>
                    <li class="page-item <?= $curr <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= upload_url('/upload?page=' . $prev) ?>" aria-label="Anterior">‹</a>
                    </li>
                    <?php if ($start > 1): ?>
                        <li class="page-item"><a class="page-link" href="<?= upload_url('/upload?page=1') ?>">1</a></li>
                        <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                    <?php endif; ?>
                    <?php for ($p = $start; $p <= $end; $p++): ?>
                        <li class="page-item <?= $p === $curr ? 'active' : '' ?>">
                            <a class="page-link" href="<?= upload_url('/upload?page=' . $p) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($end < $pages): ?>
                        <?php if ($end < $pages - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                        <li class="page-item"><a class="page-link" href="<?= upload_url('/upload?page=' . $pages) ?>"><?= $pages ?></a></li>
                    <?php endif; ?>
                    <li class="page-item <?= $curr >= $pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= upload_url('/upload?page=' . $next) ?>" aria-label="Próxima">›</a>
                    </li>
                    <li class="page-item <?= $curr >= $pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= upload_url('/upload?page=' . $pages) ?>" aria-label="Última">»</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
<script src="<?= base_path('/assets/js/upload.js') ?>"></script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
