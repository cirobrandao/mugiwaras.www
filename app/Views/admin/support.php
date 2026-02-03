<?php
use App\Core\View;
ob_start();
$labelMap = [
    'open' => 'Aberto',
    'in_progress' => 'Em andamento',
    'closed' => 'Fechado',
];
$badgeMap = [
    'open' => 'bg-secondary',
    'in_progress' => 'bg-warning text-dark',
    'closed' => 'bg-success',
];

$openMessages = [];
$closedMessages = [];
foreach ($messages ?? [] as $m) {
    if (($m['status'] ?? 'open') === 'closed') {
        $closedMessages[] = $m;
    } else {
        $openMessages[] = $m;
    }
}
?>
<h1 class="h4 mb-3">Suporte</h1>
<?php if (empty($messages)): ?>
    <div class="alert alert-secondary">Sem mensagens.</div>
<?php else: ?>
    <h2 class="h6 mb-2">Abertos / Em andamento</h2>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th style="width: 180px;">Usuário</th>
                <th>Assunto</th>
                <th style="width: 140px;">Status</th>
                <th style="width: 220px;">Nota admin</th>
                <th class="text-end" style="width: 180px;">Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($openMessages)): ?>
                <tr><td colspan="6" class="text-muted">Sem chamados em aberto.</td></tr>
            <?php endif; ?>
            <?php foreach ($openMessages as $m): ?>
                <tr class="<?= ($m['status'] ?? 'open') === 'in_progress' ? 'table-warning' : 'table-secondary' ?>">
                    <td><?= (int)$m['id'] ?></td>
                    <td>
                        <?php if (!empty($m['username'])): ?>
                            <?= View::e((string)$m['username']) ?>
                        <?php else: ?>
                            <?php $externalName = explode('@', (string)($m['email'] ?? ''))[0] ?? ''; ?>
                            <span class="text-danger me-1" title="Usuário externo"><i class="fa-solid fa-flag"></i></span>
                            <?= View::e($externalName) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_path('/admin/support/' . (int)$m['id']) ?>" class="text-decoration-none">
                            <?= View::e(mb_strimwidth((string)$m['subject'], 0, 50, '…')) ?>
                        </a>
                    </td>
                    <td>
                        <span class="badge <?= $badgeMap[$m['status'] ?? 'open'] ?? 'bg-secondary' ?>">
                            <?= View::e($labelMap[$m['status'] ?? 'open'] ?? 'Aberto') ?>
                        </span>
                    </td>
                    <td><?= View::e((string)($m['admin_note'] ?? '')) ?></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="<?= base_path('/admin/support/' . (int)$m['id']) ?>" title="Ver">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <form method="post" action="<?= base_path('/admin/support/status') ?>" class="d-inline">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                            <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                            <input type="hidden" name="status" value="in_progress">
                            <button class="btn btn-sm btn-outline-secondary" type="submit" title="Em andamento">
                                <i class="fa-solid fa-clock"></i>
                            </button>
                        </form>
                        <form method="post" action="<?= base_path('/admin/support/status') ?>" class="d-inline">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                            <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                            <input type="hidden" name="status" value="closed">
                            <button class="btn btn-sm btn-outline-success" type="submit" title="Fechar">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h2 class="h6 mt-4 mb-2">Fechados</h2>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th style="width: 180px;">Usuário</th>
                <th>Assunto</th>
                <th style="width: 140px;">Status</th>
                <th style="width: 220px;">Nota admin</th>
                <th class="text-end" style="width: 180px;">Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($closedMessages)): ?>
                <tr><td colspan="6" class="text-muted">Sem chamados fechados.</td></tr>
            <?php endif; ?>
            <?php foreach ($closedMessages as $m): ?>
                <tr class="table-light">
                    <td><?= (int)$m['id'] ?></td>
                    <td>
                        <?php if (!empty($m['username'])): ?>
                            <?= View::e((string)$m['username']) ?>
                        <?php else: ?>
                            <?php $externalName = explode('@', (string)($m['email'] ?? ''))[0] ?? ''; ?>
                            <span class="text-danger me-1" title="Usuário externo"><i class="fa-solid fa-flag"></i></span>
                            <?= View::e($externalName) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_path('/admin/support/' . (int)$m['id']) ?>" class="text-decoration-none">
                            <?= View::e(mb_strimwidth((string)$m['subject'], 0, 50, '…')) ?>
                        </a>
                    </td>
                    <td>
                        <span class="badge <?= $badgeMap[$m['status'] ?? 'closed'] ?? 'bg-success' ?>">
                            <?= View::e($labelMap[$m['status'] ?? 'closed'] ?? 'Fechado') ?>
                        </span>
                    </td>
                    <td><?= View::e((string)($m['admin_note'] ?? '')) ?></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="<?= base_path('/admin/support/' . (int)$m['id']) ?>" title="Ver">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <form method="post" action="<?= base_path('/admin/support/status') ?>" class="d-inline">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                            <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                            <input type="hidden" name="status" value="open">
                            <button class="btn btn-sm btn-outline-secondary" type="submit" title="Reabrir">
                                <i class="fa-solid fa-arrow-rotate-left"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
