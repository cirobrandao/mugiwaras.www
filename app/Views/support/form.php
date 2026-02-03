<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Suporte</h1>
<?php if (!empty($_GET['sent'])): ?>
    <div class="alert alert-success">Mensagem enviada.</div>
<?php endif; ?>
<?php if (!empty($_GET['sent']) && !empty($_GET['track']) && $_GET['track'] === '0'): ?>
    <div class="alert alert-warning">Acompanhar chamado não está disponível no momento.</div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= View::e($error) ?></div>
<?php endif; ?>
<form method="post" action="<?= base_path('/support') ?>" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Assunto</label>
        <input type="text" name="subject" class="form-control" required maxlength="120">
    </div>
    <div class="mb-3">
        <label class="form-label">Mensagem</label>
        <textarea name="message" class="form-control" rows="5" required></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Anexo (jpg, png, webp ou pdf)</label>
        <input type="file" name="attachment" class="form-control" accept="image/*,application/pdf">
    </div>
    <button class="btn btn-primary" type="submit">Enviar</button>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
