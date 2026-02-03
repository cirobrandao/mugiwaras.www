<?php
use App\Core\View;
ob_start();
?>
<?php if (!empty($error)): ?>
    <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div><?= View::e((string)$error) ?></div>
        <?php if (!empty($downloadUrl)): ?>
            <a class="btn btn-sm btn-outline-primary" href="<?= $downloadUrl ?>">Baixar PDF</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (empty($error) && !empty($content)): ?>
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
        <h1 class="h5 mb-0"><?= View::e((string)($content['title'] ?? 'PDF')) ?></h1>
        <div class="d-flex gap-2">
            <?php if (!empty($inlineUrl)): ?>
                <a class="btn btn-sm btn-outline-primary" href="<?= $inlineUrl ?>">Abrir PDF nesta janela</a>
            <?php endif; ?>
            <?php if (!empty($downloadUrl)): ?>
                <a class="btn btn-sm btn-outline-primary" href="<?= $downloadUrl ?>">Baixar PDF</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="alert alert-secondary">Abertura do PDF em página própria (sem iframe) para evitar bloqueios do navegador.</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
