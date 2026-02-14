<?php
use App\Core\Auth;
use App\Core\Markdown;
use App\Core\View;
use App\Models\User;
$loadDashboardSidebarCss = true;
ob_start();

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
    return truncate($text, $max);
};

$notifications = (array)($notifications ?? []);
$tier = (string)($user['access_tier'] ?? '');
$isFreeAccess = Auth::isAdmin($user) || Auth::isEquipe($user) || in_array($tier, ['vitalicio', 'especial'], true);

$accessAlertClass = 'secondary';
$accessAlertText = (string)($accessInfo['label'] ?? 'Sem acesso ativo');
$accessAlertCountdown = null;
$accessAlertExpires = null;
$accessAlertShowCountdown = false;
$activePackageTitle = null;

if ($isFreeAccess) {
    $accessAlertClass = 'success';
    $accessAlertText = 'Acesso livre e sem vencimento.';
} elseif (!empty ($accessInfo['expires_at'])) {
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

if (!empty($accessInfo['package_title'])) {
    $activePackageTitle = (string)$accessInfo['package_title'];
}
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

<div class="portal-container">
    <div class="row g-3">
        <div class="col-12 col-xl-8 portal-main">
            <!-- Favorites Section -->
            <section class="content-section" role="region" aria-labelledby="favorites-title">
                <div class="section-header">
                    <h2 class="section-title" id="favorites-title">
                        <i class="bi bi-star-fill me-2"></i>Meus Favoritos
                    </h2>
                </div>
                <?php if (empty($favoriteSeries)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-star"></i>
                        </div>
                        <div class="empty-title">Nenhum favorito ainda</div>
                        <div class="empty-text">Comece explorando nossa biblioteca e adicione suas séries favoritas!</div>
                        <a href="<?= base_path('/lib') ?>" class="btn btn-primary btn-sm mt-2">
                            <i class="bi bi-search me-1"></i>Explorar Bibliotecas
                        </a>
                    </div>
                <?php else: ?>
                    <?php
                    $favoriteAll = $favoriteSeries ?? [];
                    $showMore = count($favoriteAll) > 10;
                    $favoriteTop = array_slice($favoriteAll, 0, $showMore ? 8 : 9);
                    ?>
                    <div class="series-grid">
                        <?php foreach ($favoriteTop as $s): ?>
                            <?php $seriesName = (string)($s['name'] ?? ''); ?>
                            <?php $categoryName = (string)($s['category_name'] ?? ''); ?>
                            <?php $categorySlug = !empty($s['category_slug']) ? (string)$s['category_slug'] : \App\Models\Category::generateSlug($categoryName); ?>
                            <?php $chapterCount = (int)($s['chapter_count'] ?? 0); ?>
                            <?php $catId = (int)($s['category_id'] ?? 0); ?>
                            <?php $hasNewContent = !empty($s['is_new']); ?>
                            <div class="series-card <?= $hasNewContent ? 'has-new-content' : '' ?>">
                                <?php if ($hasNewContent): ?>
                                    <div class="new-badge">
                                        <span>NOVO</span>
                                    </div>
                                <?php endif; ?>
                                <div class="series-icon">
                                    <i class="bi bi-bookmark-star-fill"></i>
                                </div>
                                <a class="series-title" href="<?= base_path('/lib/' . rawurlencode($categorySlug) . '/' . rawurlencode($seriesName)) ?>">
                                    <?= View::e($seriesName) ?>
                                </a>
                                <div class="series-meta">
                                    <?php
                                    $badgeClass = $catId > 0 ? 'cat-badge-' . $catId : '';
                                    $badgeStyle = '';
                                    if (!empty($s['resolved_tag_color'])) {
                                        $badgeStyle = ' style="background-color: ' . View::e((string)$s['resolved_tag_color']) . '; color: #fff;"';
                                    }
                                    ?>
                                    <span class="badge series-badge <?= $badgeClass ?>"<?= $badgeStyle ?>>
                                        <?= View::e($categoryName) ?>
                                    </span>
                                    <span class="series-count">
                                        <i class="bi bi-book"></i><?= $chapterCount ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if ($showMore): ?>
                            <a class="series-more" href="<?= base_path('/lib') ?>">
                                <div>
                                    <i class="bi bi-arrow-right-circle d-block mb-1" style="font-size: 1.5rem;"></i>
                                    <span>Ver todas</span>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Blog Section -->
            <?php if (!empty($newsBelowMostRead)): ?>
            <section class="content-section" role="region" aria-labelledby="blog-title">
                <div class="section-header">
                    <h2 class="section-title" id="blog-title">
                        <i class="bi bi-newspaper me-2"></i>Novidades do Blog
                    </h2>

                </div>
                <?php $mainFeatured = $newsBelowMostRead[0] ?? null; ?>
                <?php $mainOthers = array_slice($newsBelowMostRead, 1); ?>

                <?php if (!empty($mainFeatured)): ?>
                    <?php
                    $fDateRaw = (string)($mainFeatured['published_at'] ?? $mainFeatured['created_at'] ?? '');
                    $fAgo = $fDateRaw !== '' ? time_ago($fDateRaw) : 'nunca';
                    $fAuthor = (string)($mainFeatured['author_name'] ?? $mainFeatured['author'] ?? 'Equipe');
                    $fExcerpt = $newsExcerpt((string)($mainFeatured['body'] ?? ''), 180);
                    $fImage = trim((string)($mainFeatured['featured_image_path'] ?? ''));
                    ?>
                    <div class="news-featured mb-3">
                        <?php if ($fImage !== ''): ?>
                            <img src="<?= base_path('/' . ltrim($fImage, '/')) ?>" alt="<?= View::e((string)($mainFeatured['title'] ?? 'Destaque')) ?>" class="news-image">
                        <?php endif; ?>
                        <div class="news-body">
                            <a class="news-title" href="<?= base_path('/news/' . (int)$mainFeatured['id']) ?>">
                                <?= View::e((string)($mainFeatured['title'] ?? 'Notícia')) ?>
                            </a>
                            <div class="news-excerpt">
                                <?= View::e($fExcerpt) ?>
                            </div>
                            <div class="news-footer">
                                <div class="news-meta">
                                    <i class="bi bi-clock"></i><?= View::e($fAgo) ?> · <i class="bi bi-person"></i><?= View::e($fAuthor) ?>
                                </div>
                                <a class="news-link" href="<?= base_path('/news/' . (int)$mainFeatured['id']) ?>">
                                    Ler mais <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($mainOthers)): ?>
                    <div class="news-list">
                        <?php foreach ($mainOthers as $n): ?>
                            <?php
                            $dateRaw = (string)($n['published_at'] ?? $n['created_at'] ?? '');
                            $metaAgo = $dateRaw !== '' ? time_ago($dateRaw) : 'nunca';
                            $author = (string)($n['author_name'] ?? $n['author'] ?? 'Equipe');
                            $excerpt = $newsExcerpt((string)($n['body'] ?? ''), 100);
                            $itemImage = trim((string)($n['featured_image_path'] ?? ''));
                            ?>
                            <div class="news-item">
                                <?php if ($itemImage !== ''): ?>
                                    <img src="<?= base_path('/' . ltrim($itemImage, '/')) ?>" alt="<?= View::e((string)$n['title']) ?>" class="news-item-image">
                                <?php endif; ?>
                                <div class="news-item-content">
                                    <a class="news-item-title" href="<?= base_path('/news/' . (int)$n['id']) ?>">
                                        <?= View::e((string)$n['title']) ?>
                                    </a>
                                    <div class="news-item-meta">
                                        <span><i class="bi bi-clock"></i><?= View::e($metaAgo) ?></span>
                                        <span><i class="bi bi-person"></i><?= View::e($author) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
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
