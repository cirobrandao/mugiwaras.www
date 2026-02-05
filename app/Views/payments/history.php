<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Meus pagamentos</h1>
<?php if (!empty($_GET['uploaded'])): ?>
    <div class="alert alert-success">Comprovante enviado.</div>
<?php endif; ?>
<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger">Erro ao enviar comprovante.</div>
<?php endif; ?>
<div class="table-responsive">
    <table class="table table-sm">
        <thead>
        <tr>
            <th>ID</th>
            <th>Pacote</th>
            <th>Meses</th>
            <th>Status</th>
            <th>Comprovante</th>
            <th>Data</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (($payments ?? []) as $p): ?>
            <tr>
                <td><?= (int)$p['id'] ?></td>
                <td><?= (int)$p['package_id'] ?></td>
                <td><?= (int)($p['months'] ?? 1) ?></td>
                <td><?= View::e($p['status']) ?></td>
                <td>
                    <?php if (!empty($p['proof_path'])): ?>
                        <span class="text-success">Enviado</span>
                    <?php elseif ($p['status'] === 'pending'): ?>
                        <form method="post" action="<?= base_path('/loja/proof') ?>" enctype="multipart/form-data" class="proofForm">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                            <input type="hidden" name="payment_id" value="<?= (int)$p['id'] ?>">
                            <input type="file" name="proof" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf" required>
                            <div class="small text-muted">JPG/PNG/PDF até 4MB.</div>
                            <div class="small text-danger mt-1 proofError" style="display:none"></div>
                            <button class="btn btn-sm btn-primary mt-1 proofSubmit" type="submit">Enviar comprovante</button>
                        </form>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td><?= View::e((string)$p['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
<script>
(() => {
    const forms = document.querySelectorAll('.proofForm');
    const max = 4 * 1024 * 1024;
    const allowed = ['image/jpeg','image/png','application/pdf','image/x-png'];
    forms.forEach((form) => {
        const input = form.querySelector('input[type="file"]');
        const btn = form.querySelector('.proofSubmit');
        const err = form.querySelector('.proofError');
        if (!input || !btn || !err) return;
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
    });
})();
</script>
