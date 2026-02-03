<?php
use App\Core\View;
ob_start();
$labelMap = [
    'open' => 'Aberto',
    'in_progress' => 'Em andamento',
    'closed' => 'Fechado',
];
?>
<h1 class="h4 mb-3">Chamado #<?= str_pad((string)(int)$ticket['id'], 4, '0', STR_PAD_LEFT) ?> - <?= View::e((string)$ticket['subject']) ?></h1>
<div class="mb-3">
    <div class="d-flex align-items-center justify-content-end gap-3">
        <div>
            <strong class="me-2">Status:</strong>
            <span class="badge fs-6 px-3 py-2 <?= ($ticket['status'] ?? 'open') === 'open' ? 'bg-danger' : (($ticket['status'] ?? 'open') === 'in_progress' ? 'bg-warning text-dark' : 'bg-success') ?>">
                <?= View::e($labelMap[$ticket['status'] ?? 'open'] ?? 'Aberto') ?>
            </span>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="small text-muted mb-2"><?= View::e((string)($ticket['created_at'] ?? '')) ?></div>
        <div class="mb-2"><strong>Mensagem inicial</strong></div>
        <div><?= nl2br(View::e((string)$ticket['message'])) ?></div>
        <?php if (!empty($ticket['attachment_path'])): ?>
            <div class="mt-2">
                <a href="<?= base_path('/' . ltrim((string)$ticket['attachment_path'], '/')) ?>" target="_blank">Anexo</a>
                <?php if (!empty($ticket['attachment_name'])): ?>
                    <span class="text-muted">(<?= View::e((string)$ticket['attachment_name']) ?>)</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($replies)): ?>
    <div class="mb-3">
        <?php foreach ($replies as $r): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <div class="small text-muted mb-2"><?= View::e((string)($r['created_at'] ?? '')) ?></div>
                    <div class="mb-2">
                        <strong><?= !empty($r['admin_id']) ? 'Equipe' : 'Você' ?></strong>
                    </div>
                    <div><?= nl2br(View::e((string)$r['message'])) ?></div>
                    <?php if (!empty($r['attachment_path'])): ?>
                        <div class="mt-2">
                            <a href="<?= base_path('/' . ltrim((string)$r['attachment_path'], '/')) ?>" target="_blank">Anexo</a>
                            <?php if (!empty($r['attachment_name'])): ?>
                                <span class="text-muted">(<?= View::e((string)$r['attachment_name']) ?>)</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (!empty($_GET['error']) && $_GET['error'] === 'wait'): ?>
            <div class="alert alert-warning">Aguarde a resposta de um atendente para continuar.</div>
        <?php endif; ?>
        <?php if (!empty($_GET['error']) && $_GET['error'] === 'message'): ?>
            <div class="alert alert-danger">Digite a mensagem.</div>
        <?php endif; ?>
        <?php if (!empty($canReply)): ?>
            <h2 class="h6">Responder</h2>
            <form method="post" action="<?= base_path('/support/' . (int)$ticket['id'] . '/reply') ?>" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                <div class="mb-3">
                    <label class="form-label">Mensagem</label>
                    <textarea name="message" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Anexo (jpg, png, webp ou pdf)</label>
                    <input type="file" name="attachment" class="form-control" accept="image/*,application/pdf">
                </div>
                <button class="btn btn-primary" type="submit">Enviar resposta</button>
            </form>
        <?php else: ?>
            <div class="text-muted">Aguardando resposta do atendente.</div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
