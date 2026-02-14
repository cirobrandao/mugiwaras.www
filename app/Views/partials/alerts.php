<?php
/**
 * Partial: User Alerts and Notifications
 * Displays subscription status and active notifications
 * Used in: dashboard/index.php, news/show.php
 */

use App\Core\Auth;
use App\Core\View;

$notifications = (array)($notifications ?? []);
$user = $user ?? null;
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
        $subscriptionWarningHours = (int)config('app.limits.subscription_warning_hours', 48);
        $warningThreshold = $subscriptionWarningHours * 3600;

        if ($remaining > 0) {
            $accessAlertClass = $remaining <= $warningThreshold ? 'warning' : 'success';
            $accessAlertText = 'Assinatura ativa (' . $remDays . ' dia' . ($remDays !== 1 ? 's' : '') . ' restante' . ($remDays !== 1 ? 's' : '') . ').';
            $accessAlertCountdown = (string)$accessInfo['expires_at'];
            $accessAlertExpires = date('d/m/Y H:i', $expTs);
            $accessAlertShowCountdown = true;
        } else {
            $accessAlertClass = 'danger';
            $accessAlertText = 'Assinatura expirada.';
        }
    }
}

$priorityMap = [
    'high' => ['class' => 'danger', 'icon' => 'bi-exclamation-octagon-fill'],
    'medium' => ['class' => 'warning', 'icon' => 'bi-exclamation-triangle-fill'],
    'low' => ['class' => 'info', 'icon' => 'bi-info-circle-fill'],
];
?>

<div class="mb-3 avisos-section">
    <?php if (!empty($user)): ?>
        <div class="alert alert-<?= $accessAlertClass ?> d-flex align-items-start gap-2 mb-2">
            <?php
            $accessIconMap = [
                'success' => 'bi-check-circle-fill',
                'warning' => 'bi-exclamation-triangle-fill',
                'danger' => 'bi-x-circle-fill',
                'secondary' => 'bi-dash-circle-fill',
            ];
            $accessIcon = $accessIconMap[$accessAlertClass] ?? 'bi-info-circle-fill';
            ?>
            <i class="bi <?= $accessIcon ?> align-self-start"></i>
            <div class="flex-fill">
                <div><?= View::e($accessAlertText) ?></div>
                <?php if ($accessAlertShowCountdown && $accessAlertCountdown !== null): ?>
                    <div class="small mt-1">
                        Expira em: <strong><?= View::e($accessAlertExpires) ?></strong>
                        <span id="accessCountdown" data-expires="<?= View::e($accessAlertCountdown) ?>"></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php foreach ($notifications as $notif): ?>
            <?php
            $nId = (int)($notif['id'] ?? 0);
            $nPriority = (string)($notif['priority'] ?? 'low');
            $nMessage = (string)($notif['message'] ?? '');
            $nTitle = (string)($notif['title'] ?? '');
            $notifMeta = $priorityMap[$nPriority] ?? $priorityMap['low'];
            $notifClass = $notifMeta['class'];
            $notifIcon = $notifMeta['icon'];
            $notifKey = 'notif_' . $nId;
            ?>
            <div class="alert alert-<?= $notifClass ?> d-flex align-items-start gap-2 mb-2 user-notification" data-notif-key="<?= View::e($notifKey) ?>">
                <i class="bi <?= $notifIcon ?> align-self-start"></i>
                <div class="flex-fill">
                    <?php if ($nTitle !== ''): ?>
                        <div class="fw-semibold"><?= View::e($nTitle) ?></div>
                    <?php endif; ?>
                    <div><?= View::e($nMessage) ?></div>
                </div>
                <button type="button" class="btn-close align-self-start" data-dismiss-notif="<?= View::e($notifKey) ?>" aria-label="Fechar"></button>
            </div>
        <?php endforeach; ?>

        <div id="restoreNotificationsLink" class="d-none">
            <a href="#" class="small text-decoration-none" onclick="event.preventDefault(); restoreNotifications();">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Recuperar notificações fechadas
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = `${name}=${value}; expires=${d.toUTCString()}; path=/; SameSite=Lax`;
}

function removeCookie(name) {
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; SameSite=Lax`;
}

function hasClosedNotifications() {
    const userId = <?= (int)($user['id'] ?? 0) ?>;
    if (!userId) return false;
    const cookies = document.cookie.split(';');
    for (let c of cookies) {
        const trimmed = c.trim();
        if (trimmed.startsWith(`mw_alert_closed_u${userId}_notif_`)) {
            return true;
        }
    }
    return false;
}

function restoreNotifications() {
    const userId = <?= (int)($user['id'] ?? 0) ?>;
    if (!userId) return;
    const cookies = document.cookie.split(';');
    for (let c of cookies) {
        const trimmed = c.trim();
        if (trimmed.startsWith(`mw_alert_closed_u${userId}_notif_`)) {
            const name = trimmed.split('=')[0];
            removeCookie(name);
        }
    }
    location.reload();
}

document.addEventListener('DOMContentLoaded', () => {
    const userId = <?= (int)($user['id'] ?? 0) ?>;
    if (!userId) return;

    // Hide closed notifications
    document.querySelectorAll('[data-notif-key]').forEach(el => {
        const key = el.getAttribute('data-notif-key');
        if (getCookie(`mw_alert_closed_u${userId}_${key}`)) {
            el.style.display = 'none';
        }
    });

    // Setup dismiss buttons
    document.querySelectorAll('[data-dismiss-notif]').forEach(btn => {
        btn.addEventListener('click', () => {
            const key = btn.getAttribute('data-dismiss-notif');
            setCookie(`mw_alert_closed_u${userId}_${key}`, '1', 30);
            const parent = btn.closest('[data-notif-key]');
            if (parent) {
                parent.style.display = 'none';
            }
            // Show restore link if now has closed notifications
            if (hasClosedNotifications()) {
                const restoreLink = document.getElementById('restoreNotificationsLink');
                if (restoreLink) restoreLink.classList.remove('d-none');
            }
        });
    });

    // Show restore link if user has closed notifications
    if (hasClosedNotifications()) {
        const restoreLink = document.getElementById('restoreNotificationsLink');
        if (restoreLink) restoreLink.classList.remove('d-none');
    }

    // Countdown timer
    const countdown = document.getElementById('accessCountdown');
    if (countdown) {
        const expiresStr = countdown.getAttribute('data-expires');
        if (expiresStr) {
            const updateCountdown = () => {
                const expTs = new Date(expiresStr.replace(' ', 'T')).getTime();
                const now = Date.now();
                const diff = Math.max(0, expTs - now);
                const days = Math.floor(diff / 86400000);
                const hours = Math.floor((diff % 86400000) / 3600000);
                const mins = Math.floor((diff % 3600000) / 60000);
                const secs = Math.floor((diff % 60000) / 1000);
                countdown.textContent = ` (${days}d ${hours}h ${mins}m ${secs}s)`;
            };
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
    }
});
</script>
