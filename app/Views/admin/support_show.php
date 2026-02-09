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
<hr class="text-success" />

<div class="mb-3">
    <div class="d-flex flex-wrap align-items-center gap-3">
        <div><strong>Email:</strong> <?= View::e((string)$ticket['email']) ?></div>
        <form method="post" action="<?= base_path('/admin/support/status') ?>" class="d-flex align-items-center gap-2 ms-auto">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
            <input type="hidden" name="id" value="<?= (int)$ticket['id'] ?>">
            <select name="status" class="form-select form-select-sm">
                <option value="open" <?= ($ticket['status'] ?? 'open') === 'open' ? 'selected' : '' ?>>Aberto</option>
                <option value="in_progress" <?= ($ticket['status'] ?? 'open') === 'in_progress' ? 'selected' : '' ?>>Em andamento</option>
                <option value="closed" <?= ($ticket['status'] ?? 'open') === 'closed' ? 'selected' : '' ?>>Fechado</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary" type="submit" style="min-width: 220px;">Atualizar</button>
        </form>
    </div>
    <?php if (!empty($ticket['whatsapp_opt_in'])): ?>
        <div><strong>WhatsApp:</strong> <?= View::e((string)($ticket['whatsapp_number'] ?? '')) ?></div>
    <?php endif; ?>
    <div class="mt-2">
        <strong>Nota admin:</strong>
        <form method="post" action="<?= base_path('/admin/support/note') ?>" class="d-flex gap-2 mt-1">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
            <input type="hidden" name="id" value="<?= (int)$ticket['id'] ?>">
            <input type="text" name="admin_note" class="form-control form-control-sm" value="<?= View::e((string)($ticket['admin_note'] ?? '')) ?>" placeholder="Nota">
            <button class="btn btn-sm btn-primary" type="submit" title="Salvar">
                <i class="fa-solid fa-floppy-disk"></i>
            </button>
        </form>
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
        <?php $lastReplyId = (int)($replies[count($replies) - 1]['id'] ?? 0); ?>
        <?php foreach ($replies as $r): ?>
            <div class="card mb-2 <?= ((int)($r['id'] ?? 0) === $lastReplyId) ? 'border border-success' : '' ?>">
                <div class="card-body">
                    <div class="small text-muted mb-2"><?= View::e((string)($r['created_at'] ?? '')) ?></div>
                    <div class="mb-2">
                        <strong><?= !empty($r['admin_id']) ? 'Equipe' : 'Usuário' ?></strong>
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
        <h2 class="h6">Responder</h2>
        <?php if (!empty($_GET['error']) && $_GET['error'] === 'wait_user'): ?>
            <div class="alert alert-warning">Aguarde a resposta do usuário antes de enviar outra mensagem.</div>
        <?php endif; ?>
        <?php if (!empty($_GET['error']) && $_GET['error'] === 'final'): ?>
            <div class="alert alert-warning">Não é possível fechar sem resposta final do atendente.</div>
        <?php endif; ?>
        <?php if (!empty($_GET['error']) && $_GET['error'] === 'message'): ?>
            <div class="alert alert-danger">Digite a mensagem.</div>
        <?php endif; ?>
        <form method="post" action="<?= base_path('/admin/support/' . (int)$ticket['id'] . '/reply') ?>" enctype="multipart/form-data" class="mb-3">
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

        
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
