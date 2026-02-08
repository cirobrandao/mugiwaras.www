<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Meu perfil</h1>
<div class="card">
    <div class="card-body">
        <div class="row g-3 align-items-start">
            <div class="col-auto">
                <div class="border rounded d-flex align-items-center justify-content-center profile-avatar-box" style="background: #f8f9fa;">
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
                    <?php if (($user['access_tier'] ?? '') !== 'restrito'): ?>
                        <a class="btn btn-outline-primary btn-sm" href="<?= base_path('/perfil/editar') ?>">Editor de perfil</a>
                    <?php endif; ?>
                </div>
                <div class="row g-2">
                    <div class="col-md-6 col-lg-4">
                        <div class="text-muted small">Email</div>
                        <div class="fw-medium text-break"><?= View::e((string)($user['email'] ?? '')) ?></div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="text-muted small">Telefone</div>
                        <div class="fw-medium"><?= View::e((string)($user['phone'] ?? '')) ?></div>
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
                    <h2 class="h6 mb-0">Historico de compras</h2>
                    <?php if (!empty($paymentsMore)): ?>
                        <button class="btn btn-sm btn-link" type="button" data-bs-toggle="modal" data-bs-target="#paymentsModal">Ver mais</button>
                    <?php endif; ?>
                </div>
                <?php if (empty($payments)): ?>
                    <div class="text-muted">Sem registros.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0 small">
                            <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 140px;">Data</th>
                                <th scope="col">Pacote</th>
                                <th scope="col" class="text-center" style="width: 90px;">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($payments as $p): ?>
                                <?php
                                $dt = (string)($p['created_at'] ?? '');
                                $date = $dt !== '' ? date('Y-m-d', strtotime($dt)) : '-';
                                $pkg = $packageMap[(int)($p['package_id'] ?? 0)] ?? null;
                                $status = (string)($p['status'] ?? '');
                                $statusLabel = '';
                                $statusIcon = '';
                                if ($status === 'approved') {
                                    $statusIcon = 'fa-circle-check text-success';
                                    $statusLabel = 'Aprovado';
                                } elseif ($status === 'pending') {
                                    $statusIcon = 'fa-clock text-warning';
                                    $statusLabel = 'Pendente';
                                } elseif ($status === 'rejected') {
                                    $statusIcon = 'fa-circle-xmark text-danger';
                                    $statusLabel = 'Rejeitado';
                                }
                                ?>
                                <tr>
                                    <td><?= View::e($date) ?></td>
                                    <td><?= View::e((string)($pkg['title'] ?? ('#' . (int)($p['package_id'] ?? 0)))) ?></td>
                                    <td class="text-center">
                                        <?php if ($statusIcon !== ''): ?>
                                            <i class="fa-solid <?= $statusIcon ?> me-1" aria-hidden="true"></i>
                                        <?php endif; ?>
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
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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
                                    <a class="page-link" href="<?= base_path('/perfil?reads_page=' . max(1, $readPage - 1)) ?>" aria-label="Anterior">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <?php for ($p = 1; $p <= $readPages; $p++): ?>
                                    <li class="page-item <?= $p === $readPage ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= base_path('/perfil?reads_page=' . $p) ?>"><?= $p ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $hasNext ? '' : 'disabled' ?>">
                                    <a class="page-link" href="<?= base_path('/perfil?reads_page=' . min($readPages, $readPage + 1)) ?>" aria-label="Proxima">
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

<?php if (!empty($paymentsMore)): ?>
<div class="modal fade" id="paymentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historico de compras (restante)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0 small">
                        <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 140px;">Data</th>
                            <th scope="col">Pacote</th>
                            <th scope="col" class="text-center" style="width: 90px;">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($paymentsMore as $p): ?>
                            <?php
                            $dt = (string)($p['created_at'] ?? '');
                            $date = $dt !== '' ? date('Y-m-d', strtotime($dt)) : '-';
                            $pkg = $packageMap[(int)($p['package_id'] ?? 0)] ?? null;
                            $status = (string)($p['status'] ?? '');
                            $statusIcon = '';
                            if ($status === 'approved') {
                                $statusIcon = 'fa-circle-check text-success';
                            } elseif ($status === 'pending') {
                                $statusIcon = 'fa-clock text-warning';
                            } elseif ($status === 'rejected') {
                                $statusIcon = 'fa-circle-xmark text-danger';
                            }
                            ?>
                            <tr>
                                <td><?= View::e($date) ?></td>
                                <td><?= View::e((string)($pkg['title'] ?? ('#' . (int)($p['package_id'] ?? 0)))) ?></td>
                                <td class="text-center">
                                    <?php if ($statusIcon !== ''): ?>
                                        <i class="fa-solid <?= $statusIcon ?>" aria-hidden="true"></i>
                                    <?php endif; ?>
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
