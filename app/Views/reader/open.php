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
	<div class="reader-modern-header">
		<div class="reader-header-container">
			<div class="reader-header-left">
				<?php if (!empty($content['category_id']) && !empty($content['series_id']) && !empty($content['category_name']) && !empty($content['series_name'])): ?>
					<a class="btn btn-sm btn-outline-secondary reader-back-btn" href="<?= base_path('/libraries/' . rawurlencode((string)$content['category_name']) . '/' . rawurlencode((string)$content['series_name'])) ?>">
						<i class="bi bi-arrow-left"></i>
						<span class="reader-desktop-only">Voltar</span>
					</a>
				<?php endif; ?>
			</div>
			<div class="reader-title-modern"><?= View::e($content['title'] ?? 'Conteúdo') ?></div>
			<div class="reader-header-right">
				<select class="form-select form-select-sm reader-mode-selector" id="readerModeMobile">
					<option value="page" <?= (($cbzMode ?? 'page') === 'page') ? 'selected' : '' ?>>Página</option>
					<option value="scroll" <?= (($cbzMode ?? '') === 'scroll') ? 'selected' : '' ?>>Scroll</option>
				</select>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php if (empty($pages) && empty($error)): ?>
	<div class="alert alert-secondary">Nenhuma página disponível.</div>
