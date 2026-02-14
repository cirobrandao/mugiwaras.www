<?php
use App\Core\View;
use App\Core\Markdown;
use App\Core\Auth;
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
    .news-article-body img {
        display: block;
        max-width: 100%;
        width: auto;
        height: auto;
        max-height: min(70vh, 560px);
        object-fit: contain;
        border-radius: .5rem;
        margin: .75rem auto;
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

<div class="row g-3">
    <div class="col-12 col-xl-8">
        <article class="section-card news-publication-detail">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                <h1 class="h4 mb-0"><?= View::e((string)($newsItem['title'] ?? 'Noticia')) ?></h1>
                <?php if (!empty($newsItem['category_name'])): ?>
                    <span class="badge bg-secondary"><?= View::e((string)$newsItem['category_name']) ?></span>
                <?php endif; ?>
            </div>

            <div class="small text-muted d-flex flex-wrap gap-3 mb-3">
                <span><i class="bi bi-calendar-event me-1"></i><?= View::e($publishedLabel) ?></span>
                <span><i class="bi bi-clock-history me-1"></i><?= View::e($publishedAgo) ?></span>
                <span><i class="bi bi-person-circle me-1"></i><?= View::e($author) ?></span>
            </div>

            <?php if (!empty($newsItem['featured_image_path'])): ?>
                <div class="mb-3">
                    <img src="<?= base_path('/' . ltrim((string)$newsItem['featured_image_path'], '/')) ?>" alt="Imagem de destaque" class="img-fluid rounded border w-100" style="max-height: 300px; object-fit: cover;">
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

    <div class="col-12 col-xl-4">
        <section class="section-card">
            <div class="news-title-box">
                <div class="section-title news-title">➧ Avisos</div>
            </div>
            <div class="alert alert-<?= View::e($accessAlertClass) ?> py-2 mb-2 border-0 small d-flex align-items-start gap-2" role="alert">
                <i class="bi <?= View::e($accessIcon) ?> align-self-start"></i>
                <div class="flex-grow-1">
                    <?= View::e($accessAlertText) ?>
                <?php if ($accessAlertShowCountdown && !empty($accessAlertExpires) && !empty($accessAlertCountdown)): ?>
                    <?php if (!empty($activePackageTitle)): ?>
                        Pacote: <?= View::e((string)$activePackageTitle) ?>.
                    <?php endif; ?>
                    Restante: <span id="accessCountdown" data-expires="<?= View::e($accessAlertExpires) ?>"><?= View::e($accessAlertCountdown) ?></span>.
                <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($notifications)): ?>
                <?php
                $prioMap = [
                    'high' => 'danger',
                    'medium' => 'warning',
                    'low' => 'info',
                ];
                $prioIconMap = [
                    'high' => 'bi-exclamation-octagon-fill',
                    'medium' => 'bi-exclamation-triangle-fill',
                    'low' => 'bi-info-circle-fill',
                ];
                ?>
                <?php foreach ($notifications as $notification): ?>
                    <?php
                    $priority = (string)($notification['priority'] ?? 'low');
                    $priorityClass = (string)($prioMap[$priority] ?? 'info');
                    $priorityIcon = (string)($prioIconMap[$priority] ?? 'bi-info-circle-fill');
                    $notifId = (int)($notification['id'] ?? 0);
                    ?>
                    <div class="alert alert-<?= View::e($priorityClass) ?> py-2 mb-2 border-0 small d-flex align-items-start gap-2 js-dismissible-alert" data-alert-key="notification-<?= $notifId ?>" role="alert">
                        <i class="bi <?= View::e($priorityIcon) ?> align-self-start"></i>
                        <div class="flex-grow-1">
                            <div class="fw-semibold"><?= View::e((string)($notification['title'] ?? 'Notificação')) ?></div>
                            <div><?= View::e((string)($notification['body'] ?? '')) ?></div>
                        </div>
                        <button type="button" class="btn btn-sm text-reset p-0 border-0 bg-transparent js-alert-close" aria-label="Fechar alerta">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="text-end d-none js-restore-wrapper">
                <a href="#" class="small text-decoration-none js-restore-notifications">Recuperar notificações fechadas</a>
            </div>
        </section>

        <section class="section-card mt-3">
            <div class="news-title-box">
                <div class="section-title news-title">➧ Top 5</div>
            </div>
            <?php $mostReadTop = array_slice($mostReadSeries ?? [], 0, 5); ?>
            <?php if (empty($mostReadTop)): ?>
                <div class="alert alert-secondary mb-0">Ainda nao ha leituras registradas.</div>
            <?php else: ?>
                <div class="list-group list-group-flush dashboard-list">
                    <?php foreach ($mostReadTop as $mr): ?>
                        <?php $mrName = (string)($mr['name'] ?? ''); ?>
                        <?php $mrCategory = (string)($mr['category_name'] ?? ''); ?>
                        <?php $mrCatId = (int)($mr['category_id'] ?? 0); ?>
                        <div class="list-group-item d-flex align-items-center justify-content-between gap-2 py-2">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <a class="fw-semibold" href="<?= base_path('/libraries/' . rawurlencode($mrCategory) . '/' . rawurlencode($mrName)) ?>">
                                    <?= View::e(mb_strimwidth($mrName, 0, 25, '...')) ?>
                                </a>
                                <?php
                                $badgeClass = $mrCatId > 0 ? 'cat-badge-' . $mrCatId : 'bg-secondary';
                                $badgeStyle = '';
                                if (!empty($mr['resolved_tag_color'])) {
                                    $badgeStyle = ' style="background-color: ' . View::e((string)$mr['resolved_tag_color']) . '; color: #fff;"';
                                }
                                ?>
                                <span class="badge <?= $badgeClass ?>"<?= $badgeStyle ?>><?= View::e($mrCategory) ?></span>
                            </div>
                            <span class="small text-muted text-nowrap"><?= (int)($mr['read_count'] ?? 0) ?> leituras</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="section-card mt-3">
            <div class="news-title-box">
                <div class="section-title news-title">➧ Ultimos Lancamentos</div>
            </div>
            <?php if (empty($recentContent)): ?>
                <div class="alert alert-secondary mb-0">Sem novos envios.</div>
            <?php else: ?>
                <?php $recentTop = array_slice($recentContent ?? [], 0, 5); ?>
                <div class="list-group list-group-flush dashboard-list">
                    <?php foreach ($recentTop as $rc): ?>
                        <?php $rcCategory = (string)($rc['category_name'] ?? ''); ?>
                        <?php $rcSeries = (string)($rc['series_name'] ?? ''); ?>
                        <?php $rcTitle = (string)($rc['title'] ?? ''); ?>
                        <?php $rcSeriesLabel = mb_strimwidth($rcSeries, 0, 40, '...'); ?>
                        <?php $rcTitleLabel = mb_strimwidth($rcTitle, 0, 40, '...'); ?>
                        <?php $rcCatId = (int)($rc['category_id'] ?? 0); ?>
                        <div class="list-group-item d-flex align-items-center justify-content-between gap-2 py-2">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <?php if ($rcCategory !== '' && $rcSeries !== ''): ?>
                                    <a class="fw-semibold" href="<?= base_path('/libraries/' . rawurlencode($rcCategory) . '/' . rawurlencode($rcSeries)) ?>">
                                        <?= View::e(mb_strimwidth($rcSeriesLabel, 0, 25, '...')) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="fw-semibold"><?= View::e(mb_strimwidth($rcTitleLabel, 0, 25, '...')) ?></span>
                                <?php endif; ?>
                                <?php if ($rcCategory !== ''): ?>
                                    <?php
                                    $rcBadgeClass = $rcCatId > 0 ? 'cat-badge-' . $rcCatId : 'bg-secondary';
                                    $rcBadgeStyle = '';
                                    if (!empty($rc['category_tag_color'])) {
                                        $rcBadgeStyle = ' style="background-color: ' . View::e((string)$rc['category_tag_color']) . '; color: #fff;"';
                                    }
                                    ?>
                                    <span class="badge <?= $rcBadgeClass ?>"<?= $rcBadgeStyle ?>><?= View::e($rcCategory) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($rc['created_at'])): ?>
                                <span class="small text-muted text-nowrap"><?= View::e(time_ago((string)$rc['created_at'])) ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="section-card mt-3">
            <div class="news-title-box">
                <div class="section-title news-title">➧ Publicações recentes</div>
            </div>
            <?php if (empty($recentNews)): ?>
                <div class="alert alert-secondary mb-0">Sem noticias recentes.</div>
            <?php else: ?>
                <div class="list-group list-group-flush dashboard-list">
                    <?php foreach ($recentNews as $rn): ?>
                        <div class="list-group-item d-flex align-items-center justify-content-between gap-2 py-2">
                            <a class="fw-semibold" href="<?= base_path('/news/' . (int)$rn['id']) ?>">
                                <?= View::e(mb_strimwidth((string)($rn['title'] ?? ''), 0, 36, '...')) ?>
                            </a>
                            <?php $rnDate = (string)($rn['published_at'] ?? $rn['created_at'] ?? ''); ?>
                            <?php if ($rnDate !== ''): ?>
                                <span class="small text-muted text-nowrap"><?= View::e(time_ago($rnDate)) ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
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
