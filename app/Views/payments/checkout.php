<?php
use App\Core\View;
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Confirmar compra</h1>
    <span class="badge bg-light text-dark">Checkout</span>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h2 class="h6">Resumo do pacote</h2>
                <p><strong>Título:</strong> <?= View::e($package['title']) ?></p>
                <p class="text-muted small mb-3"><?= View::e((string)$package['description']) ?></p>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Preço mensal</span>
                    <strong>R$ <?= number_format((float)($package['price'] ?? 0), 2, ',', '.') ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Meses</span>
                    <strong><?= (int)($months ?? 1) ?></strong>
                </div>
                <hr class="my-2">
                <?php if (!empty($prorataCredit) && $prorataCredit > 0): ?>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Subtotal</span>
                        <strong>R$ <?= number_format((float)($baseTotal ?? 0), 2, ',', '.') ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Crédito pró-rata<?= !empty($currentPackageTitle) ? ' (' . View::e((string)$currentPackageTitle) . ')' : '' ?></span>
                        <strong class="text-success">- R$ <?= number_format((float)$prorataCredit, 2, ',', '.') ?></strong>
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Total</span>
                    <strong class="fs-5">R$ <?= number_format((float)($total ?? 0), 2, ',', '.') ?></strong>
                </div>
                <?php if (!empty($remainingDays)): ?>
                    <div class="small text-muted mt-2">Crédito calculado com base em <?= (int)$remainingDays ?> dia(s) restantes.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h2 class="h6">Pagamento via PIX</h2>
                <?php if (!empty($pixKey) || !empty($pixName)): ?>
                    <div class="alert alert-info mb-0">
                        <div class="fw-semibold mb-1">Chave PIX</div>
                        <div><?= !empty($pixName) ? View::e($pixName) : '' ?></div>
                        <div class="text-break"><?= !empty($pixKey) ? View::e($pixKey) : '' ?></div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">PIX não configurado.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
 
	<div class="card shadow-sm">
     <div class="card-body">
		<h2 class="h6">Enviar comprovante</h2>
		<div class="small text-muted mb-3">Formatos aceitos: JPG, PNG ou PDF. Tamanho máximo: 4MB.</div>
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
<script>
(() => {
    const input = document.querySelector('#proofForm input[type="file"]');
    const btn = document.getElementById('proofSubmit');
    const err = document.getElementById('proofError');
    if (!input || !btn || !err) return;
    const max = 4 * 1024 * 1024;
    const allowed = ['image/jpeg','image/png','application/pdf','image/x-png'];
    input.addEventListener('change', () => {
        err.style.display = 'none';
        const file = input.files && input.files[0];
        if (!file) return;
        if (file.size > max) {
            err.textContent = 'Arquivo maior que 4MB.';
            err.style.display = 'block';
            return;
        }
        if (!allowed.includes(file.type)) {
            err.textContent = 'Tipo inválido. Envie JPG, PNG ou PDF.';
            err.style.display = 'block';
            return;
        }
    });
})();
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
