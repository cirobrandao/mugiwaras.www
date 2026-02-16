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
?>
<h1 class="h4 mb-3 mt-0">Chamado #<?= str_pad((string)(int)$ticket['id'], 4, '0', STR_PAD_LEFT) ?> - <?= View::e((string)$ticket['subject']) ?></h1>
<hr class="text-success mb-3" />

<!-- Informações do ticket -->
<div class="card mb-3">
    <div class="card-body p-3">
        <div class="row g-3 align-items-center mb-3">
            <div class="col-md-4">
                <small class="text-muted d-block mb-1">Email</small>
                <strong><?= View::e((string)$ticket['email']) ?></strong>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block mb-1">Status</small>
                <form method="post" action="<?= base_path('/admin/support/status') ?>" class="d-inline-flex gap-2">
                    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                    <input type="hidden" name="id" value="<?= (int)$ticket['id'] ?>">
                    <select name="status" class="form-select form-select-sm" style="max-width: 180px;">
                        <option value="open" <?= ($ticket['status'] ?? 'open') === 'open' ? 'selected' : '' ?>>Aberto</option>
                        <option value="in_progress" <?= ($ticket['status'] ?? 'open') === 'in_progress' ? 'selected' : '' ?>>Em andamento</option>
                        <option value="closed" <?= ($ticket['status'] ?? 'open') === 'closed' ? 'selected' : '' ?>>Fechado</option>
                    </select>
                    <button class="btn btn-sm btn-primary" type="submit">
                        <i class="bi bi-check-lg"></i>
                    </button>
                </form>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block mb-1">Data</small>
                <strong><?= View::e((string)($ticket['created_at'] ?? '')) ?></strong>
            </div>
        </div>
        
        <?php if (!empty($ticket['whatsapp_opt_in'])): ?>
        <div class="mb-3 pb-3 border-bottom">
            <small class="text-muted d-block mb-1">WhatsApp</small>
            <strong><?= View::e((string)($ticket['whatsapp_number'] ?? '')) ?></strong>
        </div>
        <?php endif; ?>
        
        <div>
            <form method="post" action="<?= base_path('/admin/support/note') ?>" class="d-flex gap-2 align-items-end">
                <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                <input type="hidden" name="id" value="<?= (int)$ticket['id'] ?>">
                <div class="flex-grow-1">
                    <label class="form-label small text-muted mb-1">Nota interna (visível apenas para admins)</label>
                    <input type="text" name="admin_note" class="form-control form-control-sm" value="<?= View::e((string)($ticket['admin_note'] ?? '')) ?>" placeholder="Adicione uma nota...">
                </div>
                <button class="btn btn-sm btn-primary" type="submit" title="Salvar nota">
                    <i class="bi bi-floppy"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Mensagem inicial -->
<div class="mb-3">
    <h2 class="h6 mb-2">
        <i class="bi bi-envelope me-1"></i> Mensagem inicial
    </h2>
    <div class="card">
        <div class="card-body p-3">
            <div class="small text-muted mb-2">
                <i class="bi bi-clock me-1"></i><?= View::e((string)($ticket['created_at'] ?? '')) ?>
            </div>
            <div><?= nl2br(View::e((string)$ticket['message'])) ?></div>
            <?php if (!empty($ticket['attachment_path'])): ?>
                <div class="mt-3 pt-3 border-top">
                    <a href="<?= base_path('/' . ltrim((string)$ticket['attachment_path'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-paperclip me-1"></i> Anexo
                        <?php if (!empty($ticket['attachment_name'])): ?>
                            (<?= View::e((string)$ticket['attachment_name']) ?>)
                        <?php endif; ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Respostas -->
<?php if (!empty($replies)): ?>
<div class="mb-3">
    <h2 class="h6 mb-2">
        <i class="bi bi-chat-dots me-1"></i> Histórico (<?= count($replies) ?> <?= count($replies) === 1 ? 'resposta' : 'respostas' ?>)
    </h2>
    <div class="replies-container">
        <?php $lastReplyId = (int)($replies[count($replies) - 1]['id'] ?? 0); ?>
        <?php foreach ($replies as $index => $r): ?>
            <?php $isLast = ((int)($r['id'] ?? 0) === $lastReplyId); ?>
            <?php $isAdmin = !empty($r['admin_id']); ?>
            <div class="card mb-2 <?= $isLast ? 'border-success' : '' ?>">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge <?= $isAdmin ? 'bg-primary' : 'bg-secondary' ?> me-2">
                                <i class="bi <?= $isAdmin ? 'bi-headset' : 'bi-person' ?> me-1"></i>
                                <?= $isAdmin ? 'Equipe' : 'Usuário' ?>
                            </span>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i><?= View::e((string)($r['created_at'] ?? '')) ?>
                            </small>
                        </div>
                        <?php if ($isLast): ?>
                            <span class="badge bg-success-subtle text-success small">Última resposta</span>
                        <?php endif; ?>
                    </div>
                    <div><?= nl2br(View::e((string)$r['message'])) ?></div>
                    <?php if (!empty($r['attachment_path'])): ?>
                        <div class="mt-2 pt-2 border-top">
                            <a href="<?= base_path('/' . ltrim((string)$r['attachment_path'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-paperclip me-1"></i> Anexo
                                <?php if (!empty($r['attachment_name'])): ?>
                                    (<?= View::e((string)$r['attachment_name']) ?>)
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Formulário de resposta -->
<div class="card border-primary">
    <div class="card-header bg-transparent">
        <h2 class="h6 mb-0">
            <i class="bi bi-reply me-1"></i> Responder
        </h2>
    </div>
    <div class="card-body p-3">
        <?php if (!empty($_GET['error']) && $_GET['error'] === 'wait_user'): ?>
            <div class="alert alert-warning alert-sm mb-3">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Aguarde a resposta do usuário antes de enviar outra mensagem.
            </div>
        <?php endif; ?>
        <?php if (!empty($_GET['error']) && $_GET['error'] === 'final'): ?>
            <div class="alert alert-warning alert-sm mb-3">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Não é possível fechar sem resposta final do atendente.
            </div>
        <?php endif; ?>
        <?php if (!empty($_GET['error']) && $_GET['error'] === 'message'): ?>
            <div class="alert alert-danger alert-sm mb-3">
                <i class="bi bi-x-circle me-1"></i>
                Digite a mensagem.
            </div>
        <?php endif; ?>
        <form method="post" action="<?= base_path('/admin/support/' . (int)$ticket['id'] . '/reply') ?>" enctype="multipart/form-data">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
            <div class="mb-3">
                <label class="form-label small">Mensagem</label>
                <textarea name="message" class="form-control" rows="4" required placeholder="Digite sua resposta..."></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label small">Anexo <span class="text-muted">(opcional: jpg, png, webp ou pdf)</span></label>
                <input type="file" name="attachment" class="form-control form-control-sm" accept="image/*,application/pdf">
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-send me-1"></i> Enviar resposta
                </button>
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal" aria-label="Fechar">
                    <i class="bi bi-x-lg me-1"></i> Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();

// Check if AJAX request for modal - multiple checks for compatibility
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
          (!empty($_GET['ajax']) && $_GET['ajax'] === '1');

if ($isAjax) {
    // Return only content without layout
    echo $content;
} else {
    // Return with full layout
    require __DIR__ . '/../layout.php';
}
