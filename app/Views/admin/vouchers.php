<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Vouchers</h1>

<div class="card mb-4">
    <div class="card-body">
        <h2 class="h6">Adicionar / Atualizar</h2>
        <form method="post" action="<?= base_path('/admin/vouchers/save') ?>" class="row g-2" id="voucherForm">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
            <div class="col-md-4">
                <input class="form-control" name="key" placeholder="chave" id="voucherKey" required>
            </div>
            <div class="col-md-6">
                <input class="form-control" type="number" min="0" step="1" name="value" placeholder="dias de acesso">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">Salvar</button>
            </div>
            <div class="col-md-4 d-grid">
                <button class="btn btn-outline-secondary" type="button" id="voucherGenerate">Gerar chave</button>
            </div>
        </form>
        <div class="small text-muted mt-2">
            Valor representa quantidade de dias de acesso.
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('voucherGenerate');
    const input = document.getElementById('voucherKey');
    if (!btn || !input) return;
    const gen = () => {
        const bytes = new Uint8Array(12);
        window.crypto.getRandomValues(bytes);
        const hex = Array.from(bytes).map(b => b.toString(16).padStart(2, '0')).join('');
        input.value = `VC-${hex}`.toUpperCase();
        input.focus();
    };
    btn.addEventListener('click', gen);
});
</script>

<div class="table-responsive">
    <table class="table table-sm">
        <thead><tr><th>Chave</th><th>Valor</th><th class="text-end">Ações</th></tr></thead>
        <tbody>
        <?php foreach (($settings ?? []) as $s): ?>
            <tr>
                <td><?= View::e($s['key']) ?></td>
                <td><?= View::e($s['value']) ?></td>
                <td class="text-end">
                    <form method="post" action="<?= base_path('/admin/vouchers/remove') ?>" class="d-inline">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                        <input type="hidden" name="key" value="<?= View::e($s['key']) ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';