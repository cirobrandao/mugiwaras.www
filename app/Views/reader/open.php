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
<?php if (!empty($content)): ?>
	<div class="reader-header">
		<?php if (!empty($content['category_id']) && !empty($content['series_id']) && !empty($content['category_name']) && !empty($content['series_name'])): ?>
			<a class="btn btn-sm btn-outline-secondary reader-back" href="<?= base_path('/libraries/' . rawurlencode((string)$content['category_name']) . '/' . rawurlencode((string)$content['series_name'])) ?>">
				<i class="fa-solid fa-chevron-left me-1"></i>
				<span class="d-none d-md-inline">Voltar aos capítulos</span>
				<span class="d-inline d-md-none">Voltar</span>
			</a>
		<?php endif; ?>
		<div class="reader-title"><?= View::e($content['title'] ?? 'Conteúdo') ?></div>
		
		<!-- next chapter link moved to toolbar -->
		<div class="reader-mobile-actions">
			<select class="form-select form-select-sm w-auto" id="readerModeMobile">
				<option value="page" <?= (($cbzMode ?? 'page') === 'page') ? 'selected' : '' ?>>Página</option>
				<option value="scroll" <?= (($cbzMode ?? '') === 'scroll') ? 'selected' : '' ?>>Scroll</option>
			</select>
		</div>
	</div>
<?php endif; ?>

<?php if (empty($pages) && empty($error)): ?>
	<div class="alert alert-secondary">Nenhuma página disponível.</div>
