<?php
use App\Core\View;
ob_start();
?>
<div style="margin-top: 1.5rem;"></div>
<h1 class="h6 mb-2 fw-bold">Histórico de compras</h1>
<?php if (!empty($_GET['uploaded'])): ?>
    <div class="alert alert-success">Comprovante enviado.</div>
<?php endif; ?>
<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger">Erro ao enviar comprovante.</div>
<?php endif; ?>
<div class="payment-history-table">
    <div class="table-responsive">
        <table class="table">
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
            <?php
            $statusMap = [
                'pending' => 'Pendente',
                'approved' => 'Aprovado',
                'rejected' => 'Rejeitado',
                'revoked' => 'Estornado',
            ];
            $statusClasses = [
                'pending' => 'status-pending',
                'approved' => 'status-approved',
                'rejected' => 'status-rejected',
                'revoked' => 'status-revoked',
            ];
            foreach (($payments ?? []) as $p): ?>
                <tr>
                    <td><strong><?= (int)$p['id'] ?></strong></td>
                    <td><?= View::e((string)($p['package_name'] ?? ('#' . (int)$p['package_id']))) ?></td>
                    <td><?= (int)($p['months'] ?? 1) ?></td>
                    <td>
                        <span class="payment-status-badge <?= $statusClasses[$p['status']] ?? '' ?>">
                            <?= View::e($statusMap[$p['status']] ?? (string)$p['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($p['proof_path'])): ?>
                            <span class="text-success fw-semibold">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                Enviado
                            </span>
                        <?php elseif ($p['status'] === 'pending'): ?>
                            <form method="post" action="<?= upload_url('/loja/proof') ?>" enctype="multipart/form-data" class="proofForm">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                <input type="hidden" name="payment_id" value="<?= (int)$p['id'] ?>">
                                <input type="file" name="proof" class="form-control form-control-sm mb-2" accept=".jpg,.jpeg,.png,.pdf" required>
                                <div class="small text-muted mb-2">JPG/PNG/PDF até 4MB.</div>
                                <div class="small text-danger proofError" style="display:none"></div>
                                <button class="btn btn-sm btn-primary w-100 proofSubmit" type="submit">
                                    <i class="bi bi-upload me-1"></i>
                                    Enviar comprovante
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="text-muted small"><?= View::e((string)$p['created_at']) ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
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
