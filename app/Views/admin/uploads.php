<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Gerenciador de Arquivos enviados</h1>
<div class="table-responsive">
    <table class="table table-sm">
        <thead>
        <tr>
            <th></th>
            <th>Arquivo</th>
            <th>Origem</th>
            <th>Destino</th>
            <th>Data</th>
            <th>Usuário</th>
            <th class="text-end">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (($uploads ?? []) as $u): ?>
            <tr>
                <td>
                    <?php
                    $st = (string)($u['status'] ?? '');
                    $icon = 'fa-circle'; $cls = 'text-secondary';
                    if ($st === 'queued' || $st === 'pending') { $icon = 'fa-clock'; $cls = 'text-muted'; }
                    elseif ($st === 'processing') { $icon = 'fa-spinner fa-spin'; $cls = 'text-primary'; }
                    elseif ($st === 'done' || $st === 'completed') { $icon = 'fa-check-circle'; $cls = 'text-success'; }
                    elseif ($st === 'failed') { $icon = 'fa-triangle-exclamation'; $cls = 'text-danger'; }
                    ?>
                    <i class="fa-solid <?= $icon ?> <?= $cls ?>" title="<?= View::e($st) ?>"></i>
                </td>
                <td><?= View::e($u['original_name']) ?></td>
                <td><?= View::e((string)$u['source_path']) ?></td>
                <td><?= View::e((string)$u['target_path']) ?></td>
                <td><?= View::e((string)$u['created_at']) ?></td>
                <td><?= View::e($u['username_display'] ?? ('#' . (int)$u['user_id'])) ?></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-secondary me-1" type="button" title="Editar">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span class="visually-hidden">Editar</span>
                    </button>
                    <form method="post" action="<?= base_path('/admin/uploads/delete') ?>" class="d-inline">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit" title="Excluir">
                            <i class="fa-solid fa-trash"></i>
                            <span class="visually-hidden">Excluir</span>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php if (!empty($total)): ?>
    <?php $pages = (int)ceil($total / ($perPage ?? 20)); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div class="small text-muted">Total uploads: <?= (int)$total ?></div>
        <nav aria-label="pag" class="mb-3">
            <ul class="pagination pagination-sm mb-0">
                <?php for ($p = 1; $p <= $pages; $p++): ?>
                    <li class="page-item <?= ($p === ($page ?? 1)) ? 'active' : '' ?>"><a class="page-link" href="<?= base_path('/admin/uploads?page=' . $p) ?>"><?= $p ?></a></li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
