<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Categorias</h1>

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

<div class="card mb-3">
    <div class="card-body">
        <form method="post" action="<?= base_path('/admin/categories/create') ?>" enctype="multipart/form-data">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
            <div class="mb-2">
                <label class="form-label">Nome</label>
                <input class="form-control" type="text" name="name" required>
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


<div class="table-responsive">
    <table class="table table-sm">
        <thead>
        <tr>
            <th>Banner</th>
            <th>Nome</th>
            <th class="text-end">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (($items ?? []) as $c): ?>
            <tr>
                <td style="width: 180px;">
                    <?php if (!empty($c['banner_path'])): ?>
                            <img src="<?= base_path('/' . ltrim((string)$c['banner_path'], '/')) ?>" alt="Banner" class="admin-banner-thumb">
                        <?php else: ?>
                            <div class="text-muted small">Sem banner</div>
                        <?php endif; ?>
                </td>
                <td>
                    <?= View::e((string)$c['name']) ?>
                    <?php if (!empty($c['tag_color'])): ?>
                        <span class="badge ms-2" style="background: <?= View::e((string)$c['tag_color']) ?>;">TAG</span>
                    <?php endif; ?>
                </td>
                <td class="text-end">
                    <form method="post" action="<?= base_path('/admin/categories/delete') ?>" class="d-inline">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                    </form>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <details>
                        <summary class="mb-2">Editar</summary>
                        <form method="post" action="<?= base_path('/admin/categories/update') ?>" enctype="multipart/form-data">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                            <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                            <div class="mb-2">
                                <label class="form-label">Nome</label>
                                <input class="form-control" type="text" name="name" value="<?= View::e((string)$c['name']) ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Cor da TAG</label>
                                <input class="form-control form-control-color" type="color" name="tag_color" value="<?= View::e((string)($c['tag_color'] ?? '#6c757d')) ?>" title="Escolher cor">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Banner</label>
                                <input class="form-control" type="file" name="banner" accept="image/*">
                            </div>
                            <button class="btn btn-sm btn-primary" type="submit">Salvar</button>
                        </form>
                    </details>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
