<?php
use App\Core\View;
ob_start();

$query = (string)($query ?? '');
$items = (array)($items ?? []);
$page = (int)($page ?? 1);
$pages = (int)($pages ?? 1);
$perPage = (int)($perPage ?? 100);
$total = (int)($total ?? count($items));
$modals = [];
?>

<div class="admin-log">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">
            <i class="bi bi-journal-text me-2"></i>Logs de acesso
        </h1>
    </div>

    <form method="get" action="<?= base_path('/admin/log') ?>" class="row g-2 align-items-end mb-3">
        <div class="col-md-6 col-lg-5">
            <label class="form-label">
                <i class="bi bi-search me-1"></i>Pesquisar
            </label>
            <input type="text" name="q" class="form-control" value="<?= View::e($query) ?>" placeholder="IP, usuário, email, user agent, ID">
        </div>
        <div class="col-md-2 col-lg-2">
            <label class="form-label">
                <i class="bi bi-list-ol me-1"></i>Por página
            </label>
            <select name="perPage" class="form-select">
                <?php foreach ([50, 100, 200, 500] as $pp): ?>
                    <option value="<?= $pp ?>" <?= $perPage === $pp ? 'selected' : '' ?>><?= $pp ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-search me-1"></i>Pesquisar
            </button>
            <a class="btn btn-outline-secondary" href="<?= base_path('/admin/log') ?>">
                <i class="bi bi-x-circle me-1"></i>Limpar
            </a>
        </div>
    </form>

    <div class="small text-muted mb-3">
        <i class="bi bi-info-circle me-1"></i>Total de logs encontrados: <strong><?= $total ?></strong>
    </div>

    <?php if (empty($items)): ?>
        <div class="alert alert-info mb-0">
            <i class="bi bi-inbox me-2"></i>Nenhum log de acesso encontrado.
        </div>
    <?php else: ?>
        <div class="admin-log-table">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th style="width: 180px;"><i class="bi bi-person me-1"></i>Usuário</th>
                    <th style="width: 160px;"><i class="bi bi-shield me-1"></i>Perfil</th>
                    <th style="width: 140px;"><i class="bi bi-hdd-network me-1"></i>IP</th>
                    <th><i class="bi bi-browser-chrome me-1"></i>User agent</th>
                    <th style="width: 170px;"><i class="bi bi-clock-history me-1"></i>Data/hora</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $i => $row): ?>
                    <tr>
                        <td>
                            <button class="btn btn-link p-0 text-decoration-none text-start" type="button" data-bs-toggle="modal" data-bs-target="#userInfoModal<?= (int)$i ?>">
                                <i class="bi bi-person-circle me-1"></i><?= View::e((string)($row['username'] ?? '')) ?>
                            </button>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= View::e((string)($row['role'] ?? '-')) ?></span>
                            <span class="badge bg-info"><?= View::e((string)($row['access_tier'] ?? '-')) ?></span>
                        </td>
                        <td><code class="small"><?= View::e((string)($row['ip_address'] ?? '-')) ?></code></td>
                        <td class="small text-truncate" style="max-width: 350px;"><?= View::e((string)($row['user_agent'] ?? '-')) ?></td>
                        <td class="small"><?= View::e((string)($row['logged_at'] ?? '-')) ?></td>
                    </tr>
                    <?php
                    $modals[] = '<div class="modal fade admin-log-modal" id="userInfoModal' . (int)$i . '" tabindex="-1" aria-hidden="true">'
                        . '<div class="modal-dialog modal-dialog-centered">'
                        . '<div class="modal-content">'
                        . '<div class="modal-header bg-gradient text-white">'
                        . '<h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Informações do usuário</h5>'
                        . '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>'
                        . '</div>'
                        . '<div class="modal-body">'
                        . '<div class="info-row"><span class="info-label"><i class="bi bi-hash"></i> ID:</span><span class="info-value">' . (int)($row['user_id'] ?? 0) . '</span></div>'
                        . '<div class="info-row"><span class="info-label"><i class="bi bi-person"></i> Usuário:</span><span class="info-value">' . View::e((string)($row['username'] ?? '-')) . '</span></div>'
                        . '<div class="info-row"><span class="info-label"><i class="bi bi-envelope"></i> Email:</span><span class="info-value">' . View::e((string)($row['email'] ?? '-')) . '</span></div>'
                        . '<div class="info-row"><span class="info-label"><i class="bi bi-shield"></i> Perfil:</span><span class="info-value">' . View::e((string)($row['role'] ?? '-')) . '</span></div>'
                        . '<div class="info-row"><span class="info-label"><i class="bi bi-key"></i> Acesso:</span><span class="info-value">' . View::e((string)($row['access_tier'] ?? '-')) . '</span></div>'
                        . '<div class="info-row"><span class="info-label"><i class="bi bi-hdd-network"></i> IP:</span><span class="info-value"><code>' . View::e((string)($row['ip_address'] ?? '-')) . '</code></span></div>'
                        . '<div class="info-row"><span class="info-label"><i class="bi bi-clock"></i> Data/hora:</span><span class="info-value">' . View::e((string)($row['logged_at'] ?? '-')) . '</span></div>'
                        . '</div>'
                        . '<div class="modal-footer">'
                        . '<a class="btn btn-sm btn-outline-primary" href="' . base_path('/perfil/' . rawurlencode((string)($row['username'] ?? ''))) . '"><i class="bi bi-box-arrow-up-right me-1"></i>Abrir perfil</a>'
                        . '<button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Fechar</button>'
                        . '</div>'
                        . '</div>'
                        . '</div>'
                        . '</div>';
                    ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($modals)): ?>
            <?php foreach ($modals as $modal): ?>
                <?= $modal ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($pages > 1): ?>
            <?php
            $prev = max(1, $page - 1);
            $next = min($pages, $page + 1);
            $start = max(1, $page - 2);
            $end = min($pages, $page + 2);
            $baseQ = 'q=' . urlencode($query) . '&perPage=' . $perPage;
            ?>
            <nav class="mt-3">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= base_path('/admin/log?' . $baseQ . '&page=1') ?>"><i class="bi bi-chevron-double-left"></i></a>
                    </li>
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= base_path('/admin/log?' . $baseQ . '&page=' . $prev) ?>"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <?php for ($p = $start; $p <= $end; $p++): ?>
                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= base_path('/admin/log?' . $baseQ . '&page=' . $p) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= base_path('/admin/log?' . $baseQ . '&page=' . $next) ?>"><i class="bi bi-chevron-right"></i></a>
                    </li>
                    <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= base_path('/admin/log?' . $baseQ . '&page=' . $pages) ?>"><i class="bi bi-chevron-double-right"></i></a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
