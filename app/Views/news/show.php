<?php
use App\Core\View;
use App\Core\Markdown;
use App\Core\Auth;
$loadDashboardSidebarCss = true;
ob_start();

if (!function_exists('time_ago')) {
    function time_ago(?string $datetime): string
    {
        if (empty($datetime)) {
            return 'nunca';
        }
        try {
            $dt = new DateTimeImmutable($datetime);
        } catch (Exception $e) {
            return 'nunca';
        }
        $now = new DateTimeImmutable('now');
        $diff = $now->getTimestamp() - $dt->getTimestamp();
        if ($diff < 60) {
            return 'agora';
        }
        if ($diff < 3600) {
            return 'há ' . (int)floor($diff / 60) . ' min';
        }
        if ($diff < 86400) {
            return 'há ' . (int)floor($diff / 3600) . ' h';
        }
        if ($diff < 2592000) {
            return 'há ' . (int)floor($diff / 86400) . ' d';
        }
        return $dt->format('d/m/Y H:i');
    }
}

$publishedRaw = (string)($newsItem['published_at'] ?? $newsItem['created_at'] ?? '');
$publishedLabel = $publishedRaw !== '' ? date('d/m/Y H:i', strtotime($publishedRaw)) : '-';
$publishedAgo = $publishedRaw !== '' ? time_ago($publishedRaw) : 'nunca';
$author = (string)($newsItem['author_name'] ?? $newsItem['author'] ?? 'Equipe');
$bodyRaw = (string)($newsItem['body'] ?? '');
$bodyHtml = Markdown::toHtml($bodyRaw);

$viewer = (array)($viewer ?? []);
$notifications = (array)($notifications ?? []);
$tier = (string)($viewer['access_tier'] ?? '');
$isFreeAccess = Auth::isAdmin($viewer) || Auth::isEquipe($viewer) || in_array($tier, ['vitalicio', 'especial'], true);

$accessAlertClass = 'secondary';
$accessAlertText = (string)($accessInfo['label'] ?? 'Sem acesso ativo');
$accessAlertCountdown = null;
$accessAlertExpires = null;
$accessAlertShowCountdown = false;

if ($isFreeAccess) {
    $accessAlertClass = 'success';
    $accessAlertText = 'Acesso livre e sem vencimento.';
} elseif (!empty($accessInfo['expires_at'])) {
    $expTs = strtotime((string)$accessInfo['expires_at']);
    if ($expTs !== false) {
        $remaining = $expTs - time();
        $remDays = (int)floor(max(0, $remaining) / 86400);
        $remHours = (int)floor((max(0, $remaining) % 86400) / 3600);
        $accessAlertCountdown = $remDays . 'd ' . $remHours . 'h';
        $accessAlertExpires = (string)$accessInfo['expires_at'];
        if ($remaining <= 0) {
            $accessAlertClass = 'danger';
            $accessAlertText = 'Acesso expirado.';
        } elseif ($remaining <= 172800) {
            $accessAlertClass = 'warning';
            $accessAlertText = 'Acesso ativo, vencendo em até 48h.';
            $accessAlertShowCountdown = true;
        } else {
            $accessAlertClass = 'success';
            $accessAlertText = 'Acesso ativo.';
            $accessAlertShowCountdown = true;
        }
    }
}

$accessIconMap = [
    'success' => 'bi-check-circle-fill',
    'warning' => 'bi-exclamation-triangle-fill',
    'danger' => 'bi-x-octagon-fill',
    'secondary' => 'bi-info-circle-fill',
];
$accessIcon = (string)($accessIconMap[$accessAlertClass] ?? 'bi-info-circle-fill');
$alertCookieUserId = (int)($viewer['id'] ?? 0);
?>

