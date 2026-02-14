<?php
use App\Core\View;
ob_start();
?>
<div class="loja-header mb-4">
    <div class="d-flex align-items-center justify-content-between gap-3 mb-2">
        <div>
            <h1 class="h3 mb-1 fw-bold">Loja de Assinaturas</h1>
            <p class="text-muted small mb-0">Escolha o plano ideal para você</p>
        </div>
        <div class="loja-icon">
            <i class="bi bi-bag-heart"></i>
        </div>
    </div>
</div>
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
    <div class="loja-packages-grid">
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
            <div class="loja-package-card">
                <div class="loja-package-header">
                    <div class="loja-package-icon">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <h3 class="loja-package-title"><?= View::e($p['title']) ?></h3>
                    <p class="loja-package-desc"><?= View::e($p['description']) ?></p>
                </div>
                
                <div class="loja-package-price">
                    <span class="price-value"><?= format_brl((float)($p['price'] ?? 0)) ?></span>
                    <span class="price-period">/ mês</span>
                </div>

                <?php if (!empty($catList)): ?>
                    <div class="loja-package-features">
                        <div class="features-label">Acesso incluído:</div>
                        <ul class="features-list">
                            <?php foreach ($activeCats as $c): ?>
                                <li class="feature-item active">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span><?= View::e((string)$c['name']) ?></span>
                                </li>
                            <?php endforeach; ?>
                            <?php foreach ($inactiveCats as $c): ?>
                                <li class="feature-item inactive">
                                    <i class="bi bi-x-circle"></i>
                                    <span><?= View::e((string)$c['name']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form class="loja-package-form" method="get" action="<?= base_path('/loja/checkout/' . (int)$p['id']) ?>">
                    <div class="form-group mb-3">
                        <label class="form-label">Período de assinatura</label>
                        <select class="form-select" name="months">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $m === 1 ? 'selected' : '' ?>>
                                    <?= $m ?> <?= $m === 1 ? 'mês' : 'meses' ?>
                                    <?php if ($m > 1): ?>
                                        - <?= format_brl((float)($p['price'] ?? 0) * $m) ?>
                                    <?php endif; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="bi bi-cart-check me-2"></i>
                        Assinar Agora
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="loja-voucher-section">
    <div class="voucher-card">
        <div class="voucher-icon">
            <i class="bi bi-ticket-perforated"></i>
        </div>
        <div class="voucher-content">
            <h2 class="voucher-title">Tem um voucher?</h2>
            <p class="voucher-desc">Digite seu código para ativar benefícios especiais</p>
            <form method="post" action="<?= base_path('/loja/voucher') ?>" class="voucher-form">
                <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                <div class="input-group">
                    <input class="form-control" name="code" placeholder="Digite seu código de voucher" required>
                    <button class="btn btn-success" type="submit">
                        <i class="bi bi-check-lg me-1"></i>
                        Aplicar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
