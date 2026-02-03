<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Confirmar compra</h1>

<div class="card mb-3">
    <div class="card-body">
        <h2 class="h6">Resumo do pacote</h2>
        <p><strong>Título:</strong> <?= View::e($package['title']) ?></p>
        <p><strong>Descrição:</strong> <?= View::e((string)$package['description']) ?></p>
        <p><strong>Preço:</strong> <?= View::e((string)$package['price']) ?></p>
        <p><strong>Bônus:</strong> <?= (int)$package['bonus_credits'] ?></p>
        <p><strong>Dias:</strong> <?= (int)$package['subscription_days'] ?></p>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h2 class="h6">Pagamento via PIX</h2>
        <?php if (!empty($pixKey) || !empty($pixName)): ?>
+            <div class="alert alert-info mb-0">
+                <strong>PIX:</strong>
+                <?= !empty($pixName) ? View::e($pixName) : '' ?>
+                <?= !empty($pixKey) ? ' - ' . View::e($pixKey) : '' ?>
+            </div>
         <?php else: ?>
             <div class="alert alert-warning mb-0">PIX não configurado.</div>
         <?php endif; ?>
     </div>
 </div>
 
 <div class="card">
     <div class="card-body">
        <h2 class="h6">Enviar comprovante</h2>
        <div class="small text-muted mb-2">Formatos aceitos: JPG, PNG ou PDF. Tamanho máximo: 4MB.</div>
                <form method="post" action="<?= base_path('/payments/request') ?>" enctype="multipart/form-data" id="proofForm">
             <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
             <input type="hidden" name="package_id" value="<?= (int)$package['id'] ?>">
             <div class="mb-3">
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