<style>
    .news-article {
        background: var(--surface);
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: var(--radius);
        padding: 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }
    
    body.theme-dark .news-article {
        background: rgba(31, 41, 55, 0.5);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }
    
    .news-article-header {
        background: linear-gradient(90deg, rgba(102, 126, 234, 0.06) 0%, rgba(118, 75, 162, 0.06) 100%);
        border-bottom: 2px solid rgba(0, 0, 0, 0.06);
        padding: 0.875rem 1.25rem;
        margin-bottom: 0;
    }
    
    body.theme-dark .news-article-header {
        border-bottom-color: rgba(255, 255, 255, 0.06);
    }
    
    .news-article-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 0.75rem;
        line-height: 1.3;
    }
    
    .news-article-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.875rem;
        font-size: 0.75rem;
        color: var(--ink-60);
    }
    
    .news-article-meta i {
        font-size: 0.6875rem;
        margin-right: 0.25rem;
        color: var(--ink-50);
    }
    
    .news-article-featured {
        margin: 0;
        border-radius: 0;
        overflow: hidden;
        box-shadow: none;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    }
    
    body.theme-dark .news-article-featured {
        border-bottom-color: rgba(255, 255, 255, 0.06);
    }
    
    .news-article-featured img {
        width: 100%;
        max-height: 320px;
        object-fit: cover;
        display: block;
    }
    
    .news-article-body {
        font-size: 0.9375rem;
        line-height: 1.7;
        color: var(--ink-80);
        padding: 1.25rem;
    }
    
    .news-article-body img {
        display: block;
        max-width: 100%;
        width: auto;
        height: auto;
        max-height: 420px;
        object-fit: contain;
        border-radius: var(--radius);
        margin: 1.25rem auto;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.06);
    }
    
    body.theme-dark .news-article-body img {
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }
    
    .news-article-body p {
        margin-bottom: 1rem;
    }
    
    .news-article-body h2,
    .news-article-body h3,
    .news-article-body h4 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
        color: var(--ink);
    }
    
    .news-article-body h2 { font-size: 1.375rem; }
    .news-article-body h3 { font-size: 1.15rem; }
    .news-article-body h4 { font-size: 1rem; }
    
    @media (max-width: 768px) {
        .news-article-header {
            padding: 0.75rem 1rem;
        }
        
        .news-article-body {
            padding: 1rem;
        }
        
        .news-article-title {
            font-size: 1.25rem;
        }
        
        .news-article-meta {
            font-size: 0.6875rem;
            gap: 0.625rem;
        }
        
        .news-article-featured img {
            max-height: 220px;
        }
        
        .news-article-body {
            font-size: 0.875rem;
        }
    }
    
    @media (max-width: 576px) {
        .news-article-header {
            padding: 0.625rem 0.875rem;
        }
        
        .news-article-body {
            padding: 0.875rem;
        }
        
        .news-article-title {
            font-size: 1.125rem;
        }
        
        .news-article-featured img {
            max-height: 180px;
        }
    }
</style>

<?php if ($accessAlertShowCountdown && !empty($accessAlertExpires)): ?>
    <script>
        (function () {
            const el = document.getElementById('accessCountdown');
            if (!el) return;
            const raw = el.getAttribute('data-expires') || '';
            const parts = raw.split(' ');
            if (parts.length < 2) return;
            const dateParts = parts[0].split('-').map((v) => parseInt(v, 10));
            const timeParts = parts[1].split(':').map((v) => parseInt(v, 10));
            if (dateParts.length < 3 || timeParts.length < 2) return;
            const target = new Date(
                dateParts[0],
                dateParts[1] - 1,
                dateParts[2],
                timeParts[0] || 0,
                timeParts[1] || 0,
                timeParts[2] || 0
            );
            if (isNaN(target.getTime())) return;
            const tick = () => {
                const now = new Date();
                let diff = Math.max(0, target.getTime() - now.getTime());
                const totalSeconds = Math.floor(diff / 1000);
                const days = Math.floor(totalSeconds / 86400);
                const hours = Math.floor((totalSeconds % 86400) / 3600);
                if (totalSeconds <= 0) {
                    el.textContent = '0d 0h';
                    return;
                }
                el.textContent = days + 'd ' + hours + 'h';
            };
            tick();
            setInterval(tick, 60000);
        })();
    </script>
<?php endif; ?>

