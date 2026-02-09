<?php
use App\Core\View;
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

$metaParts = [];
if (!empty($newsItem['published_at'])) {
    $metaParts[] = (string)$newsItem['published_at'];
} elseif (!empty($newsItem['created_at'])) {
    $metaParts[] = (string)$newsItem['created_at'];
}
$author = (string)($newsItem['author_name'] ?? $newsItem['author'] ?? '');
if ($author !== '') {
    $metaParts[] = $author;
}
$metaLine = implode(' · ', array_filter($metaParts));
?>

<?php if (!empty($accessInfo['expires_at'])): ?>
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
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1"><?= View::e((string)($newsItem['title'] ?? 'Noticia')) ?></h1>
        </div>
    </div>

            <?php if (!empty($newsItem['category_name'])): ?>
                <div class="mb-2">
                    <span class="badge bg-secondary"><?= View::e((string)$newsItem['category_name']) ?></span>
                </div>
            <?php endif; ?>
            
            <div><?= nl2br(View::e((string)($newsItem['body'] ?? ''))) ?></div>
            <hr class="text-success" />
            <?php if ($metaLine !== ''): ?>
                <div class="small text-muted mb-3"><?= View::e($metaLine) ?></div>
            <?php endif; ?>

    </div>

    <div class="col-12 col-xl-4">
        <section class="section-card">
            <div class="news-title-box">
                <div class="section-title news-title">➧ Plano Ativo</div>
            </div>
            <?php if (!empty($activePackageTitle) && !empty($accessInfo['expires_at'])): ?>
                <?php
                $expTs = strtotime((string)$accessInfo['expires_at']);
                $remaining = $expTs !== false ? max(0, $expTs - time()) : 0;
                $remDays = (int)floor($remaining / 86400);
                $remHours = (int)floor(($remaining % 86400) / 3600);
                $initialCountdown = $remDays . 'd ' . $remHours . 'h';
                ?>
                <div class="alert alert-warning py-2 mb-0 border-0 small">
                    Acesso: <?= View::e((string)$activePackageTitle) ?> expira em <span id="accessCountdown" data-expires="<?= View::e((string)$accessInfo['expires_at']) ?>"><?= View::e($initialCountdown) ?></span>.
                </div>
            <?php else: ?>
                <div class="alert alert-secondary py-2 mb-0 border-0 small">
                    <?= View::e((string)($accessInfo['label'] ?? '')) ?>
                </div>
            <?php endif; ?>
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

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
