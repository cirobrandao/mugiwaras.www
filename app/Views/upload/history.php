<?php
use App\Core\View;
ob_start();

$statusBadgeMap = [
    'pending' => 'bg-warning text-dark',
    'queued' => 'bg-secondary',
    'processing' => 'bg-info text-dark',
    'done' => 'bg-success',
    'completed' => 'bg-success',
    'failed' => 'bg-danger',
];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Meus uploads</h1>
    <a class="btn btn-sm btn-primary" href="<?= upload_url('/upload') ?>">Enviar arquivo</a>
</div>
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
        <tr>
            <th scope="col">Arquivo</th>
            <th scope="col" style="width: 190px;">Status</th>
            <th scope="col">Destino</th>
            <th scope="col" style="width: 170px;">Data</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (($uploads ?? []) as $u): ?>
            <tr>
                <td><?= View::e($u['original_name']) ?></td>
                <td>
                    <?php
                    $st = (string)($u['status'] ?? '');
                    $label = match ($st) {
                        'pending' => 'Pendente de aprovação',
                        'queued' => 'Na fila de conversão',
                        'processing' => 'Processando',
                        'done', 'completed' => 'Liberado',
                        'failed' => 'Falhou',
                        default => $st,
                    };
                    $badgeClass = $statusBadgeMap[$st] ?? 'bg-secondary';
                    ?>
                    <span class="badge <?= View::e($badgeClass) ?>">
                        <?= View::e($label) ?>
                    </span>
                </td>
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
