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
        <h2 class="h6">Identidade do sistema</h2>
        <form method="post" action="<?= base_path('/admin/settings/save') ?>" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
            <div class="col-md-6">
                <label class="form-label">Nome do sistema</label>
                <input class="form-control" name="system_name" value="<?= View::e((string)($systemName ?? '')) ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Termo de uso (cadastro)</label>
                <textarea class="form-control" name="terms_of_use" rows="6" required><?= View::e((string)($termsOfUse ?? '')) ?></textarea>
                <div class="form-text">Esse texto aparece na tela de cadastro.</div>
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
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
