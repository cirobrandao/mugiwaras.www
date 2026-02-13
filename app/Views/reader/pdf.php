<?php
use App\Core\View;
ob_start();
?>
<?php if (!empty($error)): ?>
    <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div><?= View::e((string)$error) ?></div>
        <?php if (!empty($downloadUrl)): ?>
            <a class="btn btn-sm btn-outline-primary" href="<?= $downloadUrl ?>">Baixar PDF</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (empty($error) && !empty($content)): ?>
    <?php if (empty($pages)): ?>
        <div class="alert alert-secondary">Nenhuma página disponível.</div>
    <?php else: ?>
        <div class="reader-header reader-shell-header">
            <div class="reader-header-left">
                <?php if (!empty($content['category_id']) && !empty($content['series_id']) && !empty($content['category_name']) && !empty($content['series_name'])): ?>
                    <a class="btn btn-sm btn-outline-secondary reader-back" href="<?= base_path('/libraries/' . rawurlencode((string)$content['category_name']) . '/' . rawurlencode((string)$content['series_name'])) ?>">
                        <i class="bi bi-chevron-left me-1"></i>
                        <span>Voltar aos capítulos</span>
                    </a>
                <?php endif; ?>
                <div class="reader-title"><?= View::e($content['title'] ?? 'PDF') ?></div>
            </div>
            <div class="reader-mobile-actions">
                <select class="form-select form-select-sm w-auto" id="readerModeMobile">
                    <option value="page" selected>Página</option>
                    <option value="scroll">Scroll</option>
                </select>
            </div>
        </div>

        <div id="readerWrap" class="reader-shell">
            <div class="reader-toolbar mb-2">
                <div class="toolbar-row">
                    <div class="reader-toolbar-left">
                        <?php if (!empty($previousChapterUrl)): ?>
                            <a class="btn btn-sm btn-outline-secondary" href="<?= $previousChapterUrl ?>" title="Voltar ao capítulo anterior" aria-label="Voltar ao capítulo anterior"><i class="bi bi-skip-backward-fill"></i></a>
                        <?php else: ?>
                            <button class="btn btn-sm btn-outline-secondary" disabled title="Voltar ao capítulo anterior" aria-hidden="true"><i class="bi bi-skip-backward-fill"></i></button>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-secondary" id="readerFirst" type="button" title="Primeira página" aria-label="Primeira página"><i class="bi bi-chevron-double-left"></i></button>
                        <button class="btn btn-sm btn-secondary" id="prevPage" type="button" title="Anterior" aria-label="Anterior"><i class="bi bi-chevron-left"></i></button>
                    </div>
                    <div class="reader-toolbar-center">
                        <div class="reader-center-controls d-flex align-items-center">
                            <select class="form-select form-select-sm w-auto" id="readerFitMode">
                                <option value="width">Ajustar largura</option>
                                <option value="height" selected>Ajustar altura</option>
                                <option value="original">Original</option>
                            </select>
                            <select class="form-select form-select-sm w-auto reader-desktop-only" id="readerMode">
                                <option value="page" selected>Página</option>
                                <option value="scroll">Scroll</option>
                            </select>
                            <div class="d-flex align-items-center gap-2">
                                <label class="small text-muted mb-0">Zoom</label>
                                <input type="range" id="readerZoom" min="60" max="160" step="5" value="100">
                            </div>
                            <div class="form-check form-switch m-0 d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" id="readerWheel" checked>
                                <label class="form-check-label small mb-0" for="readerWheel">Scroll do mouse</label>
                            </div>
                        </div>
                    </div>
                    <div class="reader-toolbar-right ms-auto">
                        <button class="btn btn-sm btn-outline-secondary" id="readerExpand" type="button" title="Expandir leitor" aria-label="Expandir leitor"><i class="bi bi-arrows-fullscreen"></i></button>
                        <?php $favBtnClass = !empty($isFavorite) ? 'btn-warning' : 'btn-outline-warning'; ?>
                        <?php $favIconClass = !empty($isFavorite) ? 'bi-star-fill' : 'bi-star'; ?>
                        <form method="post" action="<?= base_path('/libraries/favorite') ?>" class="m-0">
                            <input type="hidden" name="_csrf" value="<?= View::e($csrf ?? '') ?>">
                            <input type="hidden" name="id" value="<?= (int)($content['id'] ?? 0) ?>">
                            <input type="hidden" name="action" value="<?= !empty($isFavorite) ? 'remove' : 'add' ?>">
                            <button class="btn btn-sm <?= $favBtnClass ?>" type="submit" title="Favoritar" data-favorited="<?= !empty($isFavorite) ? '1' : '0' ?>" aria-label="Favoritar"><i class="bi <?= $favIconClass ?>"></i></button>
                        </form>
                        <?php if (!empty($downloadUrl)): ?>
                            <a class="btn btn-sm btn-outline-primary" href="<?= $downloadUrl ?>" title="Download PDF" aria-label="Download PDF"><i class="bi bi-download"></i></a>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-secondary" id="nextPage" type="button" title="Próxima" aria-label="Próxima"><i class="bi bi-chevron-right"></i></button>
                        <button class="btn btn-sm btn-secondary" id="readerLast" type="button" title="Última página" aria-label="Última página"><i class="bi bi-chevron-double-right"></i></button>
                        <?php if (!empty($nextChapterUrl)): ?>
                            <a class="btn btn-sm btn-outline-secondary" href="<?= $nextChapterUrl ?>" title="Avançar para o próximo capítulo" aria-label="Avançar para o próximo capítulo"><i class="bi bi-skip-forward-fill"></i></a>
                        <?php else: ?>
                            <button class="btn btn-sm btn-outline-secondary" disabled title="Avançar para o próximo capítulo" aria-hidden="true"><i class="bi bi-skip-forward-fill"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="reader-progress mt-2">
                    <div class="reader-progress-bar" id="readerProgress" style="width: 0%"></div>
                </div>
            </div>

            <div id="reader" class="reader-frame" data-total="<?= count($pages ?? []) ?>" data-base-url="<?= base_path('/reader/pdf/' . (int)($content['id'] ?? 0) . '/page') ?>" data-content-id="<?= (int)($content['id'] ?? 0) ?>" data-csrf="<?= View::e($csrf ?? '') ?>" data-last-page="<?= (int)($lastPage ?? 0) ?>" data-direction="rtl" data-previous-chapter-url="<?= View::e($previousChapterUrl ?? '') ?>" data-next-chapter-url="<?= View::e($nextChapterUrl ?? '') ?>">
                <div class="reader-overlay d-none" id="readerOverlay">Carregando...</div>
                <img id="readerImage" alt="Página">
            </div>

            <button id="scrollTopBtn" class="btn btn-primary btn-sm reader-scroll-top d-none" title="Ir para o topo" aria-label="Ir para o topo">
                <i class="bi bi-arrow-up"></i>
            </button>

            <div class="reader-footer mt-2" id="readerFooter">
                <div class="d-flex align-items-center gap-2">
                    <div class="reader-desktop-only">
                        <button class="btn btn-sm btn-secondary" id="prevPageFooter" type="button" title="Anterior" aria-label="Anterior"><i class="bi bi-chevron-left"></i></button>
                    </div>
                    <div class="input-group input-group-sm w-auto mx-auto" id="readerPageGuide" role="group" aria-label="Guia de páginas">
                        <span class="input-group-text">Página</span>
                        <input type="number" min="1" class="form-control" id="pageNumber" style="width: 90px;">
                        <span class="input-group-text" id="pageTotal">/ 0</span>
                    </div>
                    <div class="reader-desktop-only">
                        <button class="btn btn-sm btn-secondary" id="nextPageFooter" type="button" title="Próxima" aria-label="Próxima"><i class="bi bi-chevron-right"></i></button>
                    </div>
                </div>
                <div class="text-center small text-muted" id="pageCompact">0/0</div>
                <div class="reader-end-actions mt-2" id="readerEndActions">
                    <button id="readerBottomTop" class="btn btn-sm btn-outline-secondary" type="button">
                        <i class="bi bi-arrow-up me-1"></i>
                        <span>Topo</span>
                    </button>
                    <?php if (!empty($nextChapterUrl)): ?>
                        <a class="btn btn-sm btn-primary" href="<?= $nextChapterUrl ?>">
                            <i class="bi bi-skip-forward-fill me-1"></i>
                            <span>Próximo capítulo</span>
                        </a>
                    <?php else: ?>
                        <button class="btn btn-sm btn-primary" type="button" disabled>
                            <i class="bi bi-skip-forward-fill me-1"></i>
                            <span>Próximo capítulo</span>
                        </button>
                    <?php endif; ?>
                    <?php if (!empty($content['category_id']) && !empty($content['series_id']) && !empty($content['category_name']) && !empty($content['series_name'])): ?>
                        <a class="btn btn-sm btn-outline-secondary" href="<?= base_path('/libraries/' . rawurlencode((string)$content['category_name']) . '/' . rawurlencode((string)$content['series_name'])) ?>">
                            <i class="bi bi-list-ul me-1"></i>
                            <span>Capítulos</span>
                        </a>
                    <?php endif; ?>
                    <a class="btn btn-sm btn-outline-primary" href="<?= base_path('/libraries') ?>">
                        <i class="bi bi-book me-1"></i>
                        <span>Biblioteca</span>
                    </a>
                </div>
            </div>
        </div>
        <script src="<?= url('assets/js/reader.js') ?>"></script>
    <?php endif; ?>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