<?php elseif (!empty($pages)): ?>
	<div id="readerWrap" class="reader-modern-shell">
		<!-- Desktop Toolbar -->
		<div class="reader-toolbar-modern reader-desktop-only">
			<div class="toolbar-modern-container">
				<div class="toolbar-section toolbar-nav">
					<?php if (!empty($previousChapterUrl)): ?>
						<a class="btn btn-sm btn-outline-secondary" href="<?= $previousChapterUrl ?>" title="Capítulo anterior"><i class="bi bi-skip-backward-fill"></i></a>
					<?php else: ?>
						<button class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-skip-backward-fill"></i></button>
					<?php endif; ?>
					<button class="btn btn-sm btn-secondary" id="readerFirst"><i class="bi bi-chevron-double-left"></i></button>
					<button class="btn btn-sm btn-secondary" id="prevPage"><i class="bi bi-chevron-left"></i></button>
				</div>
				<div class="toolbar-section toolbar-controls">
					<select class="form-select form-select-sm" id="readerFitMode">
						<option value="width">Largura</option>
						<option value="height" selected>Altura</option>
						<option value="original">Original</option>
					</select>
					<select class="form-select form-select-sm" id="readerMode">
						<option value="page" <?= (($cbzMode ?? 'page') === 'page') ? 'selected' : '' ?>>Página</option>
						<option value="scroll" <?= (($cbzMode ?? '') === 'scroll') ? 'selected' : '' ?>>Scroll</option>
					</select>
					<div class="toolbar-zoom">
						<i class="bi bi-zoom-in"></i>
						<input type="range" id="readerZoom" min="60" max="160" step="5" value="100">
					</div>
					<div class="form-check form-switch">
						<input class="form-check-input" type="checkbox" id="readerWheel" checked>
						<label class="form-check-label" for="readerWheel">Scroll</label>
					</div>
				</div>
				<div class="toolbar-section toolbar-actions">
					<button class="btn btn-sm btn-outline-secondary" id="readerExpand" title="Tela cheia"><i class="bi bi-arrows-fullscreen"></i></button>
					<?php $favBtnClass = !empty($isFavorite) ? 'btn-warning' : 'btn-outline-warning'; ?>
					<?php $favIconClass = !empty($isFavorite) ? 'bi-star-fill' : 'bi-star'; ?>
					<form method="post" action="<?= base_path('/libraries/favorite') ?>" class="m-0">
						<input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
						<input type="hidden" name="id" value="<?= (int)($content['id'] ?? 0) ?>">
						<input type="hidden" name="action" value="<?= !empty($isFavorite) ? 'remove' : 'add' ?>">
						<button class="btn btn-sm <?= $favBtnClass ?>" type="submit" title="Favoritar"><i class="bi <?= $favIconClass ?>"></i></button>
					</form>
					<?php if (!empty($downloadToken)): ?>
						<div class="btn-group">
							<button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-download"></i></button>
							<ul class="dropdown-menu dropdown-menu-end">
								<li><a class="dropdown-item" href="<?= base_path('/download/' . (int)$content['id'] . '?token=' . urlencode((string)$downloadToken)) ?>">*.cbz</a></li>
								<?php if (!empty($pdfDownloadUrl)): ?>
									<li><a class="dropdown-item" href="<?= $pdfDownloadUrl ?>">*.pdf</a></li>
								<?php else: ?>
									<li><span class="dropdown-item disabled">*.pdf (indisponível)</span></li>
								<?php endif; ?>
								<li><span class="dropdown-item disabled">*.zip (em breve)</span></li>
							</ul>
						</div>
					<?php endif; ?>
					<button class="btn btn-sm btn-secondary" id="nextPage"><i class="bi bi-chevron-right"></i></button>
					<button class="btn btn-sm btn-secondary" id="readerLast"><i class="bi bi-chevron-double-right"></i></button>
					<?php if (!empty($nextChapterUrl)): ?>
						<a class="btn btn-sm btn-outline-secondary" href="<?= $nextChapterUrl ?>" title="Próximo capítulo"><i class="bi bi-skip-forward-fill"></i></a>
					<?php else: ?>
						<button class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-skip-forward-fill"></i></button>
					<?php endif; ?>
				</div>
			</div>
			<div class="reader-progress-modern">
				<div class="reader-progress-bar" id="readerProgress" style="width: 0%"></div>
			</div>
		</div>
		<!-- Mobile Controls (simplified) -->
		<div class="reader-mobile-controls">
			<div class="mobile-controls-container">
				<button class="btn btn-sm btn-secondary" id="prevPageMobile"><i class="bi bi-chevron-left"></i></button>
				<div class="mobile-page-info">
					<span id="pageNumberDisplay">1</span> / <span id="pageTotalDisplay">0</span>
				</div>
				<button class="btn btn-sm btn-secondary" id="nextPageMobile"><i class="bi bi-chevron-right"></i></button>
			</div>
			<div class="reader-progress-modern mobile-progress">
				<div class="reader-progress-bar" id="readerProgressMobile" style="width: 0%"></div>
			</div>
		</div>
		
		<!-- Reader Frame -->
		<div id="reader" class="reader-frame-modern" data-total="<?= count($pages ?? []) ?>" data-base-url="<?= base_path('/reader/' . (int)($content['id'] ?? 0) . '/page') ?>" data-content-id="<?= (int)($content['id'] ?? 0) ?>" data-csrf="<?= View::e($csrf ?? '') ?>" data-last-page="<?= (int)($lastPage ?? 0) ?>" data-direction="<?= View::e((string)($cbzDirection ?? 'rtl')) ?>" data-previous-chapter-url="<?= View::e($previousChapterUrl ?? '') ?>" data-next-chapter-url="<?= View::e($nextChapterUrl ?? '') ?>">
			<div class="reader-overlay d-none" id="readerOverlay">Carregando...</div>
			<img id="readerImage" alt="Página" class="reader-image-modern">
			<!-- Mobile tap zones -->
			<div class="reader-tap-zone tap-zone-left" id="tapZoneLeft"></div>
			<div class="reader-tap-zone tap-zone-right" id="tapZoneRight"></div>
		</div>

		<!-- Scroll to top button -->
		<button id="scrollTopBtn" class="reader-scroll-top-modern d-none"><i class="bi bi-arrow-up"></i></button>
	</div>
	
	<!-- Reader Footer -->
	<div class="reader-footer-modern" id="readerFooter">
		<div class="footer-page-control reader-desktop-only">
			<button class="btn btn-sm btn-secondary" id="prevPageFooter"><i class="bi bi-chevron-left"></i></button>
			<div class="footer-page-input">
				<span>Página</span>
				<input type="number" min="1" id="pageNumber" value="1">
				<span id="pageTotal">/ 0</span>
			</div>
			<button class="btn btn-sm btn-secondary" id="nextPageFooter"><i class="bi bi-chevron-right"></i></button>
		</div>
		<div class="footer-actions" id="readerEndActions">
			<button id="readerBottomTop" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-up me-1"></i>Topo</button>
			<?php if (!empty($nextChapterUrl)): ?>
				<a class="btn btn-sm btn-primary" href="<?= $nextChapterUrl ?>"><i class="bi bi-skip-forward-fill me-1"></i>Próximo capítulo</a>
			<?php else: ?>
				<button class="btn btn-sm btn-primary" disabled><i class="bi bi-skip-forward-fill me-1"></i>Próximo capítulo</button>
			<?php endif; ?>
			<?php if (!empty($content['category_id']) && !empty($content['series_id']) && !empty($content['category_name']) && !empty($content['series_name'])): ?>
				<a class="btn btn-sm btn-outline-secondary" href="<?= base_path('/libraries/' . rawurlencode((string)$content['category_name']) . '/' . rawurlencode((string)$content['series_name'])) ?>"><i class="bi bi-list-ul me-1"></i>Capítulos</a>
			<?php endif; ?>
			<a class="btn btn-sm btn-outline-primary" href="<?= base_path('/libraries') ?>"><i class="bi bi-book me-1"></i>Biblioteca</a>
		</div>
	</div>
	
	<script src="<?= url('assets/js/reader.js') ?>"></script>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
