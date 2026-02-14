<?php
use App\Core\View;
$hideHeader = true;
$isTermsStage = ($stage ?? 'terms') === 'terms';

if ($isTermsStage) {
    $authHeroTitle = 'Faça parte da comunidade';
    $authHeroText = 'Crie sua conta gratuita e tenha acesso a milhares de títulos exclusivos.';
    $authHeroFeatures = [
        [
            'icon' => 'bi bi-infinity',
            'title' => 'Acesso ilimitado',
            'text' => 'Leia quantos mangás quiser'
        ],
        [
            'icon' => 'bi bi-cloud-check',
            'title' => 'Sincronização automática',
            'text' => 'Leia em qualquer dispositivo'
        ],
        [
            'icon' => 'bi bi-shield-check',
            'title' => 'Seguro e confiável',
            'text' => 'Seus dados sempre protegidos'
        ]
    ];
} else {
    $authHeroTitle = 'Quase lá!';
    $authHeroText = 'Complete seu cadastro e comece a explorar nossa biblioteca de mangás agora mesmo.';
    $authHeroFeatures = [
        [
            'icon' => 'bi bi-person-check',
            'title' => 'Perfil personalizado',
            'text' => 'Crie seu espaço único'
        ],
        [
            'icon' => 'bi bi-bookmark-heart',
            'title' => 'Lista de favoritos',
            'text' => 'Organize suas leituras'
        ],
        [
            'icon' => 'bi bi-bell',
            'title' => 'Notificações',
            'text' => 'Saiba das novidades primeiro'
        ]
    ];
}

ob_start();
?>

<?php if ($isTermsStage): ?>
    <div class="auth-header">
        <h1>Termos de uso</h1>
        <p>Leia nossos termos para continuar</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                    <li><?= View::e($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['error']) && $_GET['error'] === 'terms'): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            Você precisa aceitar os termos de uso para continuar.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($terms)): ?>
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body">
                <div class="terms-scroll">
                    <?= nl2br(View::e((string)$terms)) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_path('/register/accept') ?>">
        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
        
        <div class="card border-primary mb-3">
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="accept_terms" id="accept_terms" required>
                    <label class="form-check-label" for="accept_terms">
                        <strong>Li e aceito os termos de uso</strong>
                        <div class="text-muted small mt-1">
                            Ao marcar esta caixa, você concorda com nossos termos e condições de uso da plataforma.
                        </div>
                    </label>
                </div>
            </div>
        </div>
        
        <button class="btn btn-primary w-100" type="submit">
            <i class="bi bi-arrow-right-circle me-2"></i>
            Continuar
        </button>
    </form>

    <div class="auth-footer">
        <span>Já tem uma conta?</span>
        <a href="<?= base_path('/') ?>">Faça login</a>
    </div>

<?php else: ?>
    <div class="auth-header">
        <h1>Criar conta</h1>
        <p>Preencha os dados para se cadastrar</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                    <li><?= View::e($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_path('/register') ?>" autocomplete="on">
        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
        <input type="hidden" name="accept_terms" value="1">
        
        <div class="mb-3">
            <label class="form-label" for="register-username">
                Usuário <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-at"></i>
                </span>
                <input 
                    id="register-username" 
                    type="text" 
                    name="username" 
                    class="form-control" 
                    placeholder="Digite seu usuário"
                    required 
                    autocomplete="username"
                    autocapitalize="none" 
                    oninput="this.value = this.value.toLowerCase()"
                >
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label" for="register-email">
                E-mail <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                </span>
                <input 
                    id="register-email" 
                    type="email" 
                    name="email" 
                    class="form-control" 
                    placeholder="seu@email.com"
                    required 
                    autocomplete="email"
                    autocapitalize="none" 
                    oninput="this.value = this.value.toLowerCase()"
                >
            </div>
        </div>
        
        <div class="row g-2 mb-3">
            <div class="col-4">
                <label class="form-label" for="register-phone-country">
                    DDI <span class="text-danger">*</span>
                </label>
                <input 
                    id="register-phone-country" 
                    type="text" 
                    name="phone_country" 
                    class="form-control" 
                    placeholder="+55" 
                    value="+55" 
                    required
                >
            </div>
            <div class="col-8">
                <label class="form-label" for="register-phone">
                    Telefone <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-phone"></i>
                    </span>
                    <input 
                        id="register-phone" 
                        type="text" 
                        name="phone" 
                        class="form-control" 
                        placeholder="11 9 1234-5678" 
                        required 
                        autocomplete="tel"
                        data-mask="phone"
                    >
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="no_whatsapp" id="no_whatsapp">
                <label class="form-check-label" for="no_whatsapp">
                    Não tenho WhatsApp neste número
                </label>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">
                Data de nascimento <span class="text-danger">*</span>
            </label>
            <div class="row g-2">
                <div class="col-4">
                    <select id="birth-day" class="form-select" name="birth_day" required data-birth-select="day">
                        <option value="">Dia</option>
                        <?php for ($d = 1; $d <= 31; $d++): ?>
                            <option value="<?= $d ?>"><?= str_pad((string)$d, 2, '0', STR_PAD_LEFT) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-4">
                    <select id="birth-month" class="form-select" name="birth_month" required data-birth-select="month">
                        <option value="">Mês</option>
                        <?php 
                        $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
                        for ($m = 1; $m <= 12; $m++): 
                        ?>
                            <option value="<?= $m ?>"><?= $months[$m-1] ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-4">
                    <select id="birth-year" class="form-select" name="birth_year" required data-birth-select="year">
                        <option value="">Ano</option>
                        <?php $currentYear = (int)date('Y'); ?>
                        <?php for ($y = $currentYear; $y >= $currentYear - 90; $y--): ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="text-danger small mt-1" data-birth-error style="display:none;">
                <i class="bi bi-exclamation-circle me-1"></i>
                Selecione dia, mês e ano válidos.
            </div>
            <input type="hidden" name="birth_date" data-birth-target="1">
        </div>
        
        <div class="mb-3">
            <label class="form-label" for="register-password">
                Senha <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-lock"></i>
                </span>
                <input 
                    id="register-password" 
                    type="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="Crie uma senha forte"
                    required
                    autocomplete="new-password"
                >
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label" for="register-password-confirm">
                Confirmar senha <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-lock-fill"></i>
                </span>
                <input 
                    id="register-password-confirm" 
                    type="password" 
                    name="password_confirm" 
                    class="form-control" 
                    placeholder="Digite a senha novamente"
                    required
                    autocomplete="new-password"
                >
            </div>
        </div>
        
        <div class="mb-4">
            <label class="form-label" for="register-referral">
                Código de indicação <span class="text-muted">(opcional)</span>
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-gift"></i>
                </span>
                <input 
                    id="register-referral" 
                    type="text" 
                    name="referral" 
                    class="form-control" 
                    placeholder="Digite o código se tiver"
                >
            </div>
        </div>
        
        <button class="btn btn-primary w-100 mb-3" type="submit">
            <i class="bi bi-person-plus me-2"></i>
            Criar minha conta
        </button>
    </form>

    <div class="auth-footer">
        <span>Já tem uma conta?</span>
        <a href="<?= base_path('/') ?>">Faça login</a>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';