<?php elseif (!empty($pages)): ?>
	<div id="readerWrap">
	<div class="reader-toolbar mb-2">
			<div class="toolbar-row">
				<div class="reader-toolbar-left">
					<?php if (!empty($previousChapterUrl)): ?>
						<a class="btn btn-sm btn-outline-secondary" href="<?= $previousChapterUrl ?>" title="Voltar ao capítulo anterior" aria-label="Voltar ao capítulo anterior"><i class="fa-solid fa-step-backward"></i></a>
					<?php else: ?>
						<button class="btn btn-sm btn-outline-secondary" disabled title="Voltar ao capítulo anterior" aria-hidden="true"><i class="fa-solid fa-step-backward"></i></button>
					<?php endif; ?>
					<button class="btn btn-sm btn-secondary" id="readerFirst" type="button" title="Primeira página" aria-label="Primeira página"><i class="fa-solid fa-angle-double-left"></i></button>
					<button class="btn btn-sm btn-secondary" id="prevPage" type="button" title="Anterior" aria-label="Anterior"><i class="fa-solid fa-chevron-left"></i></button>
				</div>
				<div class="reader-toolbar-center">
					<div class="reader-center-controls d-flex align-items-center">
						<select class="form-select form-select-sm w-auto" id="readerFitMode">
							<option value="width">Ajustar largura</option>
							<option value="height" selected>Ajustar altura</option>
							<option value="original">Original</option>
						</select>
						<select class="form-select form-select-sm w-auto reader-desktop-only" id="readerMode">
							<option value="page" <?= (($cbzMode ?? 'page') === 'page') ? 'selected' : '' ?>>Página</option>
							<option value="scroll" <?= (($cbzMode ?? '') === 'scroll') ? 'selected' : '' ?>>Scroll</option>
						</select>
						<div class="d-flex align-items-center gap-2">
							<label class="small text-muted mb-0">Zoom</label>
							<input type="range" id="readerZoom" min="60" max="160" step="5" value="100">
						</div>
						<div class="form-check form-switch m-0 d-none d-md-flex align-items-center">
							<input class="form-check-input" type="checkbox" id="readerWheel" checked>
							<label class="form-check-label small mb-0" for="readerWheel">Scroll do mouse</label>
						</div>
					</div>
				</div>
				<div class="reader-toolbar-right ms-auto">
					<button class="btn btn-sm btn-outline-secondary" id="readerLights" type="button" title="Apagar as luzes" aria-label="Apagar as luzes"><i class="fa-solid fa-moon"></i></button>
					<?php $favBtnClass = !empty($isFavorite) ? 'btn-warning' : 'btn-outline-warning'; ?>
					<form method="post" action="<?= base_path('/libraries/favorite') ?>" class="m-0">
						<input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
						<input type="hidden" name="id" value="<?= (int)($content['id'] ?? 0) ?>">
						<input type="hidden" name="action" value="<?= !empty($isFavorite) ? 'remove' : 'add' ?>">
						<button class="btn btn-sm <?= $favBtnClass ?>" type="submit" title="Favoritar" data-favorited="<?= !empty($isFavorite) ? '1' : '0' ?>" aria-label="Favoritar"><i class="fa-solid fa-star"></i></button>
					</form>
					<?php if (!empty($downloadToken)): ?>
						<div class="btn-group">
							<button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Download" aria-label="Download">
								<i class="fa-solid fa-download"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-end">
								<li><a class="dropdown-item" href="<?= base_path('/download/' . (int)$content['id'] . '?token=' . urlencode((string)$downloadToken)) ?>">*.cbz</a></li>
								<li><span class="dropdown-item disabled">*.pdf (em breve)</span></li>
								<li><span class="dropdown-item disabled">*.zip (em breve)</span></li>
							</ul>
						</div>
					<?php endif; ?>
					<button class="btn btn-sm btn-secondary" id="nextPage" type="button" title="Próxima" aria-label="Próxima"><i class="fa-solid fa-chevron-right"></i></button>
					<button class="btn btn-sm btn-secondary" id="readerLast" type="button" title="Última página" aria-label="Última página"><i class="fa-solid fa-angle-double-right"></i></button>
					<?php if (!empty($nextChapterUrl)): ?>
						<a class="btn btn-sm btn-outline-secondary" href="<?= $nextChapterUrl ?>" title="Avançar para o próximo capítulo" aria-label="Avançar para o próximo capítulo"><i class="fa-solid fa-step-forward"></i></a>
					<?php else: ?>
						<button class="btn btn-sm btn-outline-secondary" disabled title="Avançar para o próximo capítulo" aria-hidden="true"><i class="fa-solid fa-step-forward"></i></button>
					<?php endif; ?>
				</div>
			</div>
		<div class="reader-progress mt-2">
			<div class="reader-progress-bar" id="readerProgress" style="width: 0%"></div>
		</div>
	</div>
		<div id="reader" class="reader-frame" data-total="<?= count($pages ?? []) ?>" data-base-url="<?= base_path('/reader/' . (int)($content['id'] ?? 0) . '/page') ?>" data-content-id="<?= (int)($content['id'] ?? 0) ?>" data-csrf="<?= View::e($csrf ?? '') ?>" data-last-page="<?= (int)($lastPage ?? 0) ?>" data-direction="<?= View::e((string)($cbzDirection ?? 'rtl')) ?>">
		<div class="reader-overlay d-none" id="readerOverlay">Carregando...</div>
		<img id="readerImage" alt="Página">
	</div>

		<!-- scroll-mode: overlay button to go to top -->
		<button id="scrollTopBtn" class="btn btn-primary btn-sm reader-scroll-top d-none" title="Ir para o topo" aria-label="Ir para o topo">
			<i class="fa-solid fa-arrow-up"></i>
		</button>
		<!-- reader footer: page guide placed after the material -->
		<div class="reader-footer mt-2">
			<div class="input-group input-group-sm w-auto mx-auto d-none d-md-flex" role="group" aria-label="Guia de páginas">
				<span class="input-group-text">Página</span>
				<input type="number" min="1" class="form-control" id="pageNumber" style="width: 90px;">
				<span class="input-group-text" id="pageTotal">/ 0</span>
			</div>
			<div class="d-md-none text-center small text-muted" id="pageCompact">0/0</div>
		</div>
	</div>
	<script src="<?= url('assets/js/reader.js') ?>"></script>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
