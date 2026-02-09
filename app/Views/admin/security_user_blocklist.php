<?php
use App\Core\View;
ob_start();
$status = (string)($_GET['status'] ?? '');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <h1 class="h4 mb-1">Blocklist de usuarios</h1>
        <div class="text-muted small">Impede cadastro com nomes desta lista.</div>
    </div>
    <a class="btn btn-sm btn-outline-secondary" href="<?= base_path('/admin/security/email-blocklist') ?>">Email blocklist</a>
</div>
<hr class="text-success" />
<?php if ($status === 'created'): ?>
    <div class="alert alert-success">Usuario bloqueado.</div>
<?php elseif ($status === 'exists'): ?>
    <div class="alert alert-warning">Usuario ja esta bloqueado.</div>
<?php elseif ($status === 'notfound'): ?>
    <div class="alert alert-danger">Nome invalido.</div>
<?php elseif ($status === 'invalid'): ?>
    <div class="alert alert-danger">Nome invalido.</div>
<?php elseif ($status === 'removed'): ?>
    <div class="alert alert-success">Bloqueio removido.</div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <h2 class="h6">Bloquear nome de usuario</h2>
        <form method="post" action="<?= base_path('/admin/security/user-blocklist/add') ?>" class="row g-2">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
            <div class="col-md-4">
                <label class="form-label">Nome de usuario</label>
                <input class="form-control" name="identifier" placeholder="username" required>
                <div class="form-text">Apenas letras, numeros, _ ou .</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Motivo (opcional)</label>
                <input class="form-control" name="reason" placeholder="Uso interno">
            </div>
            <div class="col-md-2 d-grid align-self-end">
                <button class="btn btn-primary" type="submit">Bloquear</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h2 class="h6 mb-0">Usuarios bloqueados</h2>
            <span class="badge bg-light text-muted border"><?= count($userBlocks ?? []) ?> registros</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Usuario</th>
                    <th>Criado em</th>
                    <th class="text-end">Acoes</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($userBlocks)): ?>
                    <tr><td colspan="3" class="text-muted">Nenhum usuario bloqueado.</td></tr>
                <?php else: ?>
                    <?php foreach (($userBlocks ?? []) as $b): ?>
                        <tr>
                            <td><?= View::e((string)($b['username'] ?? '')) ?></td>
                            <td><?= View::e((string)($b['created_at'] ?? '-')) ?></td>
                            <td class="text-end">
                                <form method="post" action="<?= base_path('/admin/security/user-blocklist/remove') ?>" class="d-inline">
                                    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                                    <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Remover</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
