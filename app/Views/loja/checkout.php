<?php
use App\Core\View;
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Confirmar compra</h1>
    <span class="badge bg-secondary">Pagamento</span>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card h-100 shadow-sm loja-card">
            <div class="card-body">
                <h2 class="h6">Resumo do pacote</h2>
                <p><strong>Título:</strong> <?= View::e($package['title']) ?></p>
                <p class="text-muted small mb-3"><?= View::e((string)$package['description']) ?></p>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Preço mensal</span>
                    <strong><?= format_brl((float)($package['price'] ?? 0)) ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Meses</span>
                    <strong><?= (int)($months ?? 1) ?></strong>
                </div>
                <hr class="my-2">
                <?php if (!empty($prorataCredit) && $prorataCredit > 0): ?>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Subtotal</span>
                        <strong><?= format_brl((float)($baseTotal ?? 0)) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Crédito pró-rata<?= !empty($currentPackageTitle) ? ' (' . View::e((string)$currentPackageTitle) . ')' : '' ?></span>
                        <strong class="text-success">- <?= format_brl((float)$prorataCredit) ?></strong>
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Total</span>
                    <strong class="fs-5"><?= format_brl((float)($total ?? 0)) ?></strong>
                </div>
                <?php if (!empty($remainingDays)): ?>
                    <div class="small text-muted mt-2">Crédito calculado com base em <?= (int)$remainingDays ?> dia(s) restantes.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
            <div class="card h-100 shadow-sm loja-card">
            <div class="card-body">
                <h2 class="h6">Pagamento via PIX</h2>
                <?php if (!empty($pixKey) || !empty($pixName) || !empty($pixHolder) || !empty($pixBank) || !empty($pixCpf)): ?>
                    <div class="alert alert-info mb-0">
                        <div class="fw-semibold mb-2">Dados para transferencia</div>
                        <?php if (!empty($pixName) || !empty($pixHolder)): ?>
                            <div><strong>Recebedor:</strong> <?= View::e($pixName !== '' ? $pixName : $pixHolder) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($pixBank)): ?>
                            <div><strong>Banco:</strong> <?= View::e($pixBank) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($pixCpf)): ?>
                            <div><strong>CPF:</strong> <?= View::e($pixCpf) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($pixKey)): ?>
                            <div class="mt-2">
                                <div class="fw-semibold">Chave PIX</div>
                                <div class="input-group input-group-sm mt-1">
                                    <input class="form-control" type="text" id="pixKeyValue" value="<?= View::e($pixKey) ?>" readonly>
                                    <button class="btn btn-outline-primary" type="button" id="copyPixKey">Copiar</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">PIX não configurado.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm loja-card">
    <div class="card-body">
        <h2 class="h6">Enviar comprovante</h2>
        <div class="form-text mb-3">Formatos aceitos: JPG, PNG ou PDF. Tamanho máximo: 4MB.</div>
        <form method="post" action="<?= base_path('/loja/request') ?>" enctype="multipart/form-data" id="proofForm">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
            <input type="hidden" name="package_id" value="<?= (int)$package['id'] ?>">
            <input type="hidden" name="months" value="<?= (int)($months ?? 1) ?>">
            <div class="mb-3">
                <label class="form-label">Comprovante</label>
                <input type="file" name="proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
            </div>
            <div class="small text-danger mb-2" id="proofError" style="display:none"></div>
            <button class="btn btn-primary" type="submit" id="proofSubmit">Enviar para aprovação</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
