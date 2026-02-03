<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Uploads (Admin)</h1>
<div class="table-responsive">
    <table class="table table-sm">
        <thead>
        <tr>
            <th>Usuário</th>
            <th>Arquivo</th>
            <th>Status</th>
            <th>Origem</th>
            <th>Destino</th>
            <th>Data</th>
            <th class="text-end">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (($uploads ?? []) as $u): ?>
            <tr>
                <td><?= (int)$u['user_id'] ?></td>
                <td><?= View::e($u['original_name']) ?></td>
                <td><?= View::e($u['status']) ?></td>
                <td><?= View::e((string)$u['source_path']) ?></td>
                <td><?= View::e((string)$u['target_path']) ?></td>
                <td><?= View::e((string)$u['created_at']) ?></td>
                <td class="text-end">
                    <form method="post" action="<?= base_path('/admin/uploads/delete') ?>" class="d-inline">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
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
