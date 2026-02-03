<?php
use App\Core\View;
ob_start();
$labelMap = [
    'open' => 'Aberto',
    'in_progress' => 'Em andamento',
    'closed' => 'Fechado',
];
?>
<h1 class="h4 mb-3">Suporte</h1>
<?php if (!empty($_GET['sent'])): ?>
    <div class="alert alert-success">Chamado criado com sucesso.</div>
<?php endif; ?>
<?php if (!empty($_GET['restricted'])): ?>
    <div class="alert alert-warning">Seu acesso ao conteúdo está restrito. Suporte disponível.</div>
<?php endif; ?>

<?php if (empty($hasOpen)): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h6">Abrir novo chamado</h2>
            <form method="post" action="<?= base_path('/support') ?>" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                <div class="mb-3">
                    <label class="form-label">Assunto</label>
                    <input type="text" name="subject" class="form-control" required maxlength="120">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mensagem</label>
                    <textarea name="message" class="form-control" rows="5" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Anexo (jpg, png, webp ou pdf)</label>
                    <input type="file" name="attachment" class="form-control" accept="image/*,application/pdf">
                </div>
                <button class="btn btn-primary" type="submit">Enviar</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">Você já possui um chamado em aberto. Acompanhe abaixo.</div>
<?php endif; ?>

<h2 class="h6">Meus chamados</h2>
<?php if (empty($messages)): ?>
    <div class="alert alert-secondary">Você ainda não abriu chamados.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
            <tr>
                <th>ID</th>
                <th>Assunto</th>
                <th>Status</th>
                <th>Atualizado</th>
                <th class="text-end">Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($messages as $m): ?>
                <?php $mId = (int)($m['id'] ?? 0); ?>
                <?php $isClosed = ($m['status'] ?? 'open') === 'closed'; ?>
                <?php $attention = !empty($needsAttention[$mId]); ?>
                <tr class="<?= $isClosed ? 'table-light' : ($attention ? 'table-warning' : '') ?>">
                    <td><?= (int)$m['id'] ?></td>
                    <td>
                        <?= View::e($m['subject']) ?>
                        <?php if ($attention): ?>
                            <span class="badge bg-danger ms-2">Nova resposta</span>
                        <?php endif; ?>
                    </td>
                    <td><?= View::e($labelMap[$m['status'] ?? 'open'] ?? 'Aberto') ?></td>
                    <td><?= View::e((string)($m['updated_at'] ?? $m['created_at'] ?? '')) ?></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="<?= base_path('/support/' . (int)$m['id']) ?>">Ver</a>
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
