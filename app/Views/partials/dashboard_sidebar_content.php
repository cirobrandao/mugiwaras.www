<?php
use App\Core\View;

$notifications = (array)($notifications ?? []);
$accessAlertClass = (string)($accessAlertClass ?? 'secondary');
$accessIcon = (string)($accessIcon ?? 'bi-info-circle-fill');
$accessAlertText = (string)($accessAlertText ?? 'Sem acesso ativo');
$accessAlertShowCountdown = (bool)($accessAlertShowCountdown ?? false);
$accessAlertExpires = (string)($accessAlertExpires ?? '');
$accessAlertCountdown = (string)($accessAlertCountdown ?? '');
$activePackageTitle = (string)($activePackageTitle ?? '');

$mostReadTop = array_slice((array)($mostReadSeries ?? []), 0, 10);
$recentTop = array_slice((array)($recentContent ?? []), 0, 5);
?>

<div class="alert alert-<?= View::e($accessAlertClass) ?>" role="alert">
    <div class="alert-icon">
        <i class="bi <?= View::e($accessIcon) ?>"></i>
    </div>
    <div class="alert-content">
        <div class="alert-title"><?= View::e($accessAlertText) ?></div>
        <?php if ($accessAlertShowCountdown && $accessAlertExpires !== '' && $accessAlertCountdown !== ''): ?>
            <div class="alert-text">
                <?php if ($activePackageTitle !== ''): ?>
                    <span><i class="bi bi-box-seam me-1"></i><?= View::e($activePackageTitle) ?></span>
                    <span class="mx-1">·</span>
                <?php endif; ?>
                <span><i class="bi bi-hourglass-split me-1"></i><span id="accessCountdown" data-expires="<?= View::e($accessAlertExpires) ?>"><?= View::e($accessAlertCountdown) ?></span></span>
            </div>
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
        <div class="alert alert-<?= View::e($priorityClass) ?> js-dismissible-alert" data-alert-key="notification-<?= $notifId ?>" role="alert">
            <div class="alert-icon">
                <i class="bi <?= View::e($priorityIcon) ?>"></i>
            </div>
            <div class="alert-content">
                <div class="alert-title"><?= View::e((string)($notification['title'] ?? 'Notificação')) ?></div>
                <div class="alert-text"><?= View::e((string)($notification['body'] ?? '')) ?></div>
            </div>
            <button type="button" class="btn-close btn-close-sm js-alert-close" aria-label="Fechar alerta"></button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<div class="text-end d-none js-restore-wrapper">
    <a href="#" class="small text-decoration-none js-restore-notifications">Recuperar notificações fechadas</a>
</div>

<section class="section" role="region" aria-labelledby="top10-title">
    <div class="section-header">
        <h2 class="section-title" id="top10-title">
            <i class="bi bi-trophy-fill me-2"></i>Top 10 Mais Lidos
        </h2>
    </div>
    <?php if (empty($mostReadTop)): ?>
        <div class="alert alert-secondary mb-0">Ainda não há leituras registradas.</div>
    <?php else: ?>
        <div class="list">
            <?php $position = 1; ?>
            <?php foreach ($mostReadTop as $mr): ?>
                <?php $mrName = (string)($mr['name'] ?? ''); ?>
                <?php $mrCategory = (string)($mr['category_name'] ?? ''); ?>
                <?php $mrCatId = (int)($mr['category_id'] ?? 0); ?>
                <?php $mrCategorySlug = $mrCategory !== '' ? \App\Models\Category::generateSlug($mrCategory) : ''; ?>
                <div class="list-item">
                    <div class="rank-badge"><?= $position++ ?></div>
                    <div class="list-content">
                        <a class="list-title" href="<?= base_path('/lib/' . rawurlencode($mrCategorySlug) . '/' . rawurlencode($mrName)) ?>">
                            <?= View::e($mrName) ?>
                        </a>
                        <div class="list-meta">
                            <?php
                            $badgeClass = $mrCatId > 0 ? 'cat-badge-' . $mrCatId : 'bg-secondary';
                            $badgeStyle = '';
                            if (!empty($mr['resolved_tag_color'])) {
                                $badgeStyle = ' style="background-color: ' . View::e((string)$mr['resolved_tag_color']) . '; color: #fff;"';
                            }
                            ?>
                            <span class="badge list-badge <?= $badgeClass ?>"<?= $badgeStyle ?>>
                                <?= View::e($mrCategory) ?>
                            </span>
                            <span class="list-stat">
                                <i class="bi bi-eye-fill"></i><?= (int)($mr['read_count'] ?? 0) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="section" role="region" aria-labelledby="recent-title">
    <div class="section-header">
        <h2 class="section-title" id="recent-title">
            <i class="bi bi-stars me-2"></i>Últimos Lançamentos
        </h2>
    </div>
    <?php if (empty($recentContent)): ?>
        <div class="alert alert-secondary mb-0">Sem novos envios.</div>
    <?php else: ?>
        <div class="list">
            <?php foreach ($recentTop as $rc): ?>
                <?php $rcCategory = (string)($rc['category_name'] ?? ''); ?>
                <?php $rcSeries = (string)($rc['series_name'] ?? ''); ?>
                <?php $rcTitle = (string)($rc['title'] ?? ''); ?>
                <?php $rcSeriesLabel = $rcSeries; ?>
                <?php $rcTitleLabel = $rcTitle; ?>
                <?php $rcCatId = (int)($rc['category_id'] ?? 0); ?>
                <?php $isNewContent = !empty($rc['is_new']); ?>
                <?php $rcCategorySlug = $rcCategory !== '' ? \App\Models\Category::generateSlug($rcCategory) : ''; ?>
                <div class="list-item <?= $isNewContent ? 'has-new-content' : '' ?>">
                    <?php if ($isNewContent): ?>
                        <div class="new-indicator"></div>
                    <?php endif; ?>
                    <div class="recent-icon">
                        <i class="bi bi-stars"></i>
                    </div>
                    <div class="list-content">
                        <?php if ($rcCategory !== '' && $rcSeries !== ''): ?>
                            <a class="list-title" href="<?= base_path('/lib/' . rawurlencode($rcCategorySlug) . '/' . rawurlencode($rcSeries)) ?>">
                                <?= View::e($rcSeriesLabel) ?>
                            </a>
                        <?php else: ?>
                            <span class="list-title"><?= View::e($rcTitleLabel) ?></span>
                        <?php endif; ?>
                        <div class="list-meta">
                            <?php if ($rcCategory !== ''): ?>
                                <?php
                                $rcBadgeClass = $rcCatId > 0 ? 'cat-badge-' . $rcCatId : 'bg-secondary';
                                $rcBadgeStyle = '';
                                if (!empty($rc['category_tag_color'])) {
                                    $rcBadgeStyle = ' style="background-color: ' . View::e((string)$rc['category_tag_color']) . '; color: #fff;"';
                                }
                                ?>
                                <span class="badge list-badge <?= $rcBadgeClass ?>"<?= $rcBadgeStyle ?>><?= View::e($rcCategory) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($rc['created_at'])): ?>
                                <span class="list-stat">
                                    <i class="bi bi-clock"></i><?= View::e(time_ago((string)$rc['created_at'])) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>