<div class="portal-container">
    <div class="row g-3">
    <div class="col-12 col-xl-8">
        <article class="news-article">
            <header class="news-article-header">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                    <h1 class="news-article-title mb-0"><?= View::e((string)($newsItem['title'] ?? 'Noticia')) ?></h1>
                    <?php if (!empty($newsItem['category_name'])): ?>
                        <span class="badge bg-primary"><?= View::e((string)$newsItem['category_name']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="news-article-meta">
                    <span><i class="bi bi-calendar-event"></i><?= View::e($publishedLabel) ?></span>
                    <span><i class="bi bi-clock-history"></i><?= View::e($publishedAgo) ?></span>
                    <span><i class="bi bi-person-circle"></i><?= View::e($author) ?></span>
                </div>
            </header>

            <?php if (!empty($newsItem['featured_image_path'])): ?>
                <div class="news-article-featured">
                    <img src="<?= base_path('/' . ltrim((string)$newsItem['featured_image_path'], '/')) ?>" alt="<?= View::e((string)($newsItem['title'] ?? 'Imagem de destaque')) ?>">
                </div>
            <?php endif; ?>

            <div class="news-article-body">
                <?php if ($bodyHtml === ''): ?>
                    <p class="mb-0 text-muted">Sem conteúdo.</p>
                <?php else: ?>
                    <?= $bodyHtml ?>
                <?php endif; ?>
            </div>
        </article>
    </div>

    <div class="col-12 col-xl-4 sidebar">
        <?php require __DIR__ . '/../partials/dashboard_sidebar_content.php'; ?>
    </div>
</div>
</div>
<script>
    (function () {
        const userId = <?= (int)$alertCookieUserId ?>;
        const cookiePrefix = 'mw_alert_closed_u' + userId + '_';

        function getCookie(name) {
            const parts = document.cookie ? document.cookie.split('; ') : [];
            for (const part of parts) {
                if (part.indexOf(name + '=') === 0) {
                    return decodeURIComponent(part.substring(name.length + 1));
                }
            }
            return null;
        }

        function setCookie(name, value, days) {
            const maxAge = days * 24 * 60 * 60;
            document.cookie = name + '=' + encodeURIComponent(value) + '; path=/; max-age=' + maxAge + '; samesite=lax';
        }

        function removeCookie(name) {
            document.cookie = name + '=; path=/; max-age=0; samesite=lax';
        }

        function hasClosedNotifications() {
            const parts = document.cookie ? document.cookie.split('; ') : [];
            for (const part of parts) {
                const eqIndex = part.indexOf('=');
                const name = eqIndex > 0 ? part.substring(0, eqIndex) : part;
                const value = eqIndex > 0 ? decodeURIComponent(part.substring(eqIndex + 1)) : '';
                if (name.indexOf(cookiePrefix + 'notification-') === 0 && value === '1') {
                    return true;
                }
            }
            return false;
        }

        document.querySelectorAll('.js-dismissible-alert').forEach(function (alertEl) {
            const key = alertEl.getAttribute('data-alert-key');
            if (!key) return;
            const cookieName = cookiePrefix + key;
            if (getCookie(cookieName) === '1') {
                alertEl.remove();
                return;
            }
            const closeBtn = alertEl.querySelector('.js-alert-close');
            if (!closeBtn) return;
            closeBtn.addEventListener('click', function () {
                setCookie(cookieName, '1', 30);
                alertEl.remove();
            });
        });

        document.querySelectorAll('.js-restore-notifications').forEach(function (restoreLink) {
            restoreLink.addEventListener('click', function (event) {
                event.preventDefault();
                const parts = document.cookie ? document.cookie.split('; ') : [];
                parts.forEach(function (part) {
                    const eqIndex = part.indexOf('=');
                    const name = eqIndex > 0 ? part.substring(0, eqIndex) : part;
                    if (name.indexOf(cookiePrefix + 'notification-') === 0) {
                        removeCookie(name);
                    }
                });
                window.location.reload();
            });
        });

        if (hasClosedNotifications()) {
            document.querySelectorAll('.js-restore-wrapper').forEach(function (wrapper) {
                wrapper.classList.remove('d-none');
            });
        }
    })();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
