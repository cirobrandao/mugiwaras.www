<?php
use App\Core\View;
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1">Vouchers</h1>
        <p class="text-muted small mb-0">Gerencie códigos promocionais e vouchers de assinatura</p>
    </div>
    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#voucherCreateModal">
        <i class="bi bi-plus-lg me-1"></i> Criar Voucher
    </button>
</div>

<?php if (!empty($_GET['migration_needed'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Migração Pendente:</strong> Execute o SQL <code>sql/002_payments_voucher_days.sql</code> para corrigir o cálculo de dias dos vouchers.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($_GET['error']) && $_GET['error'] === 'package'): ?>
    <div class="alert alert-warning">Selecione um pacote válido para o voucher.</div>
<?php elseif (!empty($_GET['error']) && $_GET['error'] === 'code'): ?>
    <div class="alert alert-warning">Código inválido. Use o prefixo VC-.</div>
<?php endif; ?>

<div class="modal fade" id="voucherCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title mb-1"><i class="bi bi-ticket-perforated me-2"></i>Criar Novo Voucher</h5>
                    <div class="small opacity-90">Código gerado automaticamente • Vencimento às 00:00</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_path('/admin/vouchers/save') ?>" class="row g-3" id="voucherForm">
                    <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                    <div class="col-md-12">
                        <label class="form-label">Pacote</label>
                        <select class="form-select" name="package_id" required>
                            <option value="">Selecionar pacote</option>
                            <?php foreach (($packages ?? []) as $pkg): ?>
                                <option value="<?= (int)$pkg['id'] ?>"><?= View::e((string)$pkg['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Dias de Assinatura</label>
                        <input class="form-control" type="number" min="0" step="1" name="days" placeholder="0">
                        <div class="form-text">Deixe 0 para usar os dias do pacote</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Limite de Usos</label>
                        <input class="form-control" type="number" min="0" step="1" name="max_uses" placeholder="Ilimitado">
                        <div class="form-text">0 = uso ilimitado</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Data de Expiração</label>
                        <input class="form-control" type="date" name="expires_at">
                        <div class="form-text">Opcional</div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="voucherActive" checked>
                            <label class="form-check-label" for="voucherActive">Ativo</label>
                        </div>
                    </div>
                    <div class="col-md-8 d-flex align-items-end justify-content-end">
                        <button class="btn btn-primary px-4" type="submit">Salvar voucher</button>
                    </div>
                </form>
                <div class="small text-muted mt-3">
                    Dias e limite de usos sao opcionais. Limite 0 = ilimitado. Vencimento e aplicado as 00:00.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Pacote</th>
                    <th>Criador</th>
                    <th>Usuários</th>
                    <th class="text-center">Dias</th>
                    <th class="text-center">Usos</th>
                    <th>Expira</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
        <tbody>
        <?php $historyModalUserIds = []; ?>
        <?php foreach (($vouchers ?? []) as $v): ?>
            <?php
                $redeemedUsers = $v['redeemed_users'] ?? [];
                if (!is_array($redeemedUsers)) {
                    $redeemedUsers = [];
                }
                $redeemedUsersDetailed = $v['redeemed_users_detailed'] ?? [];
                if (!is_array($redeemedUsersDetailed)) {
                    $redeemedUsersDetailed = [];
                }
            ?>
            <tr>
                <td><code class="text-primary"><?= View::e((string)$v['code']) ?></code></td>
                <td><span class="badge bg-info text-dark"><?= View::e((string)($v['package_title'] ?? '')) ?></span></td>
                <td><small class="text-muted"><?= View::e((string)($v['creator_username'] ?? '-')) ?></small></td>
                <td>
                    <?php if (!empty($v['is_used'])): ?>
                        <?php if (!empty($redeemedUsersDetailed)): ?>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach ($redeemedUsersDetailed as $ru): ?>
                                    <?php $uid = (int)($ru['id'] ?? 0); ?>
                                    <?php $uname = (string)($ru['username'] ?? ''); ?>
                                    <?php if ($uid > 0 && $uname !== ''): ?>
                                        <?php $historyModalUserIds[$uid] = $uid; ?>
                                        <a href="#" class="link-primary text-decoration-underline" data-bs-toggle="modal" data-bs-target="#userCommerceModal<?= $uid ?>" onclick="return false;">
                                            <?= View::e($uname) ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif (!empty($redeemedUsers)): ?>
                            <?= View::e(implode(', ', $redeemedUsers)) ?>
                        <?php else: ?>
                            Usado
                        <?php endif; ?>
                    <?php else: ?>
                        Não usado
                    <?php endif; ?>
                </td>
                <td class="text-center"><strong class="text-success"><?= (int)($v['added_days'] ?? 0) ?></strong></td>
                <td class="text-center"><?= (int)($v['uses'] ?? 0) ?><?= !empty($v['max_uses']) ? '<span class="text-muted"> / ' . (int)$v['max_uses'] . '</span>' : '' ?></td>
                <td><small><?= View::e((string)($v['expires_at'] ?? '-')) ?></small></td>
                <td class="text-center"><?= !empty($v['is_active']) ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-secondary">Inativo</span>' ?></td>
                <td class="text-end">
                    <form method="post" action="<?= base_path('/admin/vouchers/remove') ?>" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir o voucher <?= View::e((string)$v['code']) ?>?');">
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
</div>

<?php
$statusLabels = [
    'pending' => 'Pendente',
    'approved' => 'Aprovado',
    'rejected' => 'Rejeitado',
    'revoked' => 'Revogado',
];
$statusClasses = [
    'pending' => 'bg-warning text-dark',
    'approved' => 'bg-success',
    'rejected' => 'bg-danger',
    'revoked' => 'bg-secondary',
];
?>
<?php foreach (array_values($historyModalUserIds) as $historyUserId): ?>
    <?php
        $username = (string)(($redeemerMap ?? [])[$historyUserId] ?? ('#' . $historyUserId));
        $history = (array)(($userCommerceHistory ?? [])[$historyUserId] ?? []);
    ?>
    <div class="modal fade" id="userCommerceModal<?= (int)$historyUserId ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <div>
                        <h5 class="modal-title mb-1"><i class="bi bi-clock-history me-2"></i>Histórico de Compras e Vouchers</h5>
                        <div class="small opacity-90"><?= View::e($username) ?> • ID #<?= (int)$historyUserId ?></div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <?php if (empty($history)): ?>
                        <div class="text-muted">Sem compras ou ativações de voucher.</div>
                    <?php else: ?>
                        <div class="table-responsive overflow-auto" style="max-height: 360px;">
                            <table class="table table-sm align-middle mb-0 small">
                                <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 90px;">Tipo</th>
                                    <th scope="col">Detalhes</th>
                                    <th scope="col" style="width: 110px;">Status</th>
                                    <th scope="col" style="width: 160px;">Data</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($history as $entry): ?>
                                    <?php
                                        $isPayment = (string)($entry['type'] ?? 'payment') === 'payment';
                                        if ($isPayment) {
                                            $p = (array)($entry['payment'] ?? []);
                                            $packageTitle = (string)($p['package_title'] ?? '-');
                                            $reference = '#' . (int)($p['id'] ?? 0);
                                            $months = (int)($p['months'] ?? 0);
                                            $days = (int)($p['package_subscription_days'] ?? 0) * max(1, $months);
                                            $total = (float)($p['package_price'] ?? 0) * max(1, $months);
                                            $status = (string)($p['status'] ?? 'pending');
                                            $date = (string)($p['created_at'] ?? '-');
                                        } else {
                                            $vRow = (array)($entry['voucher'] ?? []);
                                            $packageTitle = (string)($vRow['package_title'] ?? '-');
                                            $reference = (string)($vRow['voucher_code'] ?? '-');
                                            $months = 0;
                                            $days = (int)($vRow['added_days'] ?? 0);
                                            $total = 0.0;
                                            $status = 'approved';
                                            $date = (string)($vRow['redeemed_at'] ?? '-');
                                        }
                                        $badgeClass = $statusClasses[$status] ?? 'bg-secondary';
                                    ?>
                                    <tr>
                                        <td><?= $isPayment ? 'Compra' : 'Voucher' ?></td>
                                        <td>
                                            <div class="fw-medium"><?= View::e($packageTitle) ?></div>
                                            <div class="text-muted"><?= View::e($reference) ?> · <?= $days > 0 ? ($days . ' dias') : '-' ?><?= $months > 0 ? (' · ' . $months . ' meses') : '' ?><?= $total > 0 ? (' · ' . format_brl($total)) : '' ?></div>
                                        </td>
                                        <td><span class="badge <?= $badgeClass ?>"><?= View::e($statusLabels[$status] ?? ($isPayment ? $status : 'Ativado')) ?></span></td>
                                        <td><?= View::e($date) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';