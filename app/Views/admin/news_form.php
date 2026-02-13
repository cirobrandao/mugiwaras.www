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

<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0"><?= View::e($pageTitle) ?></h1>
    <?php if ($isEdit): ?>
        <a class="btn btn-outline-secondary" href="#" onclick="history.back(); return false;">Voltar</a>
    <?php else: ?>
        <a class="btn btn-outline-secondary" href="<?= base_path('/admin/news') ?>">Voltar</a>
    <?php endif; ?>
</div>
<hr class="text-success" />

<?php if (!empty($_GET['error']) && $_GET['error'] === 'category'): ?>
    <div class="alert alert-danger">Selecione uma categoria válida.</div>
<?php elseif (!empty($_GET['error']) && $_GET['error'] === 'image'): ?>
    <div class="alert alert-danger">Imagem de destaque inválida. Use JPG, PNG ou WEBP até 4MB.</div>
<?php elseif (!empty($_GET['error'])): ?>
    <div class="alert alert-danger">Preencha título e conteúdo.</div>
<?php endif; ?>

<form method="post" action="<?= $formAction ?>" enctype="multipart/form-data" class="row g-3">
    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
    <?php endif; ?>

    <div class="col-12 col-xl-8">
        <div class="card border-0 bg-body-tertiary h-100">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input class="form-control" type="text" name="title" value="<?= View::e($titleValue) ?>" required>
                </div>

                <div class="mb-2 d-flex flex-wrap gap-2" id="markdownToolbar">
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-action="h2">H2</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-action="h3">H3</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-action="bold">Negrito</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-action="italic">Itálico</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-action="strike">Riscado</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-action="ul">Lista</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-action="ol">Numerada</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-action="quote">Citação</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-action="code">Código</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-action="link">Link</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-action="image">Imagem</button>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#bodyImageUploadModal">Upload imagem</button>
                </div>

                <div class="mb-3">
                    <label class="form-label">Conteúdo (Markdown)</label>
                    <textarea class="form-control font-monospace" id="newsBody" name="body" rows="16" required><?= View::e($bodyValue) ?></textarea>
                    <div class="form-text">Use Markdown para títulos, listas, links, imagens, citações e blocos de código.</div>
                </div>

                <div class="border rounded p-3 bg-body" style="min-height: 200px; max-height: 420px; overflow:auto;">
                    <div class="small text-muted mb-2">Pré-visualização</div>
                    <div id="markdownPreview" class="small"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card border-0 bg-body-tertiary mb-3">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Categoria</label>
                    <select class="form-select" name="category_id" required>
                        <option value="">Selecionar</option>
                        <?php foreach (($categories ?? []) as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>" <?= (int)$cat['id'] === $categoryValue ? 'selected' : '' ?>><?= View::e((string)$cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Imagem de destaque</label>
                    <input class="form-control" type="file" name="featured_image" accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">Formatos: JPG, PNG ou WEBP. Máximo 4MB.</div>
                    <?php if ($isEdit && !empty($item['featured_image_path'])): ?>
                        <div class="mt-2">
                            <img src="<?= base_path('/' . ltrim((string)$item['featured_image_path'], '/')) ?>" alt="Imagem atual" class="img-fluid rounded border" style="max-height: 140px;">
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="remove_featured_image" id="removeFeaturedImage" value="1">
                            <label class="form-check-label" for="removeFeaturedImage">Remover imagem atual</label>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Publicado em (opcional)</label>
                    <input class="form-control" type="datetime-local" name="published_at" value="<?= View::e($publishedAtInput) ?>">
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_published" id="newsPublished" value="1" <?= $isPublished ? 'checked' : '' ?>>
                    <label class="form-check-label" for="newsPublished">Publicar</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="publish_now" id="newsPublishNow" value="1">
                    <label class="form-check-label" for="newsPublishNow">Publicar agora</label>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Salvar alterações' : 'Criar notícia' ?></button>
                    <a class="btn btn-outline-secondary" href="<?= base_path('/admin/news') ?>">Cancelar</a>
                </div>
            </div>
        </div>

        <div class="card border-0 bg-body-tertiary">
            <div class="card-body">
                <h2 class="h6 mb-2">Atalhos Markdown</h2>
                <ul class="small mb-0">
                    <li><strong>#</strong>, <strong>##</strong>, <strong>###</strong> para títulos</li>
                    <li><strong>**texto**</strong> para negrito</li>
                    <li><strong>*texto*</strong> para itálico</li>
                    <li><strong>[título](https://url)</strong> para links</li>
                    <li><strong>![alt](https://imagem)</strong> para imagens</li>
                    <li><strong>```</strong> para blocos de código</li>
                </ul>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="bodyImageUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload de imagem para conteúdo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="bodyImageFile">Imagem</label>
                    <input class="form-control" id="bodyImageFile" type="file" accept="image/jpeg,image/png,image/webp,image/gif">
                    <div class="form-text">JPG, PNG, WEBP ou GIF até 4MB.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="bodyImageAlt">Texto alternativo (opcional)</label>
                    <input class="form-control" id="bodyImageAlt" type="text" maxlength="120" placeholder="Descreva a imagem">
                </div>
                <div id="bodyImageUploadFeedback" class="small"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="bodyImageUploadSubmit">Enviar e inserir</button>
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