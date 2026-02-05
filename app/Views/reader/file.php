<?php
use App\Core\View;
ob_start();
?>
<?php if (!empty($error)): ?>
	<div class="alert alert-warning d-flex flex-column gap-2">
		<span><?= View::e($error) ?></span>
		<a class="btn btn-sm btn-primary align-self-start" href="<?= base_path('/loja') ?>">Compre o seu acesso.</a>
	</div>
<?php endif; ?>
<?php if (!empty($title)): ?>
	<div class="reader-header">
		<div class="reader-title"><?= View::e((string)$title) ?></div>
	</div>
<?php endif; ?>

<?php if (empty($pages)): ?>
	<div class="alert alert-secondary">Nenhuma página disponível.</div>
<?php else: ?>
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
				<select class="form-select form-select-sm w-auto" id="readerMode">
					<option value="page" selected>Página</option>
					<option value="scroll">Scroll</option>
				</select>
				<div class="form-check form-switch m-0">
					<input class="form-check-input" type="checkbox" id="readerWheel" checked>
					<label class="form-check-label small" for="readerWheel">Scroll do mouse</label>
				</div>
				<button class="btn btn-sm btn-outline-secondary" id="readerLights" type="button" title="Apagar as luzes"><i class="fa-solid fa-moon"></i></button>
			</div>
		</div>
		<div class="reader-progress mt-2">
			<div class="reader-progress-bar" id="readerProgress" style="width: 0%"></div>
		</div>
	</div>
	<div class="small text-muted mb-2" id="readerStatus">&nbsp;</div>
	<div id="reader" class="reader-frame" data-total="<?= count($pages ?? []) ?>" data-base-url="<?= base_path('/reader/file/page') ?>" data-query="?f=<?= View::e((string)($fileToken ?? '')) ?>">
		<div class="reader-overlay d-none" id="readerOverlay">Carregando...</div>
		<img id="readerImage" alt="Página">
	</div>
	</div>
	<script src="<?= url('assets/js/reader.js') ?>"></script>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
