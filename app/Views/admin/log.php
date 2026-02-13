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
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Logs de acesso</h1>
</div>
<hr class="text-success" />

<form method="get" action="<?= base_path('/admin/log') ?>" class="row g-2 align-items-end mb-3">
    <div class="col-md-6 col-lg-5">
        <label class="form-label">Pesquisar</label>
        <input type="text" name="q" class="form-control" value="<?= View::e($query) ?>" placeholder="IP, usuário, email, user agent, ID usuário/log">
    </div>
    <div class="col-md-2 col-lg-2">
        <label class="form-label">Por página</label>
        <select name="perPage" class="form-select">
            <?php foreach ([50, 100, 200, 500] as $pp): ?>
                <option value="<?= $pp ?>" <?= $perPage === $pp ? 'selected' : '' ?>><?= $pp ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto d-flex gap-2">
        <button class="btn btn-primary" type="submit">Pesquisar</button>
        <a class="btn btn-outline-secondary" href="<?= base_path('/admin/log') ?>">Limpar</a>
    </div>
</form>

<div class="small text-muted mb-3">Total de logs encontrados: <?= $total ?></div>

<?php if (empty($items)): ?>
    <div class="alert alert-warning">Nenhum log de acesso encontrado.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-sm table-striped align-middle">
            <thead>
            <tr>
                <th>Usuário</th>
                <th>Perfil</th>
                <th>IP</th>
                <th>User agent</th>
                <th>Data/hora acesso</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $i => $row): ?>
                <tr>
                    <td>
                        <button class="btn btn-link p-0 text-decoration-none" type="button" data-bs-toggle="modal" data-bs-target="#userInfoModal<?= (int)$i ?>">
                            <?= View::e((string)($row['username'] ?? '')) ?>
                        </button>
                    </td>
                    <td>
                        <span class="badge bg-secondary"><?= View::e((string)($row['role'] ?? '-')) ?></span>
                        <span class="badge bg-info text-dark"><?= View::e((string)($row['access_tier'] ?? '-')) ?></span>
                    </td>
                    <td><code><?= View::e((string)($row['ip_address'] ?? '-')) ?></code></td>
                    <td class="small" style="min-width: 280px;"><?= View::e((string)($row['user_agent'] ?? '-')) ?></td>
                    <td><?= View::e((string)($row['logged_at'] ?? '-')) ?></td>
                </tr>
                <?php
                $modals[] = '<div class="modal fade" id="userInfoModal' . (int)$i . '" tabindex="-1" aria-hidden="true">'
                    . '<div class="modal-dialog modal-dialog-centered">'
                    . '<div class="modal-content">'
                    . '<div class="modal-header">'
                    . '<h5 class="modal-title">Informações do usuário</h5>'
                    . '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>'
                    . '</div>'
                    . '<div class="modal-body small">'
                    . '<div class="mb-2"><strong>ID:</strong> ' . (int)($row['user_id'] ?? 0) . '</div>'
                    . '<div class="mb-2"><strong>Usuário:</strong> ' . View::e((string)($row['username'] ?? '-')) . '</div>'
                    . '<div class="mb-2"><strong>Email:</strong> ' . View::e((string)($row['email'] ?? '-')) . '</div>'
                    . '<div class="mb-2"><strong>Perfil:</strong> ' . View::e((string)($row['role'] ?? '-')) . '</div>'
                    . '<div class="mb-2"><strong>Acesso:</strong> ' . View::e((string)($row['access_tier'] ?? '-')) . '</div>'
                    . '<div class="mb-2"><strong>IP do acesso:</strong> ' . View::e((string)($row['ip_address'] ?? '-')) . '</div>'
                    . '<div class="mb-0"><strong>Data/hora:</strong> ' . View::e((string)($row['logged_at'] ?? '-')) . '</div>'
                    . '</div>'
                    . '<div class="modal-footer">'
                    . '<a class="btn btn-sm btn-outline-primary" href="' . base_path('/perfil/' . rawurlencode((string)($row['username'] ?? ''))) . '">Abrir perfil</a>'
                    . '<button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Fechar</button>'
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
        <nav>
            <ul class="pagination pagination-sm">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= base_path('/admin/log?' . $baseQ . '&page=1') ?>">«</a></li>
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= base_path('/admin/log?' . $baseQ . '&page=' . $prev) ?>">‹</a></li>
                <?php for ($p = $start; $p <= $end; $p++): ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>"><a class="page-link" href="<?= base_path('/admin/log?' . $baseQ . '&page=' . $p) ?>"><?= $p ?></a></li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>"><a class="page-link" href="<?= base_path('/admin/log?' . $baseQ . '&page=' . $next) ?>">›</a></li>
                <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>"><a class="page-link" href="<?= base_path('/admin/log?' . $baseQ . '&page=' . $pages) ?>">»</a></li>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
