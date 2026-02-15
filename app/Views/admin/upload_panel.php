<?php
ob_start();
?>
<h1 class="h4 mb-3">Painel de Upload</h1>
<div class="mb-3">
    <p>Este painel é dedicado ao envio de arquivos. Use o formulário principal de upload ou o subdomínio com bypass quando necessário.</p>
    <div class="mb-2">
        <a class="btn btn-primary me-2" href="<?= upload_url('/upload') ?>">Abrir formulário de upload</a>
        <?php if (!empty($uploadBypassUrl)): ?>
            <a class="btn btn-outline-secondary" href="<?= \App\Core\View::e($uploadBypassUrl) ?>" target="_blank" rel="noopener">Subdomínio de upload (bypass)</a>
        <?php else: ?>
            <span class="small text-muted">Subdomínio de upload não configurado.</span>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
