<?php
declare(strict_types=1);

use App\Core\View;
use App\Core\Auth;

$pageTitle = 'Grupos de Séries - Admin';
ob_start();
?>

<div class="admin-section">
    <div class="admin-section-header">
        <div>
            <h1 class="admin-section-title">Grupos de Séries</h1>
            <p class="admin-section-subtitle">Organize séries relacionadas em grupos</p>
        </div>
        <a href="<?= base_path('/admin/series-groups/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Grupo
        </a>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= View::e($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= View::e($_SESSION['flash_error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <?php if (empty($groups)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Nenhum grupo de séries criado ainda. 
            <a href="<?= base_path('/admin/series-groups/create') ?>">Criar primeiro grupo</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">Ordem</th>
                        <th>Grupo</th>
                        <th>Categoria</th>
                        <th style="width: 100px;" class="text-center">Séries</th>
                        <th style="width: 120px;" class="text-center">Status</th>
                        <th style="width: 180px;" class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groups as $group): ?>
                        <?php $groupId = (int)$group['id']; ?>
                        <?php $isCollapsed = !empty($group['is_collapsed']); ?>
                        <tr>
                            <td>
                                <form method="post" action="<?= base_path('/admin/series-groups/' . $groupId . '/reorder') ?>" class="d-inline">
                                    <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                    <input type="number" name="display_order" value="<?= (int)$group['display_order'] ?>" 
                                           class="form-control form-control-sm" style="width: 60px;" min="0"
                                           onchange="this.form.submit()">
                                </form>
                            </td>
                            <td>
                                <div>
                                    <strong class="d-block"><?= View::e($group['name']) ?></strong>
                                    <?php if (!empty($group['description'])): ?>
                                        <small class="text-muted"><?= View::e($group['description']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= View::e($group['category_name'] ?? 'N/A') ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info"><?= (int)($group['series_count'] ?? 0) ?></span>
                            </td>
                            <td class="text-center">
                                <form method="post" action="<?= base_path('/admin/series-groups/' . $groupId . '/toggle-collapsed') ?>" class="d-inline">
                                    <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                    <button type="submit" class="btn btn-sm <?= $isCollapsed ? 'btn-warning' : 'btn-success' ?>"
                                            title="<?= $isCollapsed ? 'Expandir por padrão' : 'Colapsar por padrão' ?>">
                                        <i class="bi <?= $isCollapsed ? 'bi-chevron-right' : 'bi-chevron-down' ?>"></i>
                                        <?= $isCollapsed ? 'Colapsado' : 'Expandido' ?>
                                    </button>
                                </form>
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="<?= base_path('/admin/series-groups/' . $groupId . '/edit') ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal<?= $groupId ?>"
                                            title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal<?= $groupId ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Excluir Grupo</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                    </div>
                                    <form method="post" action="<?= base_path('/admin/series-groups/' . $groupId . '/delete') ?>">
                                        <div class="modal-body">
                                            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                            <p>Tem certeza que deseja excluir o grupo <strong><?= View::e($group['name']) ?></strong>?</p>
                                            <p class="text-muted mb-0">
                                                <i class="bi bi-info-circle"></i> As séries não serão excluídas, apenas removidas do grupo.
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-danger">Excluir Grupo</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
