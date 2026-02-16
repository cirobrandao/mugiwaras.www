<?php
use App\Core\View;

ob_start();

$mode = (string)($mode ?? 'create');
$isEdit = $mode === 'edit';
$item = is_array($news ?? null) ? $news : [];
$titleValue = (string)($item['title'] ?? '');
$bodyValue = (string)($item['body'] ?? '');
$categoryValue = (int)($item['category_id'] ?? 0);
$isPublished = !empty($item['is_published']);
$publishedAtRaw = trim((string)($item['published_at'] ?? ''));
$publishedAtInput = '';
if ($publishedAtRaw !== '') {
    $ts = strtotime($publishedAtRaw);
    if ($ts !== false) {
        $publishedAtInput = date('Y-m-d\TH:i', $ts);
    }
}
$formAction = $isEdit ? base_path('/admin/news/update') : base_path('/admin/news/create');
$pageTitle = $isEdit ? 'Editar notícia' : 'Nova notícia';
?>

<div class="admin-news-form">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">
            <i class="bi bi-<?= $isEdit ? 'pencil-square' : 'file-earmark-plus' ?> me-2"></i>
            <?= View::e($pageTitle) ?>
        </h1>
        <?php if ($isEdit): ?>
            <a class="btn btn-outline-secondary" href="#" onclick="history.back(); return false;">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
        <?php else: ?>
            <a class="btn btn-outline-secondary" href="<?= base_path('/admin/news') ?>">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($_GET['error']) && $_GET['error'] === 'category'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Selecione uma categoria válida.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($_GET['error']) && $_GET['error'] === 'image'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Imagem de destaque inválida. Use JPG, PNG ou WEBP até 4MB.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Preencha título e conteúdo.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= $formAction ?>" enctype="multipart/form-data" class="row g-3">
    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
    <?php endif; ?>

        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient text-white">
                    <i class="bi bi-file-text me-2"></i>Conteúdo da notícia
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-fonts me-1"></i>Título
                        </label>
                        <input class="form-control" type="text" name="title" value="<?= View::e($titleValue) ?>" placeholder="Digite o título da notícia" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">
                            <i class="bi bi-markdown me-1"></i>Conteúdo (Markdown)
                        </label>
                        <div class="d-flex flex-wrap gap-2 mb-2" id="markdownToolbar">
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-action="h2" title="Título H2">
                                <i class="bi bi-type-h2"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-action="h3" title="Título H3">
                                <i class="bi bi-type-h3"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-action="bold" title="Negrito">
                                <i class="bi bi-type-bold"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-action="italic" title="Itálico">
                                <i class="bi bi-type-italic"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-action="strike" title="Riscado">
                                <i class="bi bi-type-strikethrough"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-action="ul" title="Lista">
                                <i class="bi bi-list-ul"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-action="ol" title="Lista numerada">
                                <i class="bi bi-list-ol"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-action="quote" title="Citação">
                                <i class="bi bi-quote"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-action="code" title="Código">
                                <i class="bi bi-code-slash"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-action="link" title="Link">
                                <i class="bi bi-link-45deg"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-action="image" title="Imagem">
                                <i class="bi bi-image"></i>
                            </button>
                            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#bodyImageUploadModal" title="Upload de imagem">
                                <i class="bi bi-cloud-upload me-1"></i>Upload
                            </button>
                        </div>
                        <textarea class="form-control font-monospace" id="newsBody" name="body" rows="14" placeholder="Digite o conteúdo em Markdown" required><?= View::e($bodyValue) ?></textarea>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>Use Markdown para títulos, listas, links, imagens, citações e blocos de código.
                        </div>
                    </div>

                    <div class="preview-container">
                        <div class="preview-header">
                            <i class="bi bi-eye me-1"></i>Pré-visualização
                        </div>
                        <div class="preview-body" id="markdownPreview"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-gradient text-white">
                    <i class="bi bi-gear me-2"></i>Configurações
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-tag me-1"></i>Categoria
                        </label>
                        <select class="form-select" name="category_id" required>
                            <option value="">Selecionar categoria</option>
                            <?php foreach (($categories ?? []) as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>" <?= (int)$cat['id'] === $categoryValue ? 'selected' : '' ?>>
                                    <?= View::e((string)$cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-image me-1"></i>Imagem de destaque
                        </label>
                        <input class="form-control" type="file" name="featured_image" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>Formatos: JPG, PNG ou WEBP. Máximo 4MB.
                        </div>
                        <?php if ($isEdit && !empty($item['featured_image_path'])): ?>
                            <div class="mt-3">
                                <img src="<?= base_path('/' . ltrim((string)$item['featured_image_path'], '/')) ?>" alt="Imagem atual" class="img-fluid rounded border" style="max-height: 160px;">
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="remove_featured_image" id="removeFeaturedImage" value="1">
                                <label class="form-check-label" for="removeFeaturedImage">
                                    <i class="bi bi-trash me-1"></i>Remover imagem atual
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-calendar-event me-1"></i>Publicado em (opcional)
                        </label>
                        <input class="form-control" type="datetime-local" name="published_at" value="<?= View::e($publishedAtInput) ?>">
                    </div>

                    <div class="mb-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="is_published" id="newsPublished" value="1" <?= $isPublished ? 'checked' : '' ?>>
                            <label class="form-check-label" for="newsPublished">
                                <i class="bi bi-check-circle me-1"></i>Publicar
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="publish_now" id="newsPublishNow" value="1">
                            <label class="form-check-label" for="newsPublishNow">
                                <i class="bi bi-lightning-charge me-1"></i>Publicar agora
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-<?= $isEdit ? 'check-circle' : 'plus-circle' ?> me-1"></i>
                            <?= $isEdit ? 'Salvar alterações' : 'Criar notícia' ?>
                        </button>
                        <a class="btn btn-outline-secondary" href="<?= base_path('/admin/news') ?>">
                            <i class="bi bi-x-circle me-1"></i>Cancelar
                        </a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient text-white">
                    <i class="bi bi-lightbulb me-2"></i>Atalhos Markdown
                </div>
                <div class="card-body">
                    <ul class="small mb-0 markdown-help">
                        <li><code>#</code>, <code>##</code>, <code>###</code> para títulos</li>
                        <li><code>**texto**</code> para negrito</li>
                        <li><code>*texto*</code> para itálico</li>
                        <li><code>[título](https://url)</code> para links</li>
                        <li><code>![alt](https://imagem)</code> para imagens</li>
                        <li><code>```</code> para blocos de código</li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade admin-news-upload-modal" id="bodyImageUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-cloud-upload me-2"></i>Upload de imagem para conteúdo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="bodyImageFile">
                        <i class="bi bi-file-image me-1"></i>Imagem
                    </label>
                    <input class="form-control" id="bodyImageFile" type="file" accept="image/jpeg,image/png,image/webp,image/gif">
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>JPG, PNG, WEBP ou GIF até 4MB.
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="bodyImageAlt">
                        <i class="bi bi-chat-square-text me-1"></i>Texto alternativo (opcional)
                    </label>
                    <input class="form-control" id="bodyImageAlt" type="text" maxlength="120" placeholder="Descreva a imagem">
                </div>
                <div id="bodyImageUploadFeedback" class="small"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Fechar
                </button>
                <button type="button" class="btn btn-primary" id="bodyImageUploadSubmit">
                    <i class="bi bi-check-circle me-1"></i>Enviar e inserir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const bodyInput = document.getElementById('newsBody');
        const preview = document.getElementById('markdownPreview');
        const toolbar = document.getElementById('markdownToolbar');
        const uploadFileInput = document.getElementById('bodyImageFile');
        const uploadAltInput = document.getElementById('bodyImageAlt');
        const uploadSubmit = document.getElementById('bodyImageUploadSubmit');
        const uploadFeedback = document.getElementById('bodyImageUploadFeedback');
        const uploadEndpoint = <?= json_encode(base_path('/admin/news/body-image')) ?>;
        const csrfToken = <?= json_encode((string)$csrf) ?>;
        if (!bodyInput || !preview || !toolbar) {
            return;
        }

        const escapeHtml = (text) => text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        const safeUrl = (url) => {
            const val = String(url || '').trim();
            if (/^(https?:\/\/|\/|#)/i.test(val)) {
                return escapeHtml(val);
            }
            return '';
        };

        const renderInline = (input) => {
            let text = escapeHtml(input);
            text = text.replace(/!\[([^\]]*)\]\(([^)]+)\)/g, (m, alt, url) => {
                const safe = safeUrl(url);
                if (!safe) return m;
                return '<img src="' + safe + '" alt="' + escapeHtml(alt) + '" class="img-fluid rounded border my-2">';
            });
            text = text.replace(/\[([^\]]+)\]\(([^)]+)\)/g, (m, label, url) => {
                const safe = safeUrl(url);
                if (!safe) return m;
                return '<a href="' + safe + '" target="_blank" rel="noopener">' + escapeHtml(label) + '</a>';
            });
            text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            text = text.replace(/\*(.+?)\*/g, '<em>$1</em>');
            text = text.replace(/~~(.+?)~~/g, '<del>$1</del>');
            text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
            return text;
        };

        const renderMarkdown = (markdown) => {
            const blocks = String(markdown || '').replace(/\r\n/g, '\n').split(/\n{2,}/);
            const html = blocks.map((rawBlock) => {
                const block = rawBlock.trim();
                if (!block) return '';

                const heading = block.match(/^(#{1,6})\s+(.+)$/);
                if (heading) {
                    const level = heading[1].length;
                    return '<h' + level + '>' + renderInline(heading[2]) + '</h' + level + '>';
                }

                if (/^```[\s\S]*```$/.test(block)) {
                    const code = block.replace(/^```[a-zA-Z0-9_-]*\n?/, '').replace(/```$/, '');
                    return '<pre class="p-2 rounded bg-dark-subtle"><code>' + escapeHtml(code) + '</code></pre>';
                }

                const lines = block.split('\n');
                if (lines.every((line) => /^[-*+]\s+/.test(line))) {
                    return '<ul>' + lines.map((line) => '<li>' + renderInline(line.replace(/^[-*+]\s+/, '')) + '</li>').join('') + '</ul>';
                }
                if (lines.every((line) => /^\d+\.\s+/.test(line))) {
                    return '<ol>' + lines.map((line) => '<li>' + renderInline(line.replace(/^\d+\.\s+/, '')) + '</li>').join('') + '</ol>';
                }
                if (lines.every((line) => /^>\s?/.test(line))) {
                    return '<blockquote class="border-start border-3 ps-3 mb-2">' + renderInline(lines.map((line) => line.replace(/^>\s?/, '')).join('<br>')) + '</blockquote>';
                }

                return '<p>' + renderInline(block).replace(/\n/g, '<br>') + '</p>';
            }).join('');
            return html || '<p class="text-muted mb-0">Digite o conteúdo para pré-visualizar.</p>';
        };

        const wrapSelection = (before, after, placeholder) => {
            const start = bodyInput.selectionStart;
            const end = bodyInput.selectionEnd;
            const selected = bodyInput.value.slice(start, end) || placeholder;
            const replacement = before + selected + after;
            bodyInput.setRangeText(replacement, start, end, 'end');
            bodyInput.focus();
            updatePreview();
        };

        const appendAtCursor = (text) => {
            const start = bodyInput.selectionStart;
            const end = bodyInput.selectionEnd;
            bodyInput.setRangeText(text, start, end, 'end');
            bodyInput.focus();
            updatePreview();
        };

        toolbar.addEventListener('click', (event) => {
            const button = event.target.closest('button[data-action]');
            if (!button) return;
            const action = button.getAttribute('data-action');
            if (action === 'h2') wrapSelection('## ', '', 'Título');
            if (action === 'h3') wrapSelection('### ', '', 'Subtítulo');
            if (action === 'bold') wrapSelection('**', '**', 'texto');
            if (action === 'italic') wrapSelection('*', '*', 'texto');
            if (action === 'strike') wrapSelection('~~', '~~', 'texto');
            if (action === 'ul') wrapSelection('- ', '', 'item');
            if (action === 'ol') wrapSelection('1. ', '', 'item');
            if (action === 'quote') wrapSelection('> ', '', 'citação');
            if (action === 'code') wrapSelection('```\n', '\n```', 'código');
            if (action === 'link') wrapSelection('[', '](https://)', 'texto do link');
            if (action === 'image') wrapSelection('![alt](', ')', 'https://imagem');
        });

        const updatePreview = () => {
            preview.innerHTML = renderMarkdown(bodyInput.value);
        };

        const setUploadFeedback = (message, isError) => {
            if (!uploadFeedback) {
                return;
            }
            uploadFeedback.className = 'small ' + (isError ? 'text-danger' : 'text-success');
            uploadFeedback.textContent = message;
        };

        if (uploadSubmit && uploadFileInput) {
            uploadSubmit.addEventListener('click', async () => {
                const file = uploadFileInput.files && uploadFileInput.files[0] ? uploadFileInput.files[0] : null;
                if (!file) {
                    setUploadFeedback('Selecione uma imagem para enviar.', true);
                    return;
                }

                uploadSubmit.disabled = true;
                setUploadFeedback('Enviando imagem...', false);

                try {
                    const formData = new FormData();
                    formData.append('_csrf', csrfToken);
                    formData.append('body_image', file);

                    const response = await fetch(uploadEndpoint, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    });
                    const data = await response.json();
                    if (!response.ok || !data || (!data.relative_url && !data.url)) {
                        setUploadFeedback('Falha no upload. Verifique formato/tamanho e tente novamente.', true);
                        return;
                    }

                    const alt = (uploadAltInput && uploadAltInput.value ? uploadAltInput.value.trim() : '') || 'imagem';
                    const imageUrl = String(data.relative_url || data.url || '').trim();
                    appendAtCursor('![' + alt + '](' + imageUrl + ')');
                    setUploadFeedback('Imagem enviada e inserida no conteúdo.', false);
                    uploadFileInput.value = '';
                    if (uploadAltInput) {
                        uploadAltInput.value = '';
                    }
                } catch (e) {
                    setUploadFeedback('Erro ao enviar imagem.', true);
                } finally {
                    uploadSubmit.disabled = false;
                }
            });
        }

        bodyInput.addEventListener('input', updatePreview);
        updatePreview();
    })();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';