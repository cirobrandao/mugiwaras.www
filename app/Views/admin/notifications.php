<?php
use App\Core\View;
ob_start();

$items = (array)($items ?? []);
$priorityLabel = [
    'high' => ['label' => 'Alta', 'class' => 'danger'],
    'medium' => ['label' => 'Media', 'class' => 'warning text-dark'],
    'low' => ['label' => 'Baixa', 'class' => 'info text-dark'],
];
?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Notificações</h1>
</div>
<hr class="text-success" />

<?php if (!empty($_GET['created'])): ?>
    <div class="alert alert-success">Notificação criada.</div>
<?php elseif (!empty($_GET['updated'])): ?>
    <div class="alert alert-success">Notificação atualizada.</div>
<?php elseif (!empty($_GET['deleted'])): ?>
    <div class="alert alert-success">Notificação removida.</div>
<?php elseif (!empty($_GET['error']) && $_GET['error'] === 'csrf'): ?>
    <div class="alert alert-danger">Token inválido.</div>
<?php elseif (!empty($_GET['error'])): ?>
    <div class="alert alert-danger">Preencha título e conteúdo.</div>
<?php endif; ?>

<div class="card border-0 bg-body-tertiary mb-3">
    <div class="card-body">
        <h2 class="h6 mb-3">Nova notificação</h2>
        <form method="post" action="<?= base_path('/admin/notifications/save') ?>" class="row g-3">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
            <div class="col-md-6">
                <label class="form-label">Título</label>
                <input class="form-control" name="title" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Prioridade</label>
                <select class="form-select" name="priority">
                    <option value="high">Alta (vermelha)</option>
                    <option value="medium">Média (amarela)</option>
                    <option value="low" selected>Baixa (azul)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActiveNew" value="1" checked>
                    <label class="form-check-label" for="isActiveNew">Ativa</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">Mensagem</label>
                <textarea class="form-control" name="body" rows="3" required></textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">Início (opcional)</label>
                <input class="form-control" type="datetime-local" name="starts_at">
            </div>
            <div class="col-md-3">
                <label class="form-label">Fim (opcional)</label>
                <input class="form-control" type="datetime-local" name="ends_at">
            </div>
            <div class="col-md-3 d-grid align-items-end">
                <button class="btn btn-primary" type="submit">Salvar</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
        <tr>
            <th>Título</th>
            <th class="table-col-lg">Prioridade</th>
            <th class="table-col-md">Status</th>
            <th class="table-col-xl">Período</th>
            <th class="text-end table-col-2xl">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <?php $meta = $priorityLabel[(string)($item['priority'] ?? 'low')] ?? $priorityLabel['low']; ?>
            <tr>
                <td>
                    <div class="fw-semibold"><?= View::e((string)$item['title']) ?></div>
                    <div class="small text-muted"><?= View::e((string)$item['body']) ?></div>
                </td>
                <td><span class="badge bg-<?= View::e($meta['class']) ?>"><?= View::e($meta['label']) ?></span></td>
                <td>
                    <?php if (!empty($item['is_active'])): ?>
                        <span class="badge bg-success">Ativa</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inativa</span>
                    <?php endif; ?>
                </td>
                <td class="small text-muted">
                    <?= View::e((string)($item['starts_at'] ?? '-')) ?><br>
                    <?= View::e((string)($item['ends_at'] ?? '-')) ?>
                </td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#editNotif<?= (int)$item['id'] ?>">Editar</button>
                    <form method="post" action="<?= base_path('/admin/notifications/delete') ?>" class="d-inline" onsubmit="return confirm('Excluir notificação?');">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                    </form>
                </td>
            </tr>
            <tr class="collapse" id="editNotif<?= (int)$item['id'] ?>">
                <td colspan="5" class="bg-body-tertiary">
                    <form method="post" action="<?= base_path('/admin/notifications/save') ?>" class="row g-2">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                        <div class="col-md-4">
                            <input class="form-control form-control-sm" name="title" value="<?= View::e((string)$item['title']) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm" name="priority">
                                <option value="high" <?= (string)$item['priority'] === 'high' ? 'selected' : '' ?>>Alta</option>
                                <option value="medium" <?= (string)$item['priority'] === 'medium' ? 'selected' : '' ?>>Média</option>
                                <option value="low" <?= (string)$item['priority'] === 'low' ? 'selected' : '' ?>>Baixa</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control form-control-sm" type="datetime-local" name="starts_at" value="<?= !empty($item['starts_at']) ? View::e(date('Y-m-d\\TH:i', strtotime((string)$item['starts_at']))) : '' ?>">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control form-control-sm" type="datetime-local" name="ends_at" value="<?= !empty($item['ends_at']) ? View::e(date('Y-m-d\\TH:i', strtotime((string)$item['ends_at']))) : '' ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-center gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active<?= (int)$item['id'] ?>" <?= !empty($item['is_active']) ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="active<?= (int)$item['id'] ?>">Ativa</label>
                            </div>
                            <button class="btn btn-sm btn-primary" type="submit">Salvar</button>
                        </div>
                        <div class="col-12">
                            <textarea class="form-control form-control-sm" name="body" rows="2" required><?= View::e((string)$item['body']) ?></textarea>
                        </div>
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
