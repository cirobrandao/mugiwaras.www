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
<div class="row">
    <div class="col-lg-8">
        <h2 class="h5">Meus Favoritos</h2>
        <?php if (empty($favoriteSeries)): ?>
            <div class="alert alert-secondary">Você ainda não favoritou nenhuma série. <a href="<?= base_path('/libraries') ?>">Explorar bibliotecas</a></div>
        <?php else: ?>
            <?php $favoriteTop = array_slice($favoriteSeries ?? [], 0, 10); ?>
            <div class="list-group list-group-flush dashboard-list mb-2">
                <?php foreach ($favoriteTop as $s): ?>
                    <?php $seriesName = (string)($s['name'] ?? ''); ?>
                    <?php $categoryName = (string)($s['category_name'] ?? ''); ?>
                    <div class="list-group-item d-flex align-items-center justify-content-between gap-2 py-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <a class="text-decoration-none fw-semibold" href="<?= base_path('/libraries/' . rawurlencode($categoryName) . '/' . rawurlencode($seriesName)) ?>">
                                <?= View::e($seriesName) ?>
                            </a>
                            <span class="badge bg-secondary"><?= View::e($categoryName) ?></span>
                        </div>
                        <span class="small text-muted text-nowrap"><?= (int)($s['chapter_count'] ?? 0) ?> capítulos</span>
                    </div>
                <?php endforeach; ?>
                <div class="list-group-item text-end py-1">
                    <span class="text-muted small">Veja mais</span>
                </div>
            </div>
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-lg-6">
                <h2 class="h5">Top 10 séries mais lidas</h2>
                <?php $mostReadTop = array_slice($mostReadSeries ?? [], 0, 10); ?>
                <?php if (empty($mostReadTop)): ?>
                    <div class="alert alert-secondary">Ainda não há leituras registradas.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush dashboard-list mb-2">
                        <?php foreach ($mostReadTop as $mr): ?>
                            <?php $mrName = (string)($mr['name'] ?? ''); ?>
                            <?php $mrCategory = (string)($mr['category_name'] ?? ''); ?>
                            <?php $mrCatId = (int)($mr['category_id'] ?? 0); ?>
                            <div class="list-group-item d-flex align-items-center justify-content-between gap-2 py-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <a class="text-decoration-none fw-semibold" href="<?= base_path('/libraries/' . rawurlencode($mrCategory) . '/' . rawurlencode($mrName)) ?>">
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
            </div>
            <div class="col-lg-6">
                <h2 class="h5">Ultimos Lançamentos</h2>
                <?php if (empty($recentContent)): ?>
                    <div class="alert alert-secondary">Sem novos envios.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush dashboard-list mb-2">
                        <?php foreach ($recentContent as $rc): ?>
                            <?php $rcCategory = (string)($rc['category_name'] ?? ''); ?>
                            <?php $rcSeries = (string)($rc['series_name'] ?? ''); ?>
                            <?php $rcTitle = (string)($rc['title'] ?? ''); ?>
                            <?php $rcSeriesLabel = mb_strimwidth($rcSeries, 0, 40, '...'); ?>
                            <?php $rcTitleLabel = mb_strimwidth($rcTitle, 0, 40, '...'); ?>
                            <?php $rcCatId = (int)($rc['category_id'] ?? 0); ?>
                            <?php $rcIsNew = !empty($rc['is_new']); ?>
                            <div class="list-group-item d-flex align-items-center justify-content-between gap-2 py-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <?php if ($rcCategory !== '' && $rcSeries !== ''): ?>
                                        <a class="text-decoration-none fw-semibold" href="<?= base_path('/libraries/' . rawurlencode($rcCategory) . '/' . rawurlencode($rcSeries)) ?>">
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
            </div>
        </div>

        <?php if (!empty($newsBelowMostRead)): ?>
            <h2 class="h5">Notícias</h2>
            <div class="list-group mb-3">
                <?php foreach ($newsBelowMostRead as $n): ?>
                    <div class="list-group-item">
                        <strong><?= View::e($n['title']) ?></strong>
                        <?php if (!empty($n['category_name'])): ?>
                            <span class="badge bg-secondary ms-2"><?= View::e((string)$n['category_name']) ?></span>
                        <?php endif; ?>
                        <div class="small text-muted mb-1"><?= View::e((string)($n['published_at'] ?? $n['created_at'])) ?></div>
                        <div><?= nl2br(View::e(mb_strimwidth((string)$n['body'], 0, 140, '...'))) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-4">
        <div class="card mb-3 border-0">
            <div class="card-body p-0">
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
            </div>
        </div>
        <h2 class="h5">Novidades</h2>
        <?php if (empty($news)): ?>
            <div class="alert alert-secondary">Sem notícias.</div>
        <?php else: ?>
            <ul class="list-group mb-3">
                <?php foreach ($news as $n): ?>
                    <li class="list-group-item">
                        <strong><?= View::e($n['title']) ?></strong>
                        <?php if (!empty($n['category_name'])): ?>
                            <span class="badge bg-secondary ms-2"><?= View::e((string)$n['category_name']) ?></span>
                        <?php endif; ?>
                        <div class="small text-muted mb-1"><?= View::e((string)($n['published_at'] ?? $n['created_at'])) ?></div>
                        <div><?= nl2br(View::e(mb_strimwidth((string)$n['body'], 0, 140, '...'))) ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
