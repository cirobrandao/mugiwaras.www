<?php
use App\Core\View;
ob_start();
$modals = [];
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Notícias</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#newsCategoryModal">Categorias</button>
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#newsCreateModal">Adicionar</button>
    </div>
</div>

<?php if (!empty($_GET['created'])): ?>
    <div class="alert alert-success">Notícia criada.</div>
<?php elseif (!empty($_GET['updated'])): ?>
    <div class="alert alert-success">Notícia atualizada.</div>
<?php elseif (!empty($_GET['deleted'])): ?>
    <div class="alert alert-success">Notícia removida.</div>
<?php elseif (!empty($_GET['error']) && $_GET['error'] === 'category'): ?>
    <div class="alert alert-danger">Selecione uma categoria válida.</div>
<?php elseif (!empty($_GET['error'])): ?>
    <div class="alert alert-danger">Preencha título e conteúdo.</div>
<?php endif; ?>

<?php if (!empty($_GET['category_created'])): ?>
    <div class="alert alert-success">Categoria criada.</div>
<?php elseif (!empty($_GET['category_updated'])): ?>
    <div class="alert alert-success">Categoria atualizada.</div>
<?php elseif (!empty($_GET['category_deleted'])): ?>
    <div class="alert alert-success">Categoria removida.</div>
<?php endif; ?>

<div class="modal fade" id="newsCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Categorias de notícias</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_path('/admin/news/category/create') ?>" class="row g-3">
                    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                    <div class="col-md-6">
                        <label class="form-label">Nome</label>
                        <input class="form-control" name="name" required>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="show_sidebar" id="catSidebar" value="1" checked>
                            <label class="form-check-label" for="catSidebar">Mostrar na lateral</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="show_below_most_read" id="catBelow" value="1">
                            <label class="form-check-label" for="catBelow">Abaixo das mais lidas</label>
                        </div>
                    </div>
                    <div class="col-md-3 d-grid">
                        <button class="btn btn-primary" type="submit">Adicionar</button>
                    </div>
                </form>

                <div class="table-responsive mt-3">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col" style="width: 220px;">Exibição</th>
                            <th scope="col" class="text-end" style="width: 220px;">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach (($categories ?? []) as $cat): ?>
                            <tr>
                                <td>
                                    <input class="form-control" form="cat-<?= (int)$cat['id'] ?>" name="name" value="<?= View::e((string)$cat['name']) ?>">
                                </td>
                                <td class="small">
                                    <?php if (!empty($cat['show_sidebar'])): ?><span class="badge bg-secondary me-1">Lateral</span><?php endif; ?>
                                    <?php if (!empty($cat['show_below_most_read'])): ?><span class="badge bg-info text-dark">Abaixo mais lidas</span><?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form id="cat-<?= (int)$cat['id'] ?>" method="post" action="<?= base_path('/admin/news/category/update') ?>" class="d-inline">
                                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                                        <input type="hidden" name="show_sidebar" value="0">
                                        <input type="hidden" name="show_below_most_read" value="0">
                                        <div class="form-check form-check-inline me-2">
                                            <input class="form-check-input" type="checkbox" name="show_sidebar" value="1" <?= !empty($cat['show_sidebar']) ? 'checked' : '' ?> id="catSide<?= (int)$cat['id'] ?>">
                                            <label class="form-check-label" for="catSide<?= (int)$cat['id'] ?>">Lateral</label>
                                        </div>
                                        <div class="form-check form-check-inline me-2">
                                            <input class="form-check-input" type="checkbox" name="show_below_most_read" value="1" <?= !empty($cat['show_below_most_read']) ? 'checked' : '' ?> id="catBelow<?= (int)$cat['id'] ?>">
                                            <label class="form-check-label" for="catBelow<?= (int)$cat['id'] ?>">Abaixo</label>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Salvar</button>
                                    </form>
                                    <form method="post" action="<?= base_path('/admin/news/category/delete') ?>" class="d-inline">
                                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newsCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar notícia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_path('/admin/news/create') ?>">
                    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <select class="form-select" name="category_id" required>
                            <option value="">Selecionar</option>
                            <?php foreach (($categories ?? []) as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>"><?= View::e((string)$cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input class="form-control" type="text" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Conteúdo</label>
                        <textarea class="form-control" name="body" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Publicado em (opcional)</label>
                        <input class="form-control" type="datetime-local" name="published_at">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_published" id="newsPublished" value="1">
                        <label class="form-check-label" for="newsPublished">Publicar</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="publish_now" id="newsPublishNow" value="1">
                        <label class="form-check-label" for="newsPublishNow">Publicar agora</label>
                    </div>
                    <button class="btn btn-primary" type="submit">Criar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
        <tr>
            <th scope="col">Título</th>
            <th scope="col" style="width: 200px;">Categoria</th>
            <th scope="col" style="width: 140px;">Status</th>
            <th scope="col" style="width: 180px;">Publicação</th>
            <th scope="col" class="text-end" style="width: 180px;">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (($items ?? []) as $n): ?>
            <tr>
                <td><?= View::e($n['title']) ?></td>
                <td><?= View::e((string)($n['category_name'] ?? '-')) ?></td>
                <td>
                    <?php if (!empty($n['is_published'])): ?>
                        <span class="badge bg-success">Publicado</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Rascunho</span>
                    <?php endif; ?>
                </td>
                <td><?= View::e((string)($n['published_at'] ?? '')) ?></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#editNews<?= (int)$n['id'] ?>">Editar</button>
                    <form method="post" action="<?= base_path('/admin/news/delete') ?>" class="d-inline">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                    </form>
                </td>
            </tr>
            <?php
            ob_start();
            ?>
            <div class="modal fade" id="editNews<?= (int)$n['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar notícia</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="<?= base_path('/admin/news/update') ?>">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                                <div class="mb-3">
                                    <label class="form-label">Categoria</label>
                                    <select class="form-select" name="category_id" required>
                                        <?php foreach (($categories ?? []) as $cat): ?>
                                            <option value="<?= (int)$cat['id'] ?>" <?= (int)$cat['id'] === (int)($n['category_id'] ?? 0) ? 'selected' : '' ?>><?= View::e((string)$cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Título</label>
                                    <input class="form-control" type="text" name="title" value="<?= View::e($n['title']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Conteúdo</label>
                                    <textarea class="form-control" name="body" rows="4" required><?= View::e($n['body']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Publicado em</label>
                                    <input class="form-control" type="datetime-local" name="published_at" value="<?= View::e((string)($n['published_at'] ?? '')) ?>">
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_published" id="newsPublished<?= (int)$n['id'] ?>" value="1" <?= !empty($n['is_published']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="newsPublished<?= (int)$n['id'] ?>">Publicar</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="publish_now" id="newsPublishNow<?= (int)$n['id'] ?>" value="1">
                                    <label class="form-check-label" for="newsPublishNow<?= (int)$n['id'] ?>">Publicar agora</label>
                                </div>
                                <button class="btn btn-sm btn-primary" type="submit">Salvar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $modals[] = ob_get_clean();
            ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
if (!empty($modals)) {
    $content .= implode('', $modals);
}
require __DIR__ . '/../layout.php';
