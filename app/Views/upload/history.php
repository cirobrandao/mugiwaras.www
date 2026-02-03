<?php
use App\Core\View;
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Meus uploads</h1>
    <a class="btn btn-sm btn-primary" href="<?= base_path('/upload') ?>">Enviar arquivo</a>
</div>
<div class="table-responsive">
    <table class="table table-sm">
        <thead>
        <tr>
            <th>Arquivo</th>
            <th>Status</th>
            <th>Destino</th>
            <th>Data</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (($uploads ?? []) as $u): ?>
            <tr>
                <td><?= View::e($u['original_name']) ?></td>
                <td><?= View::e($u['status']) ?></td>
                <td><?= View::e((string)$u['target_path']) ?></td>
                <td><?= View::e((string)$u['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
