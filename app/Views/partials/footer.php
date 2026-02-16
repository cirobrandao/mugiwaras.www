<?php
use App\Core\View;

// Online users count
$onlineCount = 0;
try {
    $db = \App\Core\Database::connection();
    
    // Online users (active in last 15 minutes)
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE data_ultimo_login IS NOT NULL AND data_ultimo_login >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $onlineCount = (int)($result['count'] ?? 0);
    
} catch (Exception $e) {
    // Silently fail if database query fails
}

// Server load calculation
$loadLabel = 'indisponivel';
$loadPercent = null;
if (function_exists('sys_getloadavg')) {
    $loads = sys_getloadavg();
    $load1 = is_array($loads) ? (float)($loads[0] ?? 0) : 0.0;
    $cpuCores = (int)(getenv('NUMBER_OF_PROCESSORS') ?: 0);
    if ($cpuCores <= 0 && is_readable('/proc/cpuinfo')) {
        $cpuInfo = file_get_contents('/proc/cpuinfo') ?: '';
        $cpuCores = max(0, substr_count($cpuInfo, 'processor'));
    }
    if ($cpuCores > 0) {
        $loadPercent = (int)round(min(100, ($load1 / $cpuCores) * 100));
        if ($loadPercent < 50) {
            $loadLabel = 'baixa';
        } elseif ($loadPercent < 75) {
            $loadLabel = 'media';
        } else {
            $loadLabel = 'alta';
        }
    }
}
?>
            <footer class="app-footer">
                <div>© <?= date('Y') ?> <?= View::e($systemName) ?></div>
                <div class="text-muted">
                    Carga do servidor: <?= View::e($loadLabel) ?>
                    <?php if ($loadPercent !== null): ?>
                        <span class="fw-semibold"><?= (int)$loadPercent ?>%</span>
                    <?php endif; ?>
                    ·
                    <i class="bi bi-people-fill me-1"></i><span class="fw-semibold"><?= $onlineCount ?></span> usuário<?= $onlineCount !== 1 ? 's' : '' ?> online
                    · Última atualização <span data-last-sync>agora</span>
                </div>
            </footer>
        </div>
    </div>

<script src="<?= base_path('/assets/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_path('/assets/js/phone-mask.js') ?>"></script>
<script src="<?= base_path('/assets/js/app.js') ?>"></script>
<script src="<?= base_path('/assets/js/theme.js') ?>"></script>
<?php if (!empty($footerScripts)): ?>
    <?= $footerScripts ?>
<?php endif; ?>
</body>
</html>
