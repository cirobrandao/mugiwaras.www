<?php
declare(strict_types=1);

use App\Core\View;

$pageTitle = 'Editar Grupo de Séries - Admin';
$groupId = (int)$group['id'];
ob_start();
?>

<div class="admin-section">
    <div class="admin-section-header">
        <div>
            <h1 class="admin-section-title">Editar Grupo de Séries</h1>
            <p class="admin-section-subtitle"><?= View::e($group['name']) ?></p>
        </div>
        <a href="<?= base_path('/admin/series-groups') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Erros encontrados:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $error): ?>
                    <li><?= View::e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= View::e($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <!-- Edit Group Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-pencil"></i> Informações do Grupo</h5>
        </div>
        <div class="card-body">
            <form method="post" action="<?= base_path('/admin/series-groups/' . $groupId . '/update') ?>">
                <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">

                <div class="mb-3">
                    <label for="category_id" class="form-label required">Categoria</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>" 
                                <?= (int)$cat['id'] === (int)$group['category_id'] ? 'selected' : '' ?>>
                                <?= View::e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label required">Nome do Grupo</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?= View::e($group['name']) ?>" 
                           required maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descrição (opcional)</label>
                    <textarea class="form-control" id="description" name="description" 
                              rows="3" maxlength="500"><?= View::e($group['description'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="display_order" class="form-label">Ordem de Exibição</label>
                    <input type="number" class="form-control" id="display_order" name="display_order" 
                           value="<?= (int)$group['display_order'] ?>" 
                           min="0" style="max-width: 150px;">
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_collapsed" name="is_collapsed" value="1"
                               <?= !empty($group['is_collapsed']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_collapsed">
                            Iniciar colapsado (fechado)
                        </label>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Manage Series -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-collection"></i> Gerenciar Séries do Grupo</h5>
        </div>
        <div class="card-body">
            <?php if (empty($groupSeries) && empty($availableSeries)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Nenhuma série disponível nesta categoria.
                </div>
            <?php else: ?>
                <!-- Current Series in Group -->
                <?php if (!empty($groupSeries)): ?>
                    <div class="mb-4">
                        <h6 class="mb-3">Séries no Grupo <span class="badge bg-primary"><?= count($groupSeries) ?></span></h6>
                        <div class="list-group">
                            <?php foreach ($groupSeries as $series): ?>
                                <?php $seriesId = (int)$series['id']; ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= View::e($series['name']) ?></strong>
                                        <div class="small text-muted">
                                            CBZ: <?= (int)($series['cbz_count'] ?? 0) ?> | 
                                            PDF: <?= (int)($series['pdf_count'] ?? 0) ?> | 
                                            EPUB: <?= (int)($series['epub_count'] ?? 0) ?>
                                        </div>
                                    </div>
                                    <form method="post" action="<?= base_path('/admin/series-groups/' . $groupId . '/remove-series') ?>" class="m-0">
                                        <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                        <input type="hidden" name="series_id" value="<?= $seriesId ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                title="Remover do grupo">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-4">
                        <i class="bi bi-exclamation-triangle"></i> Nenhuma série adicionada ao grupo ainda.
                    </div>
                <?php endif; ?>

                <!-- Available Series to Add -->
                <?php if (!empty($availableSeries)): ?>
                    <hr class="my-4">
                    <div>
                        <h6 class="mb-3">Adicionar Série ao Grupo</h6>
                        <form method="post" action="<?= base_path('/admin/series-groups/' . $groupId . '/add-series') ?>" 
                              class="d-flex gap-2 align-items-start">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                            <div class="flex-grow-1">
                                <select class="form-select" name="series_id" required>
                                    <option value="">Selecione uma série...</option>
                                    <?php foreach ($availableSeries as $series): ?>
                                        <option value="<?= (int)$series['id'] ?>">
                                            <?= View::e($series['name']) ?> 
                                            (CBZ: <?= (int)($series['cbz_count'] ?? 0) ?>, PDF: <?= (int)($series['pdf_count'] ?? 0) ?>, EPUB: <?= (int)($series['epub_count'] ?? 0) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Adicionar
                            </button>
                        </form>
                        <div class="form-text mt-2">
                            <i class="bi bi-info-circle"></i> Apenas séries da mesma categoria que não estão em outros grupos
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.form-label.required::after {
    content: " *";
    color: #dc3545;
}
.list-group-item {
    transition: all 0.2s ease;
}
.list-group-item:hover {
    background-color: rgba(102, 126, 234, 0.05);
    border-color: rgba(102, 126, 234, 0.3);
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
