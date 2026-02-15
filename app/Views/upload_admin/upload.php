<?php
ob_start();
?>
<h1 class="h4 mb-3">Painel de Upload (Bypass)</h1>
<div class="d-flex flex-wrap gap-2 mb-3">
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
<form method="post" action="<?= base_path('/upload') ?>" enctype="multipart/form-data" class="section-card p-3">
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
        <input type="file" name="file[]" class="form-control" multiple required>
        <div class="form-text">Formatos aceitos: *.epub, *.cbr, *.cbz, *.zip (imagens) e *.pdf.</div>
    </div>
    <button class="btn btn-primary" type="submit" <?= !empty($noCategories) ? 'disabled' : '' ?>>Enviar</button>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
