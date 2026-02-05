<?php
use App\Core\View;
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Vouchers</h1>
    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#voucherCreateModal">Adicionar</button>
</div>

<?php if (!empty($_GET['error']) && $_GET['error'] === 'package'): ?>
    <div class="alert alert-warning">Selecione um pacote válido para o voucher.</div>
<?php elseif (!empty($_GET['error']) && $_GET['error'] === 'code'): ?>
    <div class="alert alert-warning">Código inválido. Use o prefixo VC-.</div>
<?php endif; ?>

<div class="modal fade" id="voucherCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar voucher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_path('/admin/vouchers/save') ?>" class="row g-2" id="voucherForm">
                    <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                    <div class="col-md-6">
                        <label class="form-label">Código</label>
                        <input class="form-control" name="code" placeholder="VC-..." id="voucherKey" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pacote</label>
                        <select class="form-select" name="package_id" required>
                            <option value="">Selecionar pacote</option>
                            <?php foreach (($packages ?? []) as $pkg): ?>
                                <option value="<?= (int)$pkg['id'] ?>"><?= View::e((string)$pkg['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dias (opcional)</label>
                        <input class="form-control" type="number" min="0" step="1" name="days" placeholder="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Limite de usos</label>
                        <input class="form-control" type="number" min="0" step="1" name="max_uses" placeholder="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Expira em</label>
                        <input class="form-control" type="datetime-local" name="expires_at">
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="voucherActive" checked>
                            <label class="form-check-label" for="voucherActive">Ativo</label>
                        </div>
                    </div>
                    <div class="col-md-4 d-grid">
                        <button class="btn btn-outline-secondary" type="button" id="voucherGenerate">Gerar chave</button>
                    </div>
                    <div class="col-md-4 d-grid">
                        <button class="btn btn-primary" type="submit">Salvar</button>
                    </div>
                </form>
                <div class="small text-muted mt-2">
                    Dias é opcional; se vazio, usa os dias do pacote. Limite de usos 0 = ilimitado.
                </div>
            </div>
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
        <thead><tr><th>Código</th><th>Pacote</th><th>Dias</th><th>Usos</th><th>Expira</th><th>Status</th><th class="text-end">Ações</th></tr></thead>
        <tbody>
        <?php foreach (($vouchers ?? []) as $v): ?>
            <tr>
                <td><?= View::e((string)$v['code']) ?></td>
                <td><?= View::e((string)($v['package_title'] ?? '')) ?></td>
                <td><?= (int)($v['days'] ?? 0) ?></td>
                <td><?= (int)($v['uses'] ?? 0) ?><?= !empty($v['max_uses']) ? ' / ' . (int)$v['max_uses'] : '' ?></td>
                <td><?= View::e((string)($v['expires_at'] ?? '-')) ?></td>
                <td><?= !empty($v['is_active']) ? 'Ativo' : 'Inativo' ?></td>
                <td class="text-end">
                    <form method="post" action="<?= base_path('/admin/vouchers/remove') ?>" class="d-inline">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                        <input type="hidden" name="code" value="<?= View::e((string)$v['code']) ?>">
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