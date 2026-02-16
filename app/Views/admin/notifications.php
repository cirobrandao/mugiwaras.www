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

<div class="admin-notifications">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">
            <i class="bi bi-bell me-2"></i>Notificações
        </h1>
    </div>

    <?php if (!empty($_GET['created'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>Notificação criada.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($_GET['updated'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>Notificação atualizada.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($_GET['deleted'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>Notificação removida.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($_GET['error']) && $_GET['error'] === 'csrf'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Token inválido.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Preencha título e conteúdo.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-gradient text-white">
            <i class="bi bi-plus-circle me-2"></i>Nova notificação
        </div>
        <div class="card-body">
            <form method="post" action="<?= base_path('/admin/notifications/save') ?>" class="row g-3">
                <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-type me-1"></i>Título
                    </label>
                    <input class="form-control" name="title" placeholder="Digite o título da notificação" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="bi bi-exclamation-triangle me-1"></i>Prioridade
                    </label>
                    <select class="form-select" name="priority">
                        <option value="high">Alta (vermelha)</option>
                        <option value="medium">Média (amarela)</option>
                        <option value="low" selected>Baixa (azul)</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActiveNew" value="1" checked>
                        <label class="form-check-label" for="isActiveNew">
                            <i class="bi bi-toggle-on me-1"></i>Ativa
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">
                        <i class="bi bi-chat-text me-1"></i>Mensagem
                    </label>
                    <textarea class="form-control" name="body" rows="3" placeholder="Digite a mensagem da notificação" required></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="bi bi-calendar-check me-1"></i>Início (opcional)
                    </label>
                    <input class="form-control" type="datetime-local" name="starts_at">
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="bi bi-calendar-x me-1"></i>Fim (opcional)
                    </label>
                    <input class="form-control" type="datetime-local" name="ends_at">
                </div>
                <div class="col-md-4 d-grid align-items-end">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-circle me-1"></i>Criar notificação
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-notifications-table">
        <table class="table table-hover align-middle mb-0">
            <thead>
            <tr>
                <th style="width: 350px;"><i class="bi bi-card-heading me-1"></i>Título & Mensagem</th>
                <th style="width: 120px;"><i class="bi bi-flag me-1"></i>Prioridade</th>
                <th style="width: 100px;"><i class="bi bi-toggle-on me-1"></i>Status</th>
                <th style="width: 200px;"><i class="bi bi-calendar-range me-1"></i>Período</th>
                <th class="text-end" style="width: 180px;"><i class="bi bi-gear me-1"></i>Ações</th>
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
                <td class="text-end admin-actions">
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#editNotif<?= (int)$item['id'] ?>" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form method="post" action="<?= base_path('/admin/notifications/delete') ?>" class="d-inline" onsubmit="return confirm('Excluir notificação?');">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit" title="Excluir">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <tr class="collapse admin-edit-row" id="editNotif<?= (int)$item['id'] ?>">
                <td colspan="5" class="p-3">
                    <form method="post" action="<?= base_path('/admin/notifications/save') ?>" class="row g-3">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                        <div class="col-md-6">
                            <label class="form-label small">
                                <i class="bi bi-type me-1"></i>Título
                            </label>
                            <input class="form-control form-control-sm" name="title" value="<?= View::e((string)$item['title']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">
                                <i class="bi bi-flag me-1"></i>Prioridade
                            </label>
                            <select class="form-select form-select-sm" name="priority">
                                <option value="high" <?= (string)$item['priority'] === 'high' ? 'selected' : '' ?>>Alta</option>
                                <option value="medium" <?= (string)$item['priority'] === 'medium' ? 'selected' : '' ?>>Média</option>
                                <option value="low" <?= (string)$item['priority'] === 'low' ? 'selected' : '' ?>>Baixa</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active<?= (int)$item['id'] ?>" <?= !empty($item['is_active']) ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="active<?= (int)$item['id'] ?>">
                                    <i class="bi bi-toggle-on me-1"></i>Ativa
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">
                                <i class="bi bi-chat-text me-1"></i>Mensagem
                            </label>
                            <textarea class="form-control form-control-sm" name="body" rows="2" required><?= View::e((string)$item['body']) ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">
                                <i class="bi bi-calendar-check me-1"></i>Início
                            </label>
                            <input class="form-control form-control-sm" type="datetime-local" name="starts_at" value="<?= !empty($item['starts_at']) ? View::e(date('Y-m-d\\TH:i', strtotime((string)$item['starts_at']))) : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">
                                <i class="bi bi-calendar-x me-1"></i>Fim
                            </label>
                            <input class="form-control form-control-sm" type="datetime-local" name="ends_at" value="<?= !empty($item['ends_at']) ? View::e(date('Y-m-d\\TH:i', strtotime((string)$item['ends_at']))) : '' ?>">
                        </div>
                        <div class="col-md-4 d-grid align-items-end">
                            <button class="btn btn-sm btn-primary" type="submit">
                                <i class="bi bi-check-circle me-1"></i>Salvar alterações
                            </button>
                        </div>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
