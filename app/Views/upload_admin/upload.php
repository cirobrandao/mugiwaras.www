<?php
ob_start();
?>
<div class="upload-admin-shell">
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <h1 class="h4 mb-1">Painel de Upload (Bypass)</h1>
        <div class="text-muted upload-admin-help">Área dedicada para envio de arquivos grandes fora da plataforma principal.</div>
    </div>
    <a class="btn btn-outline-danger" href="<?= base_path('/logout') ?>">Sair</a>
</div>
<?php if (!empty($setupError)): ?>
    <div class="alert alert-danger">Biblioteca não inicializada. Execute a migração 009_library_series.sql.</div>
<?php endif; ?>
<?php if (!empty($noCategories)): ?>
    <div class="alert alert-warning">Nenhuma categoria cadastrada. Crie uma categoria no painel administrativo.</div>
<?php endif; ?>
<div class="alert alert-info small">
    Este formulário é dedicado ao host/subdomínio com bypass de proxy. O upload real é processado pela rota padrão do sistema.
</div>
<div id="uploadResult"></div>
<section class="section-card mb-3 upload-card p-3">
    <div class="mb-3">
        <div class="progress" style="height: 6px;">
            <div class="progress-bar" id="limitBar" role="progressbar" style="width: 0%"></div>
        </div>
        <div class="small text-muted mt-1" id="limitInfo" data-max-bytes="209715200" data-max-files="100">0 B / 200 MB · 0 / 100 arquivos</div>
    </div>
    <div class="row">
        <div class="col-lg-7">
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
                <div class="mt-3 mb-3">
                    <label class="form-label">Arquivos</label>
                    <input type="file" name="file[]" class="form-control" multiple required data-max-bytes="209715200" data-max-files="100">
                    <div class="form-text">Formatos aceitos: *.epub, *.cbr, *.cbz, *.zip (imagens) e *.pdf.</div>
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
</div>
<script src="<?= base_path('/assets/js/upload.js') ?>"></script>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
