<?php
use App\Core\View;
$hideHeader = true;
$authHeroTitle = 'Recupere sua conta';
$authHeroText = 'Esqueceu sua senha? Não se preocupe, vamos te ajudar a recuperar o acesso à sua conta de forma segura.';
$authHeroFeatures = [
    [
        'icon' => 'bi bi-shield-lock',
        'title' => 'Processo seguro',
        'text' => 'Validação em múltiplas etapas'
    ],
    [
        'icon' => 'bi bi-clock-history',
        'title' => 'Rápido e fácil',
        'text' => 'Recupere o acesso em minutos'
    ],
    [
        'icon' => 'bi bi-headset',
        'title' => 'Suporte disponível',
        'text' => 'Precisa de ajuda? Estamos aqui'
    ]
];

$form = is_array($form ?? null) ? $form : [];
$username = (string)($form['username'] ?? '');
$email = (string)($form['email'] ?? '');
$phone = phone_mask((string)($form['phone'] ?? ''));
$birthDay = (string)($form['birth_day'] ?? '');
$birthMonth = (string)($form['birth_month'] ?? '');
$birthYear = (string)($form['birth_year'] ?? '');
ob_start();
?>
<div class="auth-header">
    <?php if (!empty($systemLogo)): ?>
        <img src="<?= base_path('/' . ltrim((string)$systemLogo, '/')) ?>" alt="Logo" class="auth-logo-mobile">
    <?php endif; ?>
    <h1>Recuperar conta</h1>
    <p>Confirme seus dados para redefinir senha</p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <div class="mb-2"><?= View::e($error) ?></div>
        <a class="btn btn-sm btn-outline-danger" href="<?= base_path('/support') ?>">
            <i class="bi bi-headset me-1"></i>
            Abrir suporte
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="alert alert-info border-0 mb-3" role="alert">
    <div class="d-flex">
        <div class="me-2">
            <i class="bi bi-info-circle"></i>
        </div>
        <div style="font-size: 0.8125rem;">
            <strong>Segurança:</strong> Confirme todos os dados do cadastro.
        </div>
    </div>
</div>

<form method="post" action="<?= base_path('/recover') ?>" autocomplete="on">
    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
    
    <div class="mb-3">
        <label class="form-label" for="recover-username">
            Usuário <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-person"></i>
            </span>
            <input 
                id="recover-username" 
                type="text" 
                name="username" 
                class="form-control" 
                placeholder="Digite seu usuário"
                required 
                autocomplete="username"
                autocapitalize="none" 
                oninput="this.value = this.value.toLowerCase()" 
                value="<?= View::e($username) ?>"
            >
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label" for="recover-email">
            E-mail <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-envelope"></i>
            </span>
            <input 
                id="recover-email" 
                type="email" 
                name="email" 
                class="form-control" 
                placeholder="Digite seu e-mail"
                required 
                autocomplete="email"
                autocapitalize="none" 
                oninput="this.value = this.value.toLowerCase()" 
                value="<?= View::e($email) ?>"
            >
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label">
            Data de nascimento <span class="text-danger">*</span>
        </label>
        <div class="row g-2">
            <div class="col-4">
                <select id="recover-birth-day" class="form-select" name="birth_day" required data-birth-select="day">
                    <option value="">Dia</option>
                    <?php for ($d = 1; $d <= 31; $d++): ?>
                        <option value="<?= $d ?>" <?= ((string)$d === $birthDay) ? 'selected' : '' ?>>
                            <?= str_pad((string)$d, 2, '0', STR_PAD_LEFT) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-4">
                <select id="recover-birth-month" class="form-select" name="birth_month" required data-birth-select="month">
                    <option value="">Mês</option>
                    <?php 
                    $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
                    for ($m = 1; $m <= 12; $m++): 
                    ?>
                        <option value="<?= $m ?>" <?= ((string)$m === $birthMonth) ? 'selected' : '' ?>>
                            <?= $months[$m-1] ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-4">
                <select id="recover-birth-year" class="form-select" name="birth_year" required data-birth-select="year">
                    <option value="">Ano</option>
                    <?php $currentYear = (int)date('Y'); ?>
                    <?php for ($y = $currentYear; $y >= $currentYear - 90; $y--): ?>
                        <option value="<?= $y ?>" <?= ((string)$y === $birthYear) ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="text-danger small mt-1" data-birth-error style="display:none;">
            <i class="bi bi-exclamation-circle me-1"></i>
            Selecione dia, mês e ano válidos.
        </div>
        <input type="hidden" name="birth_date" data-birth-target="1" value="<?= View::e(($birthDay !== '' && $birthMonth !== '' && $birthYear !== '') ? sprintf('%02d-%02d-%04d', (int)$birthDay, (int)$birthMonth, (int)$birthYear) : '') ?>">
    </div>
    
    <div class="mb-4">
        <label class="form-label" for="recover-phone">
            Telefone <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-phone"></i>
            </span>
            <input 
                id="recover-phone" 
                type="text" 
                name="phone" 
                class="form-control" 
                placeholder="11 9 1234-5678" 
                required 
                autocomplete="tel"
                data-mask="phone" 
                maxlength="14" 
                inputmode="numeric" 
                value="<?= View::e($phone) ?>"
            >
        </div>
    </div>
    
    <div class="d-grid gap-2">
        <button class="btn btn-primary" type="submit">
            <i class="bi bi-shield-check me-2"></i>
            Validar e recuperar
        </button>
        <a class="btn btn-outline-secondary" href="<?= base_path('/') ?>">
            <i class="bi bi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>
</form>

<div class="auth-footer">
    <span>Não consegue recuperar?</span>
    <a href="<?= base_path('/support') ?>">
        <i class="bi bi-headset me-1"></i>
        Suporte
    </a>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';

