<?php
use App\Core\View;
ob_start();
?>
<div class="admin-avatar-gallery">
<div class="d-flex align-items-center mb-3">
	<h1 class="h4 mb-0">
		<i class="bi bi-person-circle me-2"></i>Galeria de Avatar
	</h1>
</div>

<?php if (!empty($_GET['error'])): ?>
	<div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
		<i class="bi bi-exclamation-triangle-fill me-2"></i>
		<?php if ($_GET['error'] === 'type'): ?>Formato inválido. Use JPG, PNG ou WEBP.
		<?php elseif ($_GET['error'] === 'size'): ?>Arquivo muito grande. Máximo 2MB.
		<?php elseif ($_GET['error'] === 'dim'): ?>Imagem deve ter no máximo 500x500px.
		<?php elseif ($_GET['error'] === 'space'): ?>Espaço insuficiente no servidor.
		<?php elseif ($_GET['error'] === 'upload'): ?>Erro no upload.
		<?php elseif ($_GET['error'] === 'move'): ?>Falha ao salvar o arquivo.
		<?php elseif ($_GET['error'] === 'empty'): ?>Selecione um arquivo.
		<?php else: ?>Erro inesperado.
		<?php endif; ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php elseif (!empty($_GET['created'])): ?>
	<div class="alert alert-success alert-dismissible fade show py-2" role="alert">
		<i class="bi bi-check-circle-fill me-2"></i>Avatar enviado com sucesso.
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php elseif (!empty($_GET['updated'])): ?>
	<div class="alert alert-success alert-dismissible fade show py-2" role="alert">
		<i class="bi bi-check-circle-fill me-2"></i>Avatar atualizado com sucesso.
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php elseif (!empty($_GET['deleted'])): ?>
	<div class="alert alert-success alert-dismissible fade show py-2" role="alert">
		<i class="bi bi-check-circle-fill me-2"></i>Avatar removido com sucesso.
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php endif; ?>

<div class="card mb-4 admin-avatar-gallery-upload">
	<div class="card-header bg-gradient text-white">
		<h2 class="h6 mb-0"><i class="bi bi-cloud-upload me-2"></i>Enviar novo avatar</h2>
	</div>
	<div class="card-body">
		<form method="post" action="<?= base_path('/admin/avatar-gallery/upload') ?>" enctype="multipart/form-data" class="row g-3">
			<input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
			<div class="col-md-5">
				<label class="form-label"><i class="bi bi-file-image me-1"></i>Arquivo</label>
				<input class="form-control form-control-sm" type="file" name="avatar" accept="image/*" required>
				<div class="form-text">JPG, PNG, WEBP. Máximo 500x500px e 2MB.</div>
			</div>
			<div class="col-md-3">
				<label class="form-label"><i class="bi bi-tag me-1"></i>Título</label>
				<input class="form-control form-control-sm" type="text" name="title" placeholder="Opcional">
			</div>
			<div class="col-md-2">
				<label class="form-label"><i class="bi bi-sort-numeric-up me-1"></i>Ordem</label>
				<input class="form-control form-control-sm" type="number" name="sort_order" value="0">
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" name="is_active" id="avatarActive" checked>
					<label class="form-check-label" for="avatarActive"><i class="bi bi-toggle-on me-1"></i>Ativo</label>
				</div>
			</div>
			<div class="col-12">
				<button class="btn btn-success btn-sm" type="submit">
					<i class="bi bi-upload me-1"></i>Enviar Avatar
				</button>
			</div>
		</form>
	</div>
</div>

<div class="card admin-avatar-gallery-list">
	<div class="card-header bg-gradient text-white">
		<h2 class="h6 mb-0"><i class="bi bi-images me-2"></i>Avatares cadastrados</h2>
	</div>
	<div class="card-body">
		<?php if (empty($avatars)): ?>
			<div class="text-muted text-center py-3">
				<i class="bi bi-inbox display-6 d-block mb-2"></i>
				<p class="mb-0">Nenhum avatar cadastrado.</p>
			</div>
		<?php else: ?>
			<div class="table-responsive">
				<table class="table table-hover align-middle mb-0 admin-avatar-gallery-table">
					<thead>
					<tr>
						<th scope="col" style="width: 80px;"><i class="bi bi-eye me-1"></i>Preview</th>
						<th scope="col"><i class="bi bi-tag me-1"></i>Título</th>
						<th scope="col" style="width: 100px;"><i class="bi bi-sort-numeric-up me-1"></i>Ordem</th>
						<th scope="col" style="width: 100px;"><i class="bi bi-toggle-on me-1"></i>Ativo</th>
						<th scope="col" style="width: 180px;" class="text-end"><i class="bi bi-gear me-1"></i>Ações</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($avatars as $a): ?>
						<?php $path = base_path('/' . ltrim((string)($a['file_path'] ?? ''), '/')); ?>
						<?php $formId = 'avatar-form-' . (int)($a['id'] ?? 0); ?>
						<tr>
							<td>
								<img src="<?= View::e($path) ?>" alt="Avatar" class="avatar-gallery-preview">
							</td>
							<td>
								<form id="<?= View::e($formId) ?>" method="post" action="<?= base_path('/admin/avatar-gallery/update') ?>"></form>
								<input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>" form="<?= View::e($formId) ?>">
								<input type="hidden" name="id" value="<?= (int)($a['id'] ?? 0) ?>" form="<?= View::e($formId) ?>">
								<input class="form-control form-control-sm" type="text" name="title" value="<?= View::e((string)($a['title'] ?? '')) ?>" placeholder="Sem título" form="<?= View::e($formId) ?>">
							</td>
							<td>
								<input class="form-control form-control-sm" type="number" name="sort_order" value="<?= (int)($a['sort_order'] ?? 0) ?>" form="<?= View::e($formId) ?>">
							</td>
							<td>
								<select class="form-select form-select-sm" name="is_active" form="<?= View::e($formId) ?>">
									<option value="1" <?= !empty($a['is_active']) ? 'selected' : '' ?>>Sim</option>
									<option value="0" <?= empty($a['is_active']) ? 'selected' : '' ?>>Não</option>
								</select>
							</td>
							<td class="text-end">
								<button class="btn btn-sm btn-success me-1" type="submit" form="<?= View::e($formId) ?>" title="Salvar alterações">
									<i class="bi bi-save"></i>
								</button>
								<form method="post" action="<?= base_path('/admin/avatar-gallery/delete') ?>" onsubmit="return confirm('Remover este avatar?');" class="d-inline">
									<input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
									<input type="hidden" name="id" value="<?= (int)($a['id'] ?? 0) ?>">
									<button class="btn btn-sm btn-danger" type="submit" title="Excluir avatar">
										<i class="bi bi-trash"></i>
									</button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
</div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
