<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Loja</h1>
<?php if (!empty($_GET['expired'])): ?>
    <div class="alert alert-warning">Sua assinatura expirou. Renove seu acesso abaixo.</div>
<?php endif; ?>
<?php if (!empty($_GET['requested'])): ?>
    <div class="alert alert-success">Solicitação enviada. Aguarde aprovação.</div>
<?php endif; ?>
<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger">Erro ao solicitar pacote.</div>
<?php endif; ?>
<?php if (!empty($_GET['voucher'])): ?>
    <?php if ($_GET['voucher'] === 'ok'): ?>
        <div class="alert alert-success">Voucher aplicado com sucesso.</div>
    <?php elseif ($_GET['voucher'] === 'used'): ?>
        <div class="alert alert-warning">Este voucher já foi utilizado por você.</div>
    <?php elseif ($_GET['voucher'] === 'expired'): ?>
        <div class="alert alert-warning">Este voucher expirou.</div>
    <?php elseif ($_GET['voucher'] === 'limit'): ?>
        <div class="alert alert-warning">Este voucher atingiu o limite de uso.</div>
    <?php else: ?>
        <div class="alert alert-danger">Voucher inválido.</div>
    <?php endif; ?>
<?php endif; ?>
<?php if (empty($packages)): ?>
    <div class="alert alert-secondary">Nenhum pacote disponível.</div>
<?php else: ?>
    <?php $catList = $categories ?? []; ?>
    <div class="row g-3">
        <?php foreach ($packages as $p): ?>
            <?php
            $pkgCats = $packageCategories[(int)$p['id']] ?? [];
            $activeCats = [];
            $inactiveCats = [];
            foreach ($catList as $c) {
                $cid = (int)$c['id'];
                if (in_array($cid, $pkgCats, true)) {
                    $activeCats[] = $c;
                } else {
                    $inactiveCats[] = $c;
                }
            }
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm loja-card">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= View::e($p['title']) ?></h5>
                        <p class="card-text text-muted small mb-3"><?= View::e($p['description']) ?></p>
                        <?php if (!empty($catList)): ?>
                            <ul class="list-unstyled small mb-3">
                                <?php foreach ($activeCats as $c): ?>
                                    <li><span class="me-1">›</span><?= View::e((string)$c['name']) ?></li>
                                <?php endforeach; ?>
                                <?php foreach ($inactiveCats as $c): ?>
                                    <li class="text-muted"><span class="me-1">›</span><del><?= View::e((string)$c['name']) ?></del></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <form class="mt-auto" method="get" action="<?= base_path('/loja/checkout/' . (int)$p['id']) ?>">
                            <div class="fw-semibold mb-2">
                                <?= format_brl((float)($p['price'] ?? 0)) ?> / mês
                            </div>
                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                                <label class="small text-muted mb-0">Deseja assinar quantos meses?</label>
                                <div class="d-flex align-items-center gap-2">
                                    <select class="form-select form-select-sm w-auto pkg-months" name="months">
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
                                            <option value="<?= $m ?>" <?= $m === 1 ? 'selected' : '' ?>><?= $m ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <button class="btn btn-primary" type="submit">Assinar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card mt-3 loja-card">
    <div class="card-body">
        <h2 class="h6">Tenho um voucher</h2>
        <form method="post" action="<?= base_path('/loja/voucher') ?>" class="row g-2">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
            <div class="col-md-6">
                <input class="form-control" name="code" placeholder="VC-XXXXXXXX" required>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-outline-primary" type="submit">Aplicar</button>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
