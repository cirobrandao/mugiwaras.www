<?php
use App\Core\View;
ob_start();

$query = (string)($query ?? '');
$items = (array)($items ?? []);
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Logs de IP</h1>
</div>
<hr class="text-success" />

<form method="get" action="<?= base_path('/admin/log') ?>" class="row g-2 align-items-end mb-3">
    <div class="col-md-8 col-lg-6">
        <label class="form-label">Pesquisar por IP ou usuário</label>
        <input type="text" name="q" class="form-control" value="<?= View::e($query) ?>" placeholder="Ex.: 187.45.10.2 ou usuario" required>
    </div>
    <div class="col-auto d-flex gap-2">
        <button class="btn btn-primary" type="submit">Pesquisar</button>
        <a class="btn btn-outline-secondary" href="<?= base_path('/admin/log') ?>">Limpar</a>
    </div>
</form>

<?php if ($query !== '' && empty($items)): ?>
    <div class="alert alert-warning">Nenhum usuário encontrado para o termo informado.</div>
<?php endif; ?>

<?php if (!empty($items)): ?>
    <div class="table-responsive">
        <table class="table table-sm table-striped align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Email</th>
                <th>Último IP</th>
                <th>Último login</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $row): ?>
                <tr>
                    <td><?= (int)($row['id'] ?? 0) ?></td>
                    <td><?= View::e((string)($row['username'] ?? '')) ?></td>
                    <td><?= View::e((string)($row['email'] ?? '')) ?></td>
                    <td><code><?= View::e((string)($row['ip_ultimo_acesso'] ?? '-')) ?></code></td>
                    <td><?= View::e((string)($row['data_ultimo_login'] ?? '-')) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
