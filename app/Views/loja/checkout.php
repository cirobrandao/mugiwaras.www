<?php
use App\Core\View;
ob_start();
?>
<div style="margin-top: 1.5rem;"></div>
<div class="checkout-header mb-2">
    <div class="d-flex align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="checkout-icon">
                <i class="bi bi-credit-card"></i>
            </div>
            <div>
                <h1 class="h6 mb-0 fw-bold">Confirmar compra</h1>
                <p class="text-muted mb-0" style="font-size: 0.6875rem;">Revise e envie o comprovante</p>
            </div>
        </div>
        <div class="badge bg-warning-subtle text-warning px-2 py-1 fw-semibold" style="font-size: 0.6875rem;">
            <i class="bi bi-clock-history" style="font-size: 0.75rem;"></i>
            Pag.
        </div>
    </div>
</div>

<?php if (!empty($pricingError)): ?>
    <div class="alert alert-danger small"><?= View::e((string)$pricingError) ?></div>
<?php endif; ?>

<div class="row g-2 mb-2">
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
<script>
(() => {
    // PIX Key Copy functionality
    const copyBtn = document.getElementById('copyPixKey');
    if (copyBtn) {
        copyBtn.addEventListener('click', () => {
            const input = document.getElementById('pixKeyValue');
            if (input) {
                input.select();
                input.setSelectionRange(0, 99999); // For mobile devices
                
                navigator.clipboard.writeText(input.value).then(() => {
                    const originalHtml = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<i class="bi bi-check-lg"></i> Copiado!';
                    copyBtn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                    
                    setTimeout(() => {
                        copyBtn.innerHTML = originalHtml;
                        copyBtn.style.background = '';
                    }, 2000);
                }).catch(err => {
                    console.error('Erro ao copiar:', err);
                    // Fallback for older browsers
                    try {
                        document.execCommand('copy');
                        const originalHtml = copyBtn.innerHTML;
                        copyBtn.innerHTML = '<i class="bi bi-check-lg"></i> Copiado!';
                        setTimeout(() => {
                            copyBtn.innerHTML = originalHtml;
                        }, 2000);
                    } catch (e) {
                        alert('Não foi possível copiar automaticamente. Por favor, copie manualmente.');
                    }
                });
            }
        });
    }

    // File validation
    const form = document.getElementById('proofForm');
    const fileInput = form?.querySelector('input[type="file"]');
    const errorDiv = document.getElementById('proofError');
    const submitBtn = document.getElementById('proofSubmit');
    
    if (form && fileInput && errorDiv && submitBtn) {
        const maxSize = 4 * 1024 * 1024; // 4MB
        const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'image/x-png'];
        
        fileInput.addEventListener('change', () => {
            errorDiv.style.display = 'none';
            const file = fileInput.files && fileInput.files[0];
            
            if (!file) return;
            
            if (file.size > maxSize) {
                errorDiv.textContent = 'Arquivo maior que 4MB. Por favor, escolha um arquivo menor.';
                errorDiv.style.display = 'block';
                fileInput.value = '';
                return;
            }
            
            if (!allowedTypes.includes(file.type)) {
                errorDiv.textContent = 'Tipo inválido. Envie apenas JPG, PNG ou PDF.';
                errorDiv.style.display = 'block';
                fileInput.value = '';
                return;
            }
        });
        
        form.addEventListener('submit', (e) => {
            const file = fileInput.files && fileInput.files[0];
            
            if (!file) {
                e.preventDefault();
                errorDiv.textContent = 'Por favor, selecione um comprovante.';
                errorDiv.style.display = 'block';
                return;
            }
            
            if (file.size > maxSize) {
                e.preventDefault();
                errorDiv.textContent = 'Arquivo maior que 4MB.';
                errorDiv.style.display = 'block';
                return;
            }
            
            if (!allowedTypes.includes(file.type)) {
                e.preventDefault();
                errorDiv.textContent = 'Tipo inválido. Envie apenas JPG, PNG ou PDF.';
                errorDiv.style.display = 'block';
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
        });
    }
})();
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
