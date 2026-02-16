<?php
use App\Core\View;

ob_start();

$images = (array)($images ?? []);
$formatBytes = static function (int $bytes): string {
    if ($bytes <= 0) {
        return '0 B';
    }
    $units = ['B', 'KB', 'MB', 'GB'];
    $pow = (int)floor(log($bytes, 1024));
    $pow = min($pow, count($units) - 1);
    $value = $bytes / (1024 ** $pow);
    return number_format($value, $value >= 100 ? 0 : 1, ',', '.') . ' ' . $units[$pow];
};
?>

<div class="admin-images">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">
            <i class="bi bi-images me-2"></i>Banco de imagens
        </h1>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="<?= base_path('/admin') ?>" title="Atalhos admin" aria-label="Atalhos admin">
                <i class="bi bi-grid"></i>
            </a>
            <a class="btn btn-outline-primary" href="<?= base_path('/admin/news') ?>" title="Notícias" aria-label="Notícias">
                <i class="bi bi-newspaper"></i>
            </a>
        </div>
    </div>

    <?php if (!empty($_GET['deleted'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>Imagem removida com sucesso.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($_GET['error']) && $_GET['error'] === 'csrf'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Sessão inválida para excluir imagem.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($_GET['error']) && $_GET['error'] === 'invalid'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Imagem inválida para exclusão.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($_GET['error']) && $_GET['error'] === 'notfound'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Arquivo não encontrado.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($images)): ?>
        <div class="alert alert-info mb-0">
            <i class="bi bi-inbox me-2"></i>Ainda não há imagens enviadas.
        </div>
    <?php else: ?>
        <div class="admin-images-table">
            <table class="table table-hover align-middle mb-0">
                    <tr>
                        <th scope="col" style="width: 100px;"><i class="bi bi-image me-1"></i>Preview</th>
                        <th scope="col"><i class="bi bi-file-earmark-text me-1"></i>Arquivo</th>
                        <th scope="col" style="width: 100px;"><i class="bi bi-file-code me-1"></i>Tipo</th>
                        <th scope="col" style="width: 110px;"><i class="bi bi-hdd me-1"></i>Tamanho</th>
                        <th scope="col" style="width: 170px;"><i class="bi bi-clock-history me-1"></i>Atualizado</th>
                        <th scope="col" class="text-end" style="width: 220px;"><i class="bi bi-gear me-1"></i>Ações</th>
                    </tr>
                <tbody>
            <?php foreach ($images as $img): ?>
                <tr>
                    <td>
                        <a href="<?= View::e((string)$img['url']) ?>" target="_blank" rel="noopener" class="image-preview-link">
                            <img src="<?= View::e((string)$img['url']) ?>" alt="preview" class="img-fluid rounded border" style="max-height:64px; max-width:80px; object-fit:cover;">
                        </a>
                    </td>
                    <td>
                        <div class="fw-semibold text-truncate" style="max-width: 300px;"><?= View::e((string)$img['name']) ?></div>
                        <div class="small text-muted text-truncate" style="max-width: 300px;"><?= View::e((string)$img['relative_path']) ?></div>
                    </td>
                    <td><span class="badge bg-info"><?= View::e((string)$img['type']) ?></span></td>
                    <td class="small"><?= View::e($formatBytes((int)($img['size'] ?? 0))) ?></td>
                    <td class="small text-muted"><?= View::e((string)($img['modified_at'] ?? '-')) ?></td>
                    <td class="text-end admin-actions">
                        <button
                            class="btn btn-sm btn-outline-primary js-copy-image-url"
                            type="button"
                            data-url="<?= View::e((string)$img['url']) ?>"
                            title="Copiar link da imagem"
                            aria-label="Copiar link da imagem"
                        ><i class="bi bi-link-45deg"></i></button>
                        <a class="btn btn-sm btn-outline-secondary" href="<?= View::e((string)$img['url']) ?>" target="_blank" rel="noopener" title="Abrir imagem" aria-label="Abrir imagem">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                        <form method="post" action="<?= base_path('/admin/images/delete') ?>" class="d-inline" onsubmit="return confirm('Excluir esta imagem do banco?');">
                            <input type="hidden" name="_csrf" value="<?= View::e((string)$csrf) ?>">
                            <input type="hidden" name="path" value="<?= View::e((string)$img['relative_path']) ?>">
                            <button class="btn btn-sm btn-outline-danger" type="submit" title="Excluir imagem" aria-label="Excluir imagem">
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

<script>
    (function () {
        const buttons = document.querySelectorAll('.js-copy-image-url');
        if (!buttons.length) {
            return;
        }

        const fallbackCopy = (text) => {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.setAttribute('readonly', 'readonly');
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            let ok = false;
            try {
                ok = document.execCommand('copy');
            } catch (e) {
                ok = false;
            }
            document.body.removeChild(textarea);
            return ok;
        };

        const copyText = async (text) => {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
                return true;
            }
            return fallbackCopy(text);
        };

        buttons.forEach((button) => {
            button.addEventListener('click', async () => {
                const url = String(button.getAttribute('data-url') || '').trim();
                if (!url) {
                    return;
                }
                let absoluteUrl = url;
                try {
                    absoluteUrl = new URL(url, window.location.origin).href;
                } catch (e) {
                    absoluteUrl = url;
                }

                const originalText = button.textContent;
                button.disabled = true;
                try {
                    const ok = await copyText(absoluteUrl);
                    button.textContent = ok ? 'Link copiado!' : 'Falha ao copiar';
                } catch (e) {
                    button.textContent = 'Falha ao copiar';
                }

                window.setTimeout(() => {
                    button.textContent = originalText;
                    button.disabled = false;
                }, 1200);
            });
        });
    })();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
