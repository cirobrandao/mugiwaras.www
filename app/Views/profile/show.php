<?php
use App\Core\View;
ob_start();
$profileTitle = $profileTitle ?? 'Meu perfil';
$profileBase = $profileBase ?? '/perfil';
$canEditProfile = $canEditProfile ?? (($user['access_tier'] ?? '') !== 'restrito');
?>
<h1 class="h4 mb-3"><?= View::e($profileTitle) ?></h1>
<div class="card">
    <div class="card-body">
        <div class="row g-3 align-items-start">
            <div class="col-auto">
                <div class="border rounded d-flex align-items-center justify-content-center profile-avatar-box">
                    <?php $avatarPath = (string)($user['avatar_path'] ?? ''); ?>
                    <?php if ($avatarPath !== ''): ?>
                        <img src="<?= base_path('/' . ltrim($avatarPath, '/')) ?>" alt="Avatar" class="profile-avatar-img">
                    <?php else: ?>
                        <div class="text-muted small">Sem avatar</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                    <div class="d-flex flex-column gap-1">
                        <div class="fw-semibold fs-5"><?= View::e((string)($user['username'] ?? '')) ?></div>
                        <div class="text-body-secondary small">Perfil do usuario</div>
                    </div>
                    <?php if (!empty($canEditProfile)): ?>
                        <a class="btn btn-outline-primary btn-sm" href="<?= base_path('/user/editar') ?>">Editor de perfil</a>
                    <?php endif; ?>
                </div>
                <div class="row g-2">
                    <div class="col-md-6 col-lg-4">
                        <div class="text-muted small">Email</div>
                        <div class="fw-medium text-break"><?= View::e((string)($user['email'] ?? '')) ?></div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="text-muted small">Telefone</div>
                            <div class="fw-medium"><?= View::e(phone_mask((string)($user['phone'] ?? ''))) ?></div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="text-muted small">Nascimento</div>
                        <div class="fw-medium"><?= View::e((string)($user['birth_date'] ?? '')) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h6 mb-0">Historico de compras e vouchers</h2>
                    <?php if (!empty($commerceHistoryMore)): ?>
                        <button class="btn btn-sm btn-link" type="button" data-bs-toggle="modal" data-bs-target="#paymentsModal">Ver mais</button>
                    <?php endif; ?>
                </div>
                <?php if (empty($commerceHistory)): ?>
                    <div class="text-muted">Sem registros.</div>
                <?php else: ?>
                    <div class="table-responsive overflow-auto" style="max-height: 320px;">
                        <table class="table table-sm align-middle mb-0 small">
                            <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 140px;">Data</th>
                                <th scope="col" class="text-center" style="width: 70px;">Tipo</th>
                                <th scope="col">Detalhes</th>
                                <th scope="col" class="text-center" style="width: 70px;">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($commerceHistory as $entry): ?>
                                <?php
                                $isPayment = (string)($entry['type'] ?? 'payment') === 'payment';
                                $dt = (string)($entry['date'] ?? '');
                                $date = $dt !== '' ? date('Y-m-d', strtotime($dt)) : '-';
                                $typeIcon = $isPayment ? 'bi-bag-check-fill text-primary' : 'bi-ticket-perforated-fill text-info';
                                $typeLabel = $isPayment ? 'Compra' : 'Voucher';
                                if ($isPayment) {
                                    $p = (array)($entry['payment'] ?? []);
                                    $pkg = $packageMap[(int)($p['package_id'] ?? 0)] ?? null;
                                    $pkgTitle = (string)($pkg['title'] ?? ('#' . (int)($p['package_id'] ?? 0)));
                                    $status = (string)($p['status'] ?? '');
                                    $reference = '#' . (int)($p['id'] ?? 0);
                                    $days = (int)($p['package_subscription_days'] ?? 0) * max(1, (int)($p['months'] ?? 1));
                                } else {
                                    $v = (array)($entry['voucher'] ?? []);
                                    $pkgTitle = (string)($v['package_title'] ?? '-');
                                    $status = 'approved';
                                    $reference = (string)($v['voucher_code'] ?? '-');
                                    $days = (int)($v['added_days'] ?? 0);
                                }
                                $statusIcon = 'bi-question-circle text-muted';
                                $statusLabel = 'Desconhecido';
                                if ($status === 'approved') {
                                    $statusIcon = 'bi-check-circle-fill text-success';
                                    $statusLabel = $isPayment ? 'Aprovado' : 'Ativado';
                                } elseif ($status === 'pending') {
                                    $statusIcon = 'bi-clock-fill text-warning';
                                    $statusLabel = 'Pendente';
                                } elseif ($status === 'rejected') {
                                    $statusIcon = 'bi-x-circle-fill text-danger';
                                    $statusLabel = 'Rejeitado';
                                } elseif ($status === 'revoked') {
                                    $statusIcon = 'bi-slash-circle-fill text-secondary';
                                    $statusLabel = 'Revogado';
                                }
                                ?>
                                <tr>
                                    <td><?= View::e($date) ?></td>
                                    <td class="text-center"><i class="bi <?= $typeIcon ?>" aria-hidden="true" title="<?= View::e($typeLabel) ?>"></i></td>
                                    <td>
                                        <div class="fw-medium"><?= View::e($pkgTitle) ?></div>
                                        <div class="text-muted"><?= View::e($reference) ?> · <?= $days > 0 ? ($days . ' dias') : '-' ?></div>
                                    </td>
                                    <td class="text-center">
                                        <i class="bi <?= $statusIcon ?>" aria-hidden="true" title="<?= View::e($statusLabel) ?>"></i>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h6 mb-0">Historico de acesso</h2>
                    <?php if (!empty($loginHistoryMore)): ?>
                        <button class="btn btn-sm btn-link" type="button" data-bs-toggle="modal" data-bs-target="#loginHistoryModal">Ver mais</button>
                    <?php endif; ?>
                </div>
                <?php if (empty($loginHistory)): ?>
                    <div class="text-muted">Sem registros.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0 small">
                            <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 140px;">Data</th>
                                <th scope="col" class="text-end" style="width: 110px;">Hora</th>
                                <th scope="col" class="text-end" style="width: 120px;">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($loginHistory as $log): ?>
                                <?php
                                $dt = (string)($log['logged_at'] ?? '');
                                $date = $dt !== '' ? date('Y-m-d', strtotime($dt)) : '-';
                                $time = $dt !== '' ? date('H:i:s', strtotime($dt)) : '-';
                                ?>
                                <tr>
                                    <td><?= View::e($date) ?></td>
                                    <td class="text-end"><?= View::e($time) ?></td>
                                    <td class="text-end"><span class="badge bg-success">Sucesso</span></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (!empty($loginFails ?? [])): ?>
                        <div class="mt-3">
                            <div class="text-muted small mb-2">Tentativas de acesso indevido</div>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0 small">
                                    <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 140px;">Data</th>
                                        <th scope="col" style="width: 110px;" class="text-end">Hora</th>
                                        <th scope="col">IP</th>
                                        <th scope="col" style="width: 120px;" class="text-end">Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($loginFails as $fail): ?>
                                        <?php
                                        $dt = (string)($fail['created_at'] ?? '');
                                        $date = $dt !== '' ? date('Y-m-d', strtotime($dt)) : '-';
                                        $time = $dt !== '' ? date('H:i:s', strtotime($dt)) : '-';
                                        $ip = (string)($fail['ip'] ?? '');
                                        ?>
                                        <tr>
                                            <td><?= View::e($date) ?></td>
                                            <td class="text-end"><?= View::e($time) ?></td>
                                            <td><?= View::e($ip !== '' ? $ip : '-') ?></td>
                                            <td class="text-end"><span class="badge bg-danger">Falhou</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h6 mb-0">Historico de leitura</h2>
                    <span class="badge bg-light text-muted border">Pagina <?= (int)($readPage ?? 1) ?> de <?= (int)($readPages ?? 1) ?></span>
                </div>
                <?php if (empty($readingHistory)): ?>
                    <div class="text-muted">Sem registros.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0 small">
                            <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 140px;">Data</th>
                                <th scope="col" style="width: 110px;">Hora</th>
                                <th scope="col">Conteudo</th>
                                <th scope="col" style="width: 180px;">Serie</th>
                                <th scope="col" style="width: 180px;">Categoria</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($readingHistory as $item): ?>
                                <?php
                                $dt = (string)($item['created_at'] ?? '');
                                $date = $dt !== '' ? date('Y-m-d', strtotime($dt)) : '-';
                                $time = $dt !== '' ? date('H:i:s', strtotime($dt)) : '-';
                                ?>
                                <tr>
                                    <td><?= View::e($date) ?></td>
                                    <td><?= View::e($time) ?></td>
                                    <td><?= View::e((string)($item['title'] ?? '')) ?></td>
                                    <td><?= View::e((string)($item['series_name'] ?? '')) ?></td>
                                    <td><?= View::e((string)($item['category_name'] ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                        $readPage = (int)($readPage ?? 1);
                        $readPages = (int)($readPages ?? 1);
                        $hasPrev = $readPage > 1;
                        $hasNext = $readPage < $readPages;
                    ?>
                    <?php if ($readPages > 1): ?>
                        <nav class="mt-3" aria-label="Paginacao de leitura">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item <?= $hasPrev ? '' : 'disabled' ?>">
                                    <a class="page-link" href="<?= base_path($profileBase . '?reads_page=' . max(1, $readPage - 1)) ?>" aria-label="Anterior">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <?php for ($p = 1; $p <= $readPages; $p++): ?>
                                    <li class="page-item <?= $p === $readPage ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= base_path($profileBase . '?reads_page=' . $p) ?>"><?= $p ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $hasNext ? '' : 'disabled' ?>">
                                    <a class="page-link" href="<?= base_path($profileBase . '?reads_page=' . min($readPages, $readPage + 1)) ?>" aria-label="Proxima">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($commerceHistoryMore)): ?>
<div class="modal fade" id="paymentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historico de compras e vouchers (restante)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive overflow-auto" style="max-height: 420px;">
                    <table class="table table-sm align-middle mb-0 small">
                        <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 140px;">Data</th>
                            <th scope="col" class="text-center" style="width: 70px;">Tipo</th>
                            <th scope="col">Detalhes</th>
                            <th scope="col" class="text-center" style="width: 70px;">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($commerceHistoryMore as $entry): ?>
                            <?php
                            $isPayment = (string)($entry['type'] ?? 'payment') === 'payment';
                            $dt = (string)($entry['date'] ?? '');
                            $date = $dt !== '' ? date('Y-m-d', strtotime($dt)) : '-';
                            $typeIcon = $isPayment ? 'bi-bag-check-fill text-primary' : 'bi-ticket-perforated-fill text-info';
                            $typeLabel = $isPayment ? 'Compra' : 'Voucher';
                            if ($isPayment) {
                                $p = (array)($entry['payment'] ?? []);
                                $pkg = $packageMap[(int)($p['package_id'] ?? 0)] ?? null;
                                $pkgTitle = (string)($pkg['title'] ?? ('#' . (int)($p['package_id'] ?? 0)));
                                $status = (string)($p['status'] ?? '');
                                $reference = '#' . (int)($p['id'] ?? 0);
                                $days = (int)($p['package_subscription_days'] ?? 0) * max(1, (int)($p['months'] ?? 1));
                            } else {
                                $v = (array)($entry['voucher'] ?? []);
                                $pkgTitle = (string)($v['package_title'] ?? '-');
                                $status = 'approved';
                                $reference = (string)($v['voucher_code'] ?? '-');
                                $days = (int)($v['added_days'] ?? 0);
                            }
                            $statusIcon = 'bi-question-circle text-muted';
                            $statusLabel = 'Desconhecido';
                            if ($status === 'approved') {
                                $statusIcon = 'bi-check-circle-fill text-success';
                                $statusLabel = $isPayment ? 'Aprovado' : 'Ativado';
                            } elseif ($status === 'pending') {
                                $statusIcon = 'bi-clock-fill text-warning';
                                $statusLabel = 'Pendente';
                            } elseif ($status === 'rejected') {
                                $statusIcon = 'bi-x-circle-fill text-danger';
                                $statusLabel = 'Rejeitado';
                            } elseif ($status === 'revoked') {
                                $statusIcon = 'bi-slash-circle-fill text-secondary';
                                $statusLabel = 'Revogado';
                            }
                            ?>
                            <tr>
                                <td><?= View::e($date) ?></td>
                                <td class="text-center"><i class="bi <?= $typeIcon ?>" aria-hidden="true" title="<?= View::e($typeLabel) ?>"></i></td>
                                <td>
                                    <div class="fw-medium"><?= View::e($pkgTitle) ?></div>
                                    <div class="text-muted"><?= View::e($reference) ?> · <?= $days > 0 ? ($days . ' dias') : '-' ?></div>
                                </td>
                                <td class="text-center">
                                    <i class="bi <?= $statusIcon ?>" aria-hidden="true" title="<?= View::e($statusLabel) ?>"></i>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($loginHistoryMore)): ?>
<div class="modal fade" id="loginHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historico de acesso (restante)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0 small">
                        <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 140px;">Data</th>
                            <th scope="col" class="text-end" style="width: 110px;">Hora</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($loginHistoryMore as $log): ?>
                            <?php
                            $dt = (string)($log['logged_at'] ?? '');
                            $date = $dt !== '' ? date('Y-m-d', strtotime($dt)) : '-';
                            $time = $dt !== '' ? date('H:i:s', strtotime($dt)) : '-';
                            ?>
                            <tr>
                                <td><?= View::e($date) ?></td>
                                <td class="text-end"><?= View::e($time) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
