<?php
declare(strict_types=1);

use App\Core\View;

$pageTitle = 'Criar Grupo de Séries - Admin';
ob_start();
?>

<div class="admin-section">
    <div class="admin-section-header">
        <div>
            <h1 class="admin-section-title">Criar Grupo de Séries</h1>
            <p class="admin-section-subtitle">Agrupe séries relacionadas em uma única seção</p>
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

    <div class="card">
        <div class="card-body">
            <form method="post" action="<?= base_path('/admin/series-groups/store') ?>">
                <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">

                <div class="mb-3">
                    <label for="category_id" class="form-label required">Categoria</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>" <?= (isset($formData['category_id']) && (int)$formData['category_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                                <?= View::e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Selecione a categoria onde este grupo aparecerá</div>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label required">Nome do Grupo</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?= View::e($formData['name'] ?? '') ?>" 
                           placeholder="Ex: Naruto - Série Completa" 
                           required maxlength="255">
                    <div class="form-text">Nome que aparecerá no cabeçalho do grupo</div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descrição (opcional)</label>
                    <textarea class="form-control" id="description" name="description" 
                              rows="3" maxlength="500"><?= View::e($formData['description'] ?? '') ?></textarea>
                    <div class="form-text">Descrição opcional que aparecerá abaixo do nome</div>
                </div>

                <div class="mb-3">
                    <label for="display_order" class="form-label">Ordem de Exibição</label>
                    <input type="number" class="form-control" id="display_order" name="display_order" 
                           value="<?= View::e($formData['display_order'] ?? '0') ?>" 
                           min="0" style="max-width: 150px;">
                    <div class="form-text">Grupos com menor número aparecem primeiro (0 = primeiro)</div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_collapsed" name="is_collapsed" value="1"
                               <?= !empty($formData['is_collapsed']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_collapsed">
                            Iniciar colapsado (fechado)
                        </label>
                    </div>
                    <div class="form-text">Se marcado, o grupo aparecerá fechado por padrão</div>
                </div>

                <hr class="my-4">

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Após criar o grupo, você poderá adicionar séries a ele na página de edição.
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?= base_path('/admin/series-groups') ?>" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Criar Grupo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-label.required::after {
    content: " *";
    color: #dc3545;
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
