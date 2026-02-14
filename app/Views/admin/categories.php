<?php
use App\Core\View;
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Categorias</h1>
    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#categoryCreateModal">Adicionar</button>
</div>
<hr class="text-success" />
<?php if (!empty($_GET['created'])): ?>
    <div class="alert alert-success">Categoria criada.</div>
<?php elseif (!empty($_GET['updated'])): ?>
    <div class="alert alert-success">Categoria atualizada.</div>
<?php elseif (!empty($_GET['deleted'])): ?>
    <div class="alert alert-success">Categoria removida.</div>
<?php elseif (!empty($_GET['error'])): ?>
        <?php if ($_GET['error'] === 'inuse'): ?>
            <div class="alert alert-danger">Categoria em uso (séries ou conteúdos vinculados).</div>
        <?php elseif ($_GET['error'] === 'banner'): ?>
            <div class="alert alert-danger">Banner inválido. Use imagem (jpg, png, webp).</div>
        <?php else: ?>
            <div class="alert alert-danger">Preencha o nome.</div>
        <?php endif; ?>
    <?php endif; ?>
<?php if (!empty($setupError)): ?>
    <div class="alert alert-danger">Biblioteca não inicializada. Execute a migração 009_library_series.sql.</div>
<?php endif; ?>

<div class="modal fade" id="categoryCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_path('/admin/categories/create') ?>" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                    <div class="mb-2">
                        <label class="form-label">Nome</label>
                        <input class="form-control" type="text" name="name" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Ordem</label>
                        <input class="form-control" type="number" name="sort_order" value="0" min="0">
                    </div>
                    <div class="mb-2">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" name="requires_subscription" value="1">
                            <span class="form-check-label">Ocultar para não assinantes</span>
                        </label>
                    </div>
                    <div class="mb-2">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" name="adult_only" value="1">
                            <span class="form-check-label">Categoria 18+</span>
                        </label>
                    </div>
                    <div class="mb-2">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" name="hide_from_store" value="1">
                            <span class="form-check-label">Nao listar nos pacotes da loja</span>
                        </label>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label">Exibição na biblioteca</label>
                            <select class="form-select" name="display_orientation">
                                <option value="vertical" selected>Lista</option>
                                <option value="horizontal">Grade</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CBZ: direção</label>
                            <select class="form-select" name="cbz_direction">
                                <option value="rtl" selected>Trás pra frente (mangá)</option>
                                <option value="ltr">Frente pra trás</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CBZ: modo</label>
                            <select class="form-select" name="cbz_mode">
                                <option value="page" selected>Página</option>
                                <option value="scroll">Scroll</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">ePub: tipo</label>
                        <select class="form-select" name="epub_mode">
                            <option value="text" selected>Texto</option>
                            <option value="comic">Quadrinhos</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Tipos de conteúdo</label>
                        <div class="d-flex flex-wrap gap-3">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="content_video" value="1">
                                <span class="form-check-label">Vídeo</span>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="content_cbz" value="1" checked>
                                <span class="form-check-label">CBZ</span>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="content_pdf" value="1" checked>
                                <span class="form-check-label">PDF</span>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="content_epub" value="1">
                                <span class="form-check-label">ePub</span>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="content_download" value="1">
                                <span class="form-check-label">Download</span>
                            </label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Cor da TAG</label>
                        <input class="form-control form-control-color" type="color" name="tag_color" value="#6c757d" title="Escolher cor">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Banner</label>
                        <input class="form-control" type="file" name="banner" accept="image/*">
                    </div>
                    <button class="btn btn-primary" type="submit">Criar</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="admin-categories-table">
    <table class="table">
        <thead>
        <tr>
            <th style="width: 160px;">Banner</th>
            <th style="width: 180px;">Nome</th>
            <th style="width: 140px;">Slug</th>
            <th style="width: 80px;">Ordem</th>
            <th style="width: 160px;">Tipos</th>
            <th style="width: 140px;">Configurações</th>
            <th style="width: 220px;" class="text-end">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (($items ?? []) as $c): ?>
            <tr>
                <td>
                    <?php if (!empty($c['banner_path'])): ?>
                        <img src="<?= base_path('/' . ltrim((string)$c['banner_path'], '/')) ?>" alt="Banner" class="admin-banner-thumb">
                    <?php else: ?>
                        <div class="admin-banner-placeholder">Sem banner</div>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="admin-category-name">
                        <?= View::e((string)$c['name']) ?>
                        <?php if (!empty($c['tag_color'])): ?>
                            <span class="badge" style="background: <?= View::e((string)$c['tag_color']) ?>;">TAG</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <?php if (!empty($c['slug'])): ?>
                        <span class="admin-category-slug"><?= View::e((string)$c['slug']) ?></span>
                    <?php else: ?>
                        <span class="text-muted small">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge bg-secondary"><?= (int)($c['sort_order'] ?? 0) ?></span>
                </td>
                <td>
                    <div class="admin-badge-group">
                        <?php if (!empty($c['content_video'])): ?><span class="badge bg-secondary">Vídeo</span><?php endif; ?>
                        <?php if (!empty($c['content_cbz'])): ?><span class="badge bg-primary">CBZ</span><?php endif; ?>
                        <?php if (!empty($c['content_pdf'])): ?><span class="badge bg-warning text-dark">PDF</span><?php endif; ?>
                        <?php if (!empty($c['content_epub'])): ?><span class="badge bg-info text-dark">ePub</span><?php endif; ?>
                        <?php if (!empty($c['content_download'])): ?><span class="badge bg-dark">DL</span><?php endif; ?>
                    </div>
                </td>
                <td>
                    <div class="admin-info-cell">
                        <?php if (!empty($c['requires_subscription'])): ?><span class="badge bg-success mb-1">Assinantes</span><br><?php endif; ?>
                        <?php if (!empty($c['adult_only'])): ?><span class="badge bg-danger mb-1">18+</span><br><?php endif; ?>
                        <?php if (!empty($c['hide_from_store'])): ?><span class="badge bg-secondary mb-1">Oculto da loja</span><?php endif; ?>
                        <div class="text-muted mt-1" style="font-size: 0.7rem;">
                            <?= (($c['display_orientation'] ?? 'vertical') === 'horizontal') ? 'Grade' : 'Lista' ?> · 
                            <?= View::e((string)($c['cbz_direction'] ?? 'rtl')) ?> · 
                            <?= View::e((string)($c['cbz_mode'] ?? 'page')) ?>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="admin-actions">
                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#editCategory<?= (int)$c['id'] ?>">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteCategory<?= (int)$c['id'] ?>">
                            <i class="bi bi-trash"></i> Excluir
                        </button>
                    </div>
                </td>
            </tr>
            <?php
            ob_start();
            ?>
            <div class="modal fade" id="editCategory<?= (int)$c['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar categoria</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <form method="post" action="<?= base_path('/admin/categories/update') ?>" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                <div class="mb-2">
                                    <label class="form-label">Nome</label>
                                    <input class="form-control" type="text" name="name" value="<?= View::e((string)$c['name']) ?>" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Ordem</label>
                                    <input class="form-control" type="number" name="sort_order" value="<?= (int)($c['sort_order'] ?? 0) ?>" min="0">
                                </div>
                                <div class="mb-2">
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" name="requires_subscription" value="1" <?= !empty($c['requires_subscription']) ? 'checked' : '' ?>>
                                        <span class="form-check-label">Ocultar para não assinantes</span>
                                    </label>
                                </div>
                                <div class="mb-2">
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" name="adult_only" value="1" <?= !empty($c['adult_only']) ? 'checked' : '' ?>>
                                        <span class="form-check-label">Categoria 18+</span>
                                    </label>
                                </div>
                                <div class="mb-2">
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" name="hide_from_store" value="1" <?= !empty($c['hide_from_store']) ? 'checked' : '' ?>>
                                        <span class="form-check-label">Nao listar nos pacotes da loja</span>
                                    </label>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Exibição na biblioteca</label>
                                        <select class="form-select" name="display_orientation">
                                            <option value="vertical" <?= (($c['display_orientation'] ?? 'vertical') === 'vertical') ? 'selected' : '' ?>>Lista</option>
                                            <option value="horizontal" <?= (($c['display_orientation'] ?? '') === 'horizontal') ? 'selected' : '' ?>>Grade</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">CBZ: direção</label>
                                        <select class="form-select" name="cbz_direction">
                                            <option value="rtl" <?= (($c['cbz_direction'] ?? 'rtl') === 'rtl') ? 'selected' : '' ?>>Trás pra frente (mangá)</option>
                                            <option value="ltr" <?= (($c['cbz_direction'] ?? '') === 'ltr') ? 'selected' : '' ?>>Frente pra trás</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">CBZ: modo</label>
                                        <select class="form-select" name="cbz_mode">
                                            <option value="page" <?= (($c['cbz_mode'] ?? 'page') === 'page') ? 'selected' : '' ?>>Página</option>
                                            <option value="scroll" <?= (($c['cbz_mode'] ?? '') === 'scroll') ? 'selected' : '' ?>>Scroll</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">ePub: tipo</label>
                                    <select class="form-select" name="epub_mode">
                                        <option value="text" <?= (($c['epub_mode'] ?? 'text') === 'text') ? 'selected' : '' ?>>Texto</option>
                                        <option value="comic" <?= (($c['epub_mode'] ?? '') === 'comic') ? 'selected' : '' ?>>Quadrinhos</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Tipos de conteúdo</label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" name="content_video" value="1" <?= !empty($c['content_video']) ? 'checked' : '' ?>>
                                            <span class="form-check-label">Vídeo</span>
                                        </label>
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" name="content_cbz" value="1" <?= !empty($c['content_cbz']) ? 'checked' : '' ?>>
                                            <span class="form-check-label">CBZ</span>
                                        </label>
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" name="content_pdf" value="1" <?= !empty($c['content_pdf']) ? 'checked' : '' ?>>
                                            <span class="form-check-label">PDF</span>
                                        </label>
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" name="content_epub" value="1" <?= !empty($c['content_epub']) ? 'checked' : '' ?>>
                                            <span class="form-check-label">ePub</span>
                                        </label>
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" name="content_download" value="1" <?= !empty($c['content_download']) ? 'checked' : '' ?>>
                                            <span class="form-check-label">Download</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Cor da TAG</label>
                                    <input class="form-control form-control-color" type="color" name="tag_color" value="<?= View::e((string)($c['tag_color'] ?? '#6c757d')) ?>" title="Escolher cor">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Banner</label>
                                    <input class="form-control" type="file" name="banner" accept="image/*">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button class="btn btn-primary" type="submit">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="deleteCategory<?= (int)$c['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmar exclusão</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            Tem certeza que deseja excluir a categoria <strong><?= View::e((string)$c['name']) ?></strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form method="post" action="<?= base_path('/admin/categories/delete') ?>">
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                <button class="btn btn-danger" type="submit">Excluir</button>
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
<?php if (!empty($modals ?? [])): ?>
    <?php foreach ($modals as $m): ?>
        <?= $m ?>
    <?php endforeach; ?>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
