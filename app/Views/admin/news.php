<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Notícias</h1>

<?php if (!empty($_GET['created'])): ?>
    <div class="alert alert-success">Notícia criada.</div>
<?php elseif (!empty($_GET['updated'])): ?>
    <div class="alert alert-success">Notícia atualizada.</div>
<?php elseif (!empty($_GET['deleted'])): ?>
    <div class="alert alert-success">Notícia removida.</div>
<?php elseif (!empty($_GET['error'])): ?>
    <div class="alert alert-danger">Preencha título e conteúdo.</div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <form method="post" action="<?= base_path('/admin/news/create') ?>">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
            <div class="mb-2">
                <label class="form-label">Título</label>
                <input class="form-control" type="text" name="title" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Conteúdo</label>
                <textarea class="form-control" name="body" rows="4" required></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">Publicado em (opcional)</label>
                <input class="form-control" type="datetime-local" name="published_at">
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_published" id="newsPublished" value="1">
                <label class="form-check-label" for="newsPublished">Publicar</label>
            </div>
            <button class="btn btn-primary" type="submit">Criar</button>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-sm">
        <thead>
        <tr>
            <th>Título</th>
            <th>Status</th>
            <th>Publicação</th>
            <th class="text-end">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (($items ?? []) as $n): ?>
            <tr>
                <td><?= View::e($n['title']) ?></td>
                <td><?= !empty($n['is_published']) ? 'Publicado' : 'Rascunho' ?></td>
                <td><?= View::e((string)($n['published_at'] ?? '')) ?></td>
                <td class="text-end">
                    <form method="post" action="<?= base_path('/admin/news/delete') ?>" class="d-inline">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                    </form>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <details>
                        <summary class="mb-2">Editar</summary>
                        <form method="post" action="<?= base_path('/admin/news/update') ?>">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                        <div class="mb-2">
                            <label class="form-label">Título</label>
                            <input class="form-control" type="text" name="title" value="<?= View::e($n['title']) ?>" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Conteúdo</label>
                            <textarea class="form-control" name="body" rows="4" required><?= View::e($n['body']) ?></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Publicado em</label>
                            <input class="form-control" type="datetime-local" name="published_at" value="<?= View::e((string)($n['published_at'] ?? '')) ?>">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_published" id="newsPublished<?= (int)$n['id'] ?>" value="1" <?= !empty($n['is_published']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="newsPublished<?= (int)$n['id'] ?>">Publicar</label>
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
