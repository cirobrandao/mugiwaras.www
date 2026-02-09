<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Galeria de Avatar</h1>
<hr class="text-success" />
<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?php if ($_GET['error'] === 'type'): ?>Formato invalido. Use jpg, png ou webp.
        <?php elseif ($_GET['error'] === 'size'): ?>Arquivo muito grande. Maximo 2MB.
        <?php elseif ($_GET['error'] === 'dim'): ?>Imagem deve ter no maximo 500x500px.
        <?php elseif ($_GET['error'] === 'space'): ?>Espaco insuficiente no servidor.
        <?php elseif ($_GET['error'] === 'upload'): ?>Erro no upload.
        <?php elseif ($_GET['error'] === 'move'): ?>Falha ao salvar o arquivo.
        <?php elseif ($_GET['error'] === 'empty'): ?>Selecione um arquivo.
        <?php else: ?>Erro inesperado.
        <?php endif; ?>
    </div>
<?php elseif (!empty($_GET['created'])): ?>
    <div class="alert alert-success">Avatar enviado.</div>
<?php elseif (!empty($_GET['updated'])): ?>
    <div class="alert alert-success">Avatar atualizado.</div>
<?php elseif (!empty($_GET['deleted'])): ?>
    <div class="alert alert-success">Avatar removido.</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <h2 class="h6">Enviar novo avatar</h2>
        <form method="post" action="<?= base_path('/admin/avatar-gallery/upload') ?>" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
            <div class="col-md-5">
                <label class="form-label">Arquivo</label>
                <input class="form-control" type="file" name="avatar" accept="image/*" required>
                <div class="form-text">JPG, PNG, WEBP. Maximo 500x500px e 2MB.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Titulo</label>
                <input class="form-control" type="text" name="title" placeholder="Opcional">
            </div>
            <div class="col-md-2">
                <label class="form-label">Ordem</label>
                <input class="form-control" type="number" name="sort_order" value="0">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="avatarActive" checked>
                    <label class="form-check-label" for="avatarActive">Ativo</label>
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit">Enviar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h2 class="h6">Avatares cadastrados</h2>
        <?php if (empty($avatars)): ?>
            <div class="text-muted">Nenhum avatar cadastrado.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 80px;">Preview</th>
                        <th scope="col">Titulo</th>
                        <th scope="col" style="width: 110px;">Ordem</th>
                        <th scope="col" style="width: 110px;">Ativo</th>
                        <th scope="col" style="width: 140px;">Acoes</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($avatars as $a): ?>
                        <?php $path = base_path('/' . ltrim((string)($a['file_path'] ?? ''), '/')); ?>
                        <?php $formId = 'avatar-form-' . (int)($a['id'] ?? 0); ?>
                        <tr>
                            <td>
                                <img src="<?= View::e($path) ?>" alt="Avatar" style="width: 48px; height: 48px; border-radius: 6px; object-fit: cover;">
                            </td>
                            <td>
                                <form id="<?= View::e($formId) ?>" method="post" action="<?= base_path('/admin/avatar-gallery/update') ?>"></form>
                                <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>" form="<?= View::e($formId) ?>">
                                <input type="hidden" name="id" value="<?= (int)($a['id'] ?? 0) ?>" form="<?= View::e($formId) ?>">
                                <input class="form-control" type="text" name="title" value="<?= View::e((string)($a['title'] ?? '')) ?>" form="<?= View::e($formId) ?>">
                            </td>
                            <td>
                                <input class="form-control" type="number" name="sort_order" value="<?= (int)($a['sort_order'] ?? 0) ?>" form="<?= View::e($formId) ?>">
                            </td>
                            <td>
                                <select class="form-select" name="is_active" form="<?= View::e($formId) ?>">
                                    <option value="1" <?= !empty($a['is_active']) ? 'selected' : '' ?>>Sim</option>
                                    <option value="0" <?= empty($a['is_active']) ? 'selected' : '' ?>>Nao</option>
                                </select>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary" type="submit" form="<?= View::e($formId) ?>">Salvar</button>
                                <form method="post" action="<?= base_path('/admin/avatar-gallery/delete') ?>" onsubmit="return confirm('Remover avatar?');">
                                    <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                                    <input type="hidden" name="id" value="<?= (int)($a['id'] ?? 0) ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
