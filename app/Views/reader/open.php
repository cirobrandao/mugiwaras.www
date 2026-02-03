<?php
use App\Core\View;
ob_start();
?>
<?php if (!empty($error)): ?>
	<div class="alert alert-warning"><?= View::e($error) ?></div>
<?php endif; ?>
<?php if (!empty($content)): ?>
	<div class="reader-header">
		<?php if (!empty($content['category_id']) && !empty($content['series_id']) && !empty($content['category_name']) && !empty($content['series_name'])): ?>
			<a class="btn btn-sm btn-outline-secondary reader-back" href="<?= base_path('/libraries/' . rawurlencode((string)$content['category_name']) . '/' . rawurlencode((string)$content['series_name'])) ?>">&larr; Voltar aos capítulos</a>
		<?php endif; ?>
		<div class="reader-title"><?= View::e($content['title'] ?? 'Conteúdo') ?></div>
		<div class="reader-actions">
			<button class="btn btn-sm btn-outline-secondary" id="readerLights" type="button" title="Apagar as luzes"><i class="fa-solid fa-moon"></i></button>
			<?php $favBtnClass = !empty($isFavorite) ? 'btn-warning' : 'btn-outline-warning'; ?>
			<form method="post" action="<?= base_path('/libraries/favorite') ?>" class="m-0">
				<input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
				<input type="hidden" name="id" value="<?= (int)($content['id'] ?? 0) ?>">
				<input type="hidden" name="action" value="<?= !empty($isFavorite) ? 'remove' : 'add' ?>">
				<button class="btn btn-sm <?= $favBtnClass ?>" type="submit" title="Favoritar" data-favorited="<?= !empty($isFavorite) ? '1' : '0' ?>">
					<i class="fa-solid fa-star"></i>
				</button>
			</form>
			<?php if (!empty($downloadToken)): ?>
				<div class="btn-group">
					<button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Download">
						<i class="fa-solid fa-download"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-end">
						<li><a class="dropdown-item" href="<?= base_path('/download/' . (int)$content['id'] . '?token=' . urlencode((string)$downloadToken)) ?>">Original (CBZ)</a></li>
						<li><span class="dropdown-item disabled">PDF (em breve)</span></li>
						<li><span class="dropdown-item disabled">JPEG (ZIP) (em breve)</span></li>
					</ul>
				</div>
			<?php endif; ?>
		</div>
		<div class="reader-mobile-actions">
			<select class="form-select form-select-sm w-auto" id="readerModeMobile">
				<option value="page">Página</option>
				<option value="scroll" selected>Scroll</option>
			</select>
			<?php $favBtnClassMobile = !empty($isFavorite) ? 'btn-warning' : 'btn-outline-warning'; ?>
			<form method="post" action="<?= base_path('/libraries/favorite') ?>" class="m-0">
				<input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
				<input type="hidden" name="id" value="<?= (int)($content['id'] ?? 0) ?>">
				<input type="hidden" name="action" value="<?= !empty($isFavorite) ? 'remove' : 'add' ?>">
				<button class="btn btn-sm <?= $favBtnClassMobile ?>" type="submit" title="Favoritar" data-favorited="<?= !empty($isFavorite) ? '1' : '0' ?>"><i class="fa-solid fa-star"></i></button>
			</form>
		</div>
	</div>
<?php endif; ?>

<?php if (empty($pages) && empty($error)): ?>
	<div class="alert alert-secondary">Nenhuma página disponível.</div>
<?php elseif (!empty($pages)): ?>
	<div id="readerWrap">
	<div class="reader-toolbar mb-2">
		<div class="d-flex flex-wrap align-items-center gap-2">
			<button class="btn btn-sm btn-secondary" id="prevPage">Anterior</button>
			<button class="btn btn-sm btn-secondary" id="nextPage">Próxima</button>
			<button class="btn btn-sm btn-secondary" id="readerFirst" type="button" title="Voltar ao início"><i class="fa-solid fa-backward"></i></button>
			<div class="input-group input-group-sm w-auto">
				<span class="input-group-text">Página</span>
				<input type="number" min="1" class="form-control" id="pageNumber" style="width: 90px;">
				<span class="input-group-text" id="pageTotal">/ 0</span>
			</div>
			<select class="form-select form-select-sm w-auto" id="readerFitMode">
				<option value="width">Ajustar largura</option>
				<option value="height" selected>Ajustar altura</option>
				<option value="original">Original</option>
			</select>
			<div class="d-flex align-items-center gap-2 ms-auto">
				<label class="small text-muted">Zoom</label>
				<input type="range" id="readerZoom" min="60" max="160" step="5" value="100">
				<select class="form-select form-select-sm w-auto reader-desktop-only" id="readerMode">
					<option value="page" selected>Página</option>
					<option value="scroll">Scroll</option>
				</select>
				<div class="form-check form-switch m-0">
					<input class="form-check-input" type="checkbox" id="readerWheel" checked>
					<label class="form-check-label small" for="readerWheel">Scroll do mouse</label>
				</div>
			</div>
		</div>
		<div class="reader-progress mt-2">
			<div class="reader-progress-bar" id="readerProgress" style="width: 0%"></div>
		</div>
	</div>
	<div id="reader" class="reader-frame" data-total="<?= count($pages ?? []) ?>" data-base-url="<?= base_path('/reader/' . (int)($content['id'] ?? 0) . '/page') ?>" data-content-id="<?= (int)($content['id'] ?? 0) ?>" data-csrf="<?= View::e($csrf ?? '') ?>" data-last-page="<?= (int)($lastPage ?? 0) ?>">
		<div class="reader-overlay d-none" id="readerOverlay">Carregando...</div>
		<img id="readerImage" alt="Página">
	</div>
	</div>
	<script src="<?= url('assets/js/reader.js') ?>"></script>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
