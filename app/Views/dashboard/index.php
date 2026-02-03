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
<h1 class="h4 mb-3">Bem-vindo, <?= View::e($user['username'] ?? 'usuário') ?></h1>
<div class="card mb-3">
    <div class="card-body">
        <p class="mb-0">Acompanhe suas séries favoritas e as novidades mais recentes.</p>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <h2 class="h5">Séries favoritas</h2>
        <?php if (empty($favoriteSeries)): ?>
            <div class="alert alert-secondary">Você ainda não favoritou nenhuma série. <a href="<?= base_path('/libraries') ?>">Explorar bibliotecas</a></div>
        <?php else: ?>
            <div class="list-group mb-3">
                <?php foreach ($favoriteSeries as $s): ?>
                    <?php $seriesName = (string)($s['name'] ?? ''); ?>
                    <?php $categoryName = (string)($s['category_name'] ?? ''); ?>
                    <div class="list-group-item d-flex align-items-center gap-2">
                        <div class="flex-grow-1">
                            <a class="text-decoration-none fw-semibold" href="<?= base_path('/libraries/' . rawurlencode($categoryName) . '/' . rawurlencode($seriesName)) ?>">
                                <?= View::e($seriesName) ?>
                            </a>
                            <div class="small text-muted">Categoria: <?= View::e($categoryName) ?> • Capítulos: <?= (int)($s['chapter_count'] ?? 0) ?></div>
                        </div>
                        <a class="btn btn-sm btn-outline-primary" href="<?= base_path('/libraries/' . rawurlencode($categoryName) . '/' . rawurlencode($seriesName)) ?>">Abrir</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h2 class="h5">Séries mais lidas</h2>
        <?php if (empty($mostReadSeries)): ?>
            <div class="alert alert-secondary">Ainda não há leituras registradas.</div>
        <?php else: ?>
            <div class="list-group mb-3">
                <?php foreach ($mostReadSeries as $mr): ?>
                    <?php $mrName = (string)($mr['name'] ?? ''); ?>
                    <?php $mrCategory = (string)($mr['category_name'] ?? ''); ?>
                    <?php $mrCatId = (int)($mr['category_id'] ?? 0); ?>
                    <div class="list-group-item d-flex align-items-center gap-2">
                        <div class="flex-grow-1">
                            <a class="text-decoration-none fw-semibold" href="<?= base_path('/libraries/' . rawurlencode($mrCategory) . '/' . rawurlencode($mrName)) ?>">
                                <?= View::e($mrName) ?>
                            </a>
                            <div class="small text-muted d-flex flex-wrap align-items-center gap-2">
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
                                <?php if (!empty($mr['has_pdf'])): ?>
                                    <span class="badge bg-warning text-dark">PDF</span>
                                <?php endif; ?>
                                <span><?= (int)($mr['read_count'] ?? 0) ?> leituras</span>
                            </div>
                        </div>
                        <a class="btn btn-sm btn-outline-primary" href="<?= base_path('/libraries/' . rawurlencode($mrCategory) . '/' . rawurlencode($mrName)) ?>">Abrir</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-4">
        <h2 class="h5">Notícias</h2>
        <?php if (empty($news)): ?>
            <div class="alert alert-secondary">Sem notícias.</div>
        <?php else: ?>
            <ul class="list-group mb-3">
                <?php foreach ($news as $n): ?>
                    <li class="list-group-item">
                        <strong><?= View::e($n['title']) ?></strong>
                        <div class="small text-muted mb-1"><?= View::e((string)($n['published_at'] ?? $n['created_at'])) ?></div>
                        <div><?= nl2br(View::e(mb_strimwidth((string)$n['body'], 0, 140, '...'))) ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($isAdmin)): ?>
            <h2 class="h5 mt-4">Últimos usuários conectados</h2>
            <?php if (empty($recentUsers)): ?>
                <div class="alert alert-secondary">Nenhum acesso registrado.</div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($recentUsers as $ru): ?>
                        <?php $lastLogin = $ru['data_ultimo_login'] ?? $ru['data_registro'] ?? null; ?>
                        <div class="list-group-item d-flex align-items-center justify-content-between gap-2">
                            <div class="fw-semibold"><?= View::e((string)($ru['username'] ?? '')) ?></div>
                            <div class="small text-muted text-end"><?= View::e(time_ago(is_string($lastLogin) ? $lastLogin : null)) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
