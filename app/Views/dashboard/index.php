<?php
use App\Core\Auth;
use App\Core\Markdown;
use App\Core\View;
use App\Models\User;
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

$newsExcerpt = static function (string $body, int $max = 180): string {
    $body = Markdown::toPlainText($body);
    $body = trim($body);
    if ($body === '') {
        return '';
    }
    $lines = preg_split('/\r\n|\n|\r/', $body) ?: [];
    $paragraphLines = [];
    $started = false;
    foreach ($lines as $line) {
        $line = trim((string)$line);
        if ($line === '') {
            if ($started) {
                break;
            }
            continue;
        }
        $started = true;
        $paragraphLines[] = $line;
    }
    $paragraph = implode(' ', $paragraphLines);
    $text = $paragraph !== '' ? $paragraph : preg_replace('/\s+/u', ' ', $body);
    $text = trim((string)$text);
    return mb_strimwidth($text, 0, $max, '...');
};

$notifications = (array)($notifications ?? []);
$tier = (string)($user['access_tier'] ?? '');
$isFreeAccess = Auth::isAdmin($user) || Auth::isEquipe($user) || in_array($tier, ['vitalicio', 'especial'], true);

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
$alertCookieUserId = (int)($user['id'] ?? 0);
?>
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
    <div class="col-12 col-xl-8 dashboard-main">
        <section class="section-card">
            <div class="news-title-box">
                <div class="section-title news-title">➧ Meus Favoritos</div>
            </div>
            <?php if (empty($favoriteSeries)): ?>
                <div class="alert alert-secondary mb-0">Você ainda não favoritou nenhuma série. <a href="<?= base_path('/libraries') ?>">Explorar bibliotecas</a></div>
            <?php else: ?>
                <?php
                $favoriteAll = $favoriteSeries ?? [];
                $showMore = count($favoriteAll) > 10;
                $favoriteTop = array_slice($favoriteAll, 0, $showMore ? 8 : 9);
                ?>
                <div class="row g-2">
                    <?php foreach ($favoriteTop as $s): ?>
                        <?php $seriesName = (string)($s['name'] ?? ''); ?>
                        <?php $categoryName = (string)($s['category_name'] ?? ''); ?>
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card h-100">
                                <div class="card-body py-2">
                                    <a class="fw-semibold d-block" href="<?= base_path('/libraries/' . rawurlencode($categoryName) . '/' . rawurlencode($seriesName)) ?>">
                                        <?= View::e(mb_strimwidth($seriesName, 0, 28, '...')) ?>
                                    </a>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <span class="badge bg-secondary"><?= View::e($categoryName) ?></span>
                                        <span class="small text-muted text-nowrap"><?= (int)($s['chapter_count'] ?? 0) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($showMore): ?>
                        <div class="col-12 col-md-6 col-xl-4">
                            <a class="card h-100 text-decoration-none" href="<?= base_path('/libraries') ?>">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <span class="text-muted">Veja mais</span>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php if (!empty($newsBelowMostRead)): ?>
            <section class="section-card mt-3">
                <div class="news-title-box">
                    <div class="section-title news-title">➧ Blog</div>
                </div>
                <?php $mainFeatured = $newsBelowMostRead[0] ?? null; ?>
                <?php $mainOthers = array_slice($newsBelowMostRead, 1); ?>

                <?php if (!empty($mainFeatured)): ?>
                    <?php
                    $fDateRaw = (string)($mainFeatured['published_at'] ?? $mainFeatured['created_at'] ?? '');
                    $fDate = $fDateRaw !== '' ? date('d/m/Y H:i', strtotime($fDateRaw)) : '-';
                    $fAgo = $fDateRaw !== '' ? time_ago($fDateRaw) : 'nunca';
                    $fAuthor = (string)($mainFeatured['author_name'] ?? $mainFeatured['author'] ?? 'Equipe');
                    $fExcerpt = $newsExcerpt((string)($mainFeatured['body'] ?? ''), 260);
                    $fImage = trim((string)($mainFeatured['featured_image_path'] ?? ''));
                    ?>
                    <div class="card border-0 bg-body-tertiary mb-3">
                        <div class="row g-0">
                            <?php if ($fImage !== ''): ?>
                                <div class="col-12 col-md-4">
                                    <img src="<?= base_path('/' . ltrim($fImage, '/')) ?>" alt="Destaque" class="img-fluid h-100 w-100 rounded-start" style="max-height: 260px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            <div class="<?= $fImage !== '' ? 'col-12 col-md-8' : 'col-12' ?>">
                                <div class="card-body d-flex flex-column h-100" style="min-height: 220px;">
                                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                                        <a class="fw-semibold fs-5 text-decoration-none" href="<?= base_path('/news/' . (int)$mainFeatured['id']) ?>">
                                            <?= View::e((string)($mainFeatured['title'] ?? 'Notícia')) ?>
                                        </a>
                                        <?php if (!empty($mainFeatured['category_name'])): ?>
                                            <span class="badge bg-secondary"><?= View::e((string)$mainFeatured['category_name']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="small text-muted d-flex flex-wrap gap-3 mb-2">
                                        <span><i class="bi bi-calendar-event me-1"></i><?= View::e($fDate) ?></span>
                                        <span><i class="bi bi-clock-history me-1"></i><?= View::e($fAgo) ?></span>
                                        <span><i class="bi bi-person-circle me-1"></i><?= View::e($fAuthor) ?></span>
                                    </div>
                                    <p class="mb-2"><?= View::e($fExcerpt) ?></p>
                                    <div class="mt-auto text-end">
                                        <a class="small text-decoration-none" href="<?= base_path('/news/' . (int)$mainFeatured['id']) ?>">ler publicação</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($mainOthers)): ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($mainOthers as $n): ?>
                            <?php
                            $dateRaw = (string)($n['published_at'] ?? $n['created_at'] ?? '');
                            $metaDate = $dateRaw !== '' ? date('d/m/Y H:i', strtotime($dateRaw)) : '-';
                            $metaAgo = $dateRaw !== '' ? time_ago($dateRaw) : 'nunca';
                            $author = (string)($n['author_name'] ?? $n['author'] ?? 'Equipe');
                            $excerpt = $newsExcerpt((string)($n['body'] ?? ''), 140);
                            $itemImage = trim((string)($n['featured_image_path'] ?? ''));
                            ?>
                            <article class="card border-0 bg-body-tertiary">
                                <div class="row g-0 h-100">
                                    <?php if ($itemImage !== ''): ?>
                                        <div class="col-12 col-md-3">
                                            <img src="<?= base_path('/' . ltrim($itemImage, '/')) ?>" alt="Imagem" class="img-fluid h-100 w-100 rounded-start" style="max-height: 160px; object-fit: cover;">
                                        </div>
                                    <?php endif; ?>
                                    <div class="<?= $itemImage !== '' ? 'col-12 col-md-9' : 'col-12' ?>">
                                        <div class="card-body py-3 d-flex flex-column h-100" style="min-height: 160px;">
                                            <div class="news-row">
                                                <a class="fw-semibold" href="<?= base_path('/news/' . (int)$n['id']) ?>"><?= View::e((string)$n['title']) ?></a>
                                                <?php if (!empty($n['category_name'])): ?>
                                                    <span class="badge bg-secondary"><?= View::e((string)$n['category_name']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="small text-muted d-flex flex-wrap gap-3 mb-1">
                                                <span><i class="bi bi-calendar-event me-1"></i><?= View::e($metaDate) ?></span>
                                                <span><i class="bi bi-clock-history me-1"></i><?= View::e($metaAgo) ?></span>
                                                <span><i class="bi bi-person-circle me-1"></i><?= View::e($author) ?></span>
                                            </div>
                                            <div><?= View::e($excerpt) ?></div>
                                            <div class="mt-auto pt-2 text-end">
                                                <a class="small text-decoration-none" href="<?= base_path('/news/' . (int)$n['id']) ?>">ler publicação</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>

    <div class="col-12 col-xl-4 dashboard-sidebar">
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
            <div class="news-title-box news-title">
                <div class="section-title">➧ Top 10</div>
            </div>
            <?php $mostReadTop = array_slice($mostReadSeries ?? [], 0, 10); ?>
            <?php if (empty($mostReadTop)): ?>
                <div class="alert alert-secondary mb-0">Ainda não há leituras registradas.</div>
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
                                <span class="badge <?= $badgeClass ?>"<?= $badgeStyle ?>>
                                    <?= View::e($mrCategory) ?>
                                </span>
                            </div>
                            <span class="small text-muted text-nowrap"><?= (int)($mr['read_count'] ?? 0) ?> leituras</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="section-card mt-3">
            <div class="news-title-box">
                <div class="section-title news-title">➧ Ultimos Lançamentos</div>
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
                        <?php $rcIsNew = !empty($rc['is_new']); ?>
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
