<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Configurações</h1>

<?php if (!empty($_GET['error']) && $_GET['error'] === 'logo'): ?>
    <div class="alert alert-danger">Logo inválido. Use imagem (jpg, png, webp, svg).</div>
<?php elseif (!empty($_GET['error']) && $_GET['error'] === 'favicon'): ?>
    <div class="alert alert-danger">Favicon inválido. Use imagem (ico, png, svg, jpg, webp).</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <h2 class="h6 mb-1">Identidade do sistema</h2>
        <div class="text-muted small mb-3">Nome do sistema, logo e favicon sao configuracoes do sistema.</div>
        <form method="post" action="<?= base_path('/admin/settings/save') ?>" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
            <div class="col-md-6">
                <label class="form-label">Nome do sistema</label>
                <input class="form-control" name="system_name" value="<?= View::e((string)($systemName ?? '')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Logo (altura limitada pela barra)</label>
                <input class="form-control" type="file" name="logo" accept="image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label">Favicon</label>
                <input class="form-control" type="file" name="favicon" accept="image/*">
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit">Salvar</button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h2 class="h6 mb-1">Termo de uso do cadastro</h2>
                <div class="text-muted small">Esse texto aparece na tela de cadastro.</div>
            </div>
            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#termsModal">Atualizar termo</button>
        </div>
        <div class="mt-3 border rounded p-3 bg-light small" style="max-height: 200px; overflow: auto;">
            <?= nl2br(View::e((string)($termsOfUse ?? ''))) ?>
        </div>
    </div>
</div>

<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="post" action="<?= base_path('/admin/settings/save') ?>">
                <div class="modal-header">
                    <h2 class="modal-title h6" id="termsModalLabel">Atualizar termo de uso</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                    <label class="form-label">Termo de uso (cadastro)</label>
                    <textarea class="form-control" name="terms_of_use" rows="10" required><?= View::e((string)($termsOfUse ?? '')) ?></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" type="submit">Salvar termo</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
