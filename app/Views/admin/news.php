<?php
use App\Core\View;
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Notícias</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#newsCategoryModal">Categorias</button>
        <a class="btn btn-primary" href="<?= base_path('/admin/news/create') ?>">Nova notícia</a>
    </div>
</div>
<hr class="text-success" />

<?php if (!empty($_GET['created'])): ?>
    <div class="alert alert-success">Notícia criada.</div>
<?php elseif (!empty($_GET['updated'])): ?>
    <div class="alert alert-success">Notícia atualizada.</div>
<?php elseif (!empty($_GET['deleted'])): ?>
    <div class="alert alert-success">Notícia removida.</div>
<?php elseif (!empty($_GET['error']) && $_GET['error'] === 'category'): ?>
    <div class="alert alert-danger">Selecione uma categoria válida.</div>
<?php elseif (!empty($_GET['error']) && $_GET['error'] === 'image'): ?>
    <div class="alert alert-danger">Imagem de destaque inválida. Use JPG, PNG ou WEBP até 4MB.</div>
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
                            <label class="form-check-label" for="catSidebar">Mostrar na Sidebar</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="show_below_most_read" id="catBelow" value="1">
                            <label class="form-check-label" for="catBelow">Mostrar na MainPage</label>
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
                                    <?php if (!empty($cat['show_sidebar'])): ?><span class="badge bg-secondary me-1">Sidebar</span><?php endif; ?>
                                    <?php if (!empty($cat['show_below_most_read'])): ?><span class="badge bg-info text-dark">MainPage</span><?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form id="cat-<?= (int)$cat['id'] ?>" method="post" action="<?= base_path('/admin/news/category/update') ?>" class="d-inline">
                                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                                        <input type="hidden" name="show_sidebar" value="0">
                                        <input type="hidden" name="show_below_most_read" value="0">
                                        <div class="form-check form-check-inline me-2">
                                            <input class="form-check-input" type="checkbox" name="show_sidebar" value="1" <?= !empty($cat['show_sidebar']) ? 'checked' : '' ?> id="catSide<?= (int)$cat['id'] ?>">
                                            <label class="form-check-label" for="catSide<?= (int)$cat['id'] ?>">Sidebar</label>
                                        </div>
                                        <div class="form-check form-check-inline me-2">
                                            <input class="form-check-input" type="checkbox" name="show_below_most_read" value="1" <?= !empty($cat['show_below_most_read']) ? 'checked' : '' ?> id="catBelow<?= (int)$cat['id'] ?>">
                                            <label class="form-check-label" for="catBelow<?= (int)$cat['id'] ?>">MainPage</label>
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

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
        <tr>
            <th scope="col">Título</th>
            <th scope="col" style="width: 200px;">Categoria</th>
            <th scope="col" style="width: 140px;">Status</th>
            <th scope="col" style="width: 180px;">Publicação</th>
            <th scope="col" class="text-end" style="width: 220px;">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (($items ?? []) as $n): ?>
            <tr>
                <td>
                    <a class="fw-semibold text-decoration-none" href="<?= base_path('/news/' . (int)$n['id']) ?>" target="_blank" rel="noopener">
                        <?= View::e((string)$n['title']) ?>
                    </a>
                </td>
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
                    <a class="btn btn-sm btn-outline-secondary" href="<?= base_path('/admin/news/edit/' . (int)$n['id']) ?>">Editar</a>
                    <form method="post" action="<?= base_path('/admin/news/delete') ?>" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta notícia?');">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
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
