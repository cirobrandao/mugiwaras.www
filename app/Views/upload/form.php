<?php
ob_start();
?>
<h1 class="h4 mb-3">Upload</h1>
<?php if (!empty($_GET['ok']) || !empty($_GET['queued']) || !empty($_GET['failed'])): ?>
    <div class="alert alert-info">
        Enviados: <?= (int)($_GET['ok'] ?? 0) ?>,
        Enfileirados: <?= (int)($_GET['queued'] ?? 0) ?>,
        Falhas: <?= (int)($_GET['failed'] ?? 0) ?>.
    </div>
<?php elseif (isset($_GET['processed'])): ?>
    <div class="alert alert-info">
        Conversões processadas: <?= (int)($_GET['processed'] ?? 0) ?>,
        Falhas: <?= (int)($_GET['failed_jobs'] ?? 0) ?>.
    </div>
<?php elseif (!empty($_GET['error'])): ?>
    <?php if ($_GET['error'] === 'limit'): ?>
        <div class="alert alert-danger">O tamanho total do envio excede 5 GB.</div>
    <?php elseif ($_GET['error'] === 'category'): ?>
        <div class="alert alert-danger">Selecione uma categoria válida.</div>
    <?php elseif ($_GET['error'] === 'files'): ?>
        <div class="alert alert-danger">Máximo de 20 arquivos por envio.</div>
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
<div id="uploadResult"></div>
<div class="alert alert-secondary">Upload e conversões são processados via jobs.</div>
<div class="mb-3">
    <div class="progress" style="height: 6px;">
        <div class="progress-bar" id="limitBar" role="progressbar" style="width: 0%"></div>
    </div>
    <div class="small text-muted mt-1" id="limitInfo" data-max-bytes="5368709120" data-max-files="20">0 B / 5 GB · 0 / 20 arquivos</div>
</div>
<form method="post" action="<?= base_path('/upload') ?>" enctype="multipart/form-data">
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
        <input type="file" name="file[]" class="form-control" multiple required data-max-bytes="5368709120" data-max-files="20">
        <div class="small text-muted mt-1">Formatos aceitos: *.epub, *.cbr, *.cbz, *.zip (imagens).</div>
        <div class="small text-muted">PDF: sem leitor, apenas download (aparece sinalizado na biblioteca).</div>
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
<hr class="my-4">

<?php
$formatBytes = function (int $bytes): string {
    if ($bytes >= 1024 ** 3) return number_format($bytes / (1024 ** 3), 2) . ' GB';
    if ($bytes >= 1024 ** 2) return number_format($bytes / (1024 ** 2), 2) . ' MB';
    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
};
?>

<div id="uploadHistory">
    <h2 class="h5 mb-2">Histórico de envios</h2>
    <?php if (!empty($pendingCount)): ?>
        <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center">
            <div class="me-2">
                Conversões pendentes: <?= (int)$pendingCount ?>.
                <span class="text-muted">Processa até 5 por clique.</span>
            </div>
            <form method="post" action="<?= base_path('/upload/process-pending') ?>" class="mb-0">
                <input type="hidden" name="_csrf" value="<?= \App\Core\View::e($csrf ?? '') ?>">
                <button class="btn btn-sm btn-outline-dark" type="submit">Processar conversões</button>
            </form>
        </div>
    <?php endif; ?>
    <div class="small text-muted mb-3">
        Total de arquivos: <?= (int)($totalUploads ?? 0) ?> · Total enviado: <?= $formatBytes((int)($totalSize ?? 0)) ?>
    </div>

    <?php if (empty($uploads)): ?>
        <div class="alert alert-secondary">Nenhum upload encontrado.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Arquivo</th>
                    <th>Status</th>
                    <th class="text-end">Tamanho</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach (($uploads ?? []) as $u): ?>
                    <tr>
                        <td><?= \App\Core\View::e((string)($u['created_at'] ?? '')) ?></td>
                        <td><?= \App\Core\View::e((string)($u['original_name'] ?? $u['title'] ?? '')) ?></td>
                        <td><?= \App\Core\View::e((string)($u['status'] ?? '')) ?></td>
                        <td class="text-end"><?= $formatBytes((int)($u['file_size'] ?? 0)) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($pages) && $pages > 1): ?>
            <nav>
                <ul class="pagination pagination-sm">
                    <?php $prev = max(1, (int)($page ?? 1) - 1); ?>
                    <?php $next = min((int)$pages, (int)($page ?? 1) + 1); ?>
                    <li class="page-item <?= ($page ?? 1) <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= base_path('/upload?page=' . $prev) ?>">Anterior</a>
                    </li>
                    <?php for ($p = 1; $p <= (int)$pages; $p++): ?>
                        <li class="page-item <?= ($page ?? 1) === $p ? 'active' : '' ?>">
                            <a class="page-link" href="<?= base_path('/upload?page=' . $p) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page ?? 1) >= (int)$pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= base_path('/upload?page=' . $next) ?>">Próxima</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
<script src="<?= url('assets/js/upload.js') ?>"></script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
