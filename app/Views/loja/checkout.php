<?php
use App\Core\View;
ob_start();
?>
<div class="checkout-header mb-4">
    <div class="d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="checkout-icon">
                <i class="bi bi-credit-card"></i>
            </div>
            <div>
                <h1 class="h3 mb-1 fw-bold">Confirmar compra</h1>
                <p class="text-muted small mb-0">Revise os detalhes e envie o comprovante</p>
            </div>
        </div>
        <div class="badge bg-warning-subtle text-warning px-3 py-2 fw-semibold">
            <i class="bi bi-clock-history me-1"></i>
            Pagamento
        </div>
    </div>
</div>

<?php if (!empty($pricingError)): ?>
    <div class="alert alert-danger small"><?= View::e((string)$pricingError) ?></div>
<?php endif; ?>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="checkout-card">
            <div class="checkout-card-header">
                <i class="bi bi-box-seam me-2"></i>
                <h2 class="checkout-card-title">Resumo do pacote</h2>
            </div>
            <div class="checkout-card-body">
                <div class="checkout-package-info mb-3">
                    <div class="package-title"><?= View::e($package['title']) ?></div>
                    <div class="package-desc"><?= View::e((string)$package['description']) ?></div>
                </div>
                <div class="checkout-summary">
                    <div class="summary-item">
                        <span>Preço mensal</span>
                        <strong><?= format_brl((float)($package['price'] ?? 0)) ?></strong>
                    </div>
                    <div class="summary-item">
                        <span>Meses</span>
                        <strong><?= (int)($months ?? 1) ?></strong>
                    </div>
                    <div class="summary-divider"></div>
                <?php if (!empty($quote)): ?>
                    <?php
                    $newTermCostCents = (int)($quote['new_term_cost_cents'] ?? 0);
                    $upgradeDiffCents = (int)($quote['upgrade_diff_cents'] ?? 0);
                    $creditRemainingCents = (int)($quote['credit_current_remaining_cents'] ?? 0);
                    $costRemainingCents = (int)($quote['cost_new_remaining_cents'] ?? 0);
                    ?>
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <strong><?= format_brl($newTermCostCents / 100) ?></strong>
                    </div>
                    <?php if ($upgradeDiffCents > 0): ?>
                        <div class="summary-item">
                            <span>Diferença de upgrade</span>
                            <strong><?= format_brl($upgradeDiffCents / 100) ?></strong>
                        </div>
                    <?php endif; ?>
                    <?php if ($creditRemainingCents > 0 || $costRemainingCents > 0): ?>
                        <div class="summary-note">
                            Crédito restante<?= !empty($currentPackageTitle) ? ' (' . View::e((string)$currentPackageTitle) . ')' : '' ?>:
                            <strong><?= format_brl($creditRemainingCents / 100) ?></strong>
                            • Custo restante novo pacote:
                            <strong><?= format_brl($costRemainingCents / 100) ?></strong>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                    <div class="summary-total">
                        <span>Total</span>
                        <strong><?= format_brl((float)($total ?? 0)) ?></strong>
                    </div>
                    <?php if (!empty($remainingDays)): ?>
                        <div class="summary-note">Crédito calculado com base em <?= (int)$remainingDays ?> dia(s) restantes.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="checkout-card">
            <div class="checkout-card-header">
                <i class="bi bi-qr-code me-2"></i>
                <h2 class="checkout-card-title">Pagamento via PIX</h2>
            </div>
            <div class="checkout-card-body">
                <?php if (!empty($pixKey) || !empty($pixName) || !empty($pixHolder) || !empty($pixBank) || !empty($pixCpf)): ?>
                    <div class="checkout-pix-info">
                        <div class="pix-info-title">Dados para transferência</div>
                        <div class="pix-info-items">
                            <?php if (!empty($pixName) || !empty($pixHolder)): ?>
                                <div class="pix-info-item">
                                    <span class="pix-info-label">Recebedor</span>
                                    <span class="pix-info-value"><?= View::e($pixName !== '' ? $pixName : $pixHolder) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($pixBank)): ?>
                                <div class="pix-info-item">
                                    <span class="pix-info-label">Banco</span>
                                    <span class="pix-info-value"><?= View::e($pixBank) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($pixCpf)): ?>
                                <div class="pix-info-item">
                                    <span class="pix-info-label">CPF</span>
                                    <span class="pix-info-value"><?= View::e($pixCpf) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($pixKey)): ?>
                            <div class="pix-key-section">
                                <div class="pix-key-label">Chave PIX</div>
                                <div class="pix-key-copy">
                                    <input type="text" id="pixKeyValue" value="<?= View::e($pixKey) ?>" readonly>
                                    <button type="button" id="copyPixKey">
                                        <i class="bi bi-clipboard"></i>
                                        Copiar
                                    </button>
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

<div class="checkout-card">
    <div class="checkout-card-header">
        <i class="bi bi-file-earmark-arrow-up me-2"></i>
        <h2 class="checkout-card-title">Enviar comprovante</h2>
    </div>
    <div class="checkout-card-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger small">
                <?php if ($error === 'proof'): ?>
                    Envio do comprovante é obrigatório.
                <?php elseif ($error === 'ini_size' || $error === 'form_size'): ?>
                    O arquivo enviado excede o limite permitido pelo servidor.
                <?php elseif ($error === 'partial'): ?>
                    O upload foi interrompido. Tente novamente.
                <?php elseif ($error === 'tmp'): ?>
                    Pasta temporária ausente no servidor. Contate o suporte.
                <?php elseif ($error === 'write' || $error === 'perm'): ?>
                    Não foi possível salvar o comprovante no servidor.
                <?php elseif ($error === 'ext'): ?>
                    Extensão bloqueada pelo servidor.
                <?php elseif ($error === 'move'): ?>
                    Não foi possível salvar o comprovante. Tente novamente.
                <?php elseif ($error === 'size'): ?>
                    O arquivo ultrapassa 4MB.
                <?php elseif ($error === 'type'): ?>
                    Formato inválido. Envie JPG, PNG ou PDF.
                <?php elseif ($error === 'downgrade'): ?>
                    Downgrade só após vencimento.
                <?php else: ?>
                    Não foi possível enviar o comprovante.
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="checkout-file-info mb-3">
            <i class="bi bi-info-circle me-2"></i>
            Formatos aceitos: JPG, PNG ou PDF. Tamanho máximo: 4MB.
        </div>
        <form method="post" action="<?= upload_url('/loja/request') ?>" enctype="multipart/form-data" id="proofForm">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
            <input type="hidden" name="package_id" value="<?= (int)$package['id'] ?>">
            <input type="hidden" name="months" value="<?= (int)($months ?? 1) ?>">
            <div class="mb-3">
                <label class="form-label fw-semibold">Comprovante</label>
                <input type="file" name="proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
            </div>
            <div class="small text-danger mb-2" id="proofError" style="display:none"></div>
            <button class="btn btn-primary btn-lg w-100" type="submit" id="proofSubmit">
                <i class="bi bi-send me-2"></i>
                Enviar para aprovação
            </button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
