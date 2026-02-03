<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Pacotes</h1>
<?php if (!empty($_GET['expired'])): ?>
    <div class="alert alert-warning">Sua assinatura expirou. Renove seu acesso abaixo.</div>
<?php endif; ?>
<?php if (!empty($_GET['requested'])): ?>
    <div class="alert alert-success">Solicitação enviada. Aguarde aprovação.</div>
<?php endif; ?>
<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger">Erro ao solicitar pacote.</div>
<?php endif; ?>
<?php if (empty($packages)): ?>
    <div class="alert alert-secondary">Nenhum pacote disponível.</div>
<?php else: ?>
    <div class="row">
        <?php foreach ($packages as $p): ?>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?= View::e($p['title']) ?></h5>
                        <p class="card-text"><?= View::e($p['description']) ?></p>
                        <a class="btn btn-primary" href="<?= base_path('/payments/checkout/' . (int)$p['id']) ?>">Comprar</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
