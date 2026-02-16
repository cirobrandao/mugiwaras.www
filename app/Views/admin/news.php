<?php
use App\Core\View;
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0"><i class="bi bi-newspaper me-2"></i>Notícias</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#newsCategoryModal">
            <i class="bi bi-folder me-1"></i>Categorias
        </button>
        <a class="btn btn-primary" href="<?= base_path('/admin/news/create') ?>">
            <i class="bi bi-plus-circle me-1"></i>Nova notícia
        </a>
    </div>
</div>
<hr class="text-success" />

<?php if (!empty($_GET['created'])): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Notícia criada.</div>
<?php elseif (!empty($_GET['updated'])): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Notícia atualizada.</div>
<?php elseif (!empty($_GET['deleted'])): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Notícia removida.</div>
<?php elseif (!empty($_GET['error']) && $_GET['error'] === 'category'): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Selecione uma categoria válida.</div>
<?php elseif (!empty($_GET['error']) && $_GET['error'] === 'image'): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Imagem de destaque inválida. Use JPG, PNG ou WEBP até 4MB.</div>
<?php elseif (!empty($_GET['error'])): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Preencha título e conteúdo.</div>
<?php endif; ?>

<?php if (!empty($_GET['category_created'])): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Categoria criada.</div>
<?php elseif (!empty($_GET['category_updated'])): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Categoria atualizada.</div>
<?php elseif (!empty($_GET['category_deleted'])): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Categoria removida.</div>
<?php endif; ?>

<div class="modal fade" id="newsCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg admin-news-category-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-folder me-2"></i>Categorias de notícias</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_path('/admin/news/category/create') ?>" class="row g-3 pb-3 mb-3 border-bottom">
                    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                    <div class="col-md-5">
                        <label class="form-label"><i class="bi bi-tag me-1"></i>Nome</label>
                        <input class="form-control" name="name" placeholder="Ex: Lançamentos" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-block opacity-0">-</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="show_sidebar" id="catSidebar" value="1" checked>
                            <label class="form-check-label" for="catSidebar">
                                <i class="bi bi-layout-sidebar me-1"></i>Sidebar
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-block opacity-0">-</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="show_below_most_read" id="catBelow" value="1">
                            <label class="form-check-label" for="catBelow">
                                <i class="bi bi-house me-1"></i>MainPage
                            </label>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button class="btn btn-primary w-100" type="submit" title="Adicionar">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </form>

                <div class="admin-news-categories-list">
                    <table class="table table-sm mb-0">
                        <thead>
                        <tr>
                            <th style="width: 200px;">Nome</th>
                            <th style="width: 200px;">Exibição</th>
                            <th class="text-end">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="3" class="text-muted text-center py-3">
                                    <i class="bi bi-inbox fs-4 d-block mb-1 opacity-50"></i>
                                    Nenhuma categoria cadastrada.
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach (($categories ?? []) as $cat): ?>
                            <tr>
                                <td>
                                    <input class="form-control form-control-sm" form="cat-<?= (int)$cat['id'] ?>" name="name" value="<?= View::e((string)$cat['name']) ?>">
                                </td>
                                <td>
                                    <form id="cat-<?= (int)$cat['id'] ?>" method="post" action="<?= base_path('/admin/news/category/update') ?>" class="d-inline">
                                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                                        <input type="hidden" name="show_sidebar" value="0">
                                        <input type="hidden" name="show_below_most_read" value="0">
                                        <div class="d-flex gap-2">
                                            <div class="form-check form-check-inline mb-0">
                                                <input class="form-check-input" type="checkbox" name="show_sidebar" value="1" <?= !empty($cat['show_sidebar']) ? 'checked' : '' ?> id="catSide<?= (int)$cat['id'] ?>">
                                                <label class="form-check-label small" for="catSide<?= (int)$cat['id'] ?>">Sidebar</label>
                                            </div>
                                            <div class="form-check form-check-inline mb-0">
                                                <input class="form-check-input" type="checkbox" name="show_below_most_read" value="1" <?= !empty($cat['show_below_most_read']) ? 'checked' : '' ?> id="catBelow<?= (int)$cat['id'] ?>">
                                                <label class="form-check-label small" for="catBelow<?= (int)$cat['id'] ?>">MainPage</label>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <div class="admin-actions">
                                        <button class="btn btn-sm btn-outline-primary" type="submit" form="cat-<?= (int)$cat['id'] ?>">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <form method="post" action="<?= base_path('/admin/news/category/delete') ?>" class="d-inline">
                                            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                                            <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                                            <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Tem certeza?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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

<div class="admin-news-table">
    <table class="table">
        <thead>
        <tr>
            <th style="width: 350px;">Título</th>
            <th style="width: 160px;">Categoria</th>
            <th style="width: 110px;" class="text-center">Status</th>
            <th style="width: 160px;">Publicação</th>
            <th style="width: 180px;" class="text-end">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($items)): ?>
            <tr>
                <td colspan="5" class="text-muted text-center py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                    Nenhuma notícia cadastrada.
                </td>
            </tr>
        <?php endif; ?>
        <?php foreach (($items ?? []) as $n): ?>
            <tr>
                <td>
                    <div class="news-title">
                        <i class="bi bi-newspaper text-warning me-2"></i>
                        <a class="news-link" href="<?= base_path('/news/' . (int)$n['id']) ?>" target="_blank" rel="noopener">
                            <?= View::e((string)$n['title']) ?>
                        </a>
                    </div>
                </td>
                <td>
                    <?php if (!empty($n['category_name'])): ?>
                        <span class="badge bg-info text-dark">
                            <i class="bi bi-folder me-1"></i><?= View::e((string)$n['category_name']) ?>
                        </span>
                    <?php else: ?>
                        <span class="text-muted small">—</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if (!empty($n['is_published'])): ?>
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Publicado</span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><i class="bi bi-file-earmark me-1"></i>Rascunho</span>
                    <?php endif; ?>
                </td>
                <td>
                    <small class="text-muted">
                        <?php if (!empty($n['published_at'])): ?>
                            <i class="bi bi-calendar-check me-1"></i><?= View::e((string)$n['published_at']) ?>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </small>
                </td>
                <td>
                    <div class="admin-actions">
                        <a class="btn btn-sm btn-outline-primary" href="<?= base_path('/admin/news/edit/' . (int)$n['id']) ?>">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <form method="post" action="<?= base_path('/admin/news/delete') ?>" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta notícia?');">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger" type="submit">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
