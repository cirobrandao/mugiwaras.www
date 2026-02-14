<?php
use App\Core\View;

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
