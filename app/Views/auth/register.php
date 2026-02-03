<?php
use App\Core\View;
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-7">
        <h1 class="h4 mb-3">Registrar</h1>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= View::e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (($stage ?? 'terms') === 'terms'): ?>
            <?php if (!empty($terms)): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h2 class="h6">Termo de uso</h2>
                        <div class="small text-muted" style="max-height: 260px; overflow:auto;">
                            <?= nl2br(View::e((string)$terms)) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($_GET['error']) && $_GET['error'] === 'terms'): ?>
                <div class="alert alert-danger">Você precisa aceitar o termo para continuar.</div>
            <?php endif; ?>
            <form method="post" action="<?= base_path('/register/accept') ?>">
                <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="accept_terms" id="accept_terms" required>
                    <label class="form-check-label" for="accept_terms">Li e aceito o termo de uso.</label>
                </div>
                <button class="btn btn-primary w-100" type="submit">Continuar cadastro</button>
            </form>
        <?php else: ?>
            <form method="post" action="<?= base_path('/register') ?>">
                <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                <input type="hidden" name="accept_terms" value="1">
                <div class="mb-3">
                    <label class="form-label">Usuário</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-4 mb-3">
                        <label class="form-label">DDI</label>
                        <input type="text" name="phone_country" class="form-control" placeholder="+55" value="+55" required>
                    </div>
                    <div class="col-8 mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="phone" class="form-control" placeholder="00 0 0000-0000" required data-mask="phone">
                    </div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="no_whatsapp" id="no_whatsapp">
                    <label class="form-check-label" for="no_whatsapp">Não tenho WhatsApp</label>
                </div>
                <div class="mb-3">
                    <label class="form-label">Data de nascimento</label>
                    <div class="row g-2 align-items-center">
                        <div class="col-4">
                            <select class="form-select" name="birth_day" required data-birth-select="day">
                                <option value="">Dia</option>
                                <?php for ($d = 1; $d <= 31; $d++): ?>
                                    <option value="<?= $d ?>"><?= $d ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <select class="form-select" name="birth_month" required data-birth-select="month">
                                <option value="">Mês</option>
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>"><?= $m ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <select class="form-select" name="birth_year" required data-birth-select="year">
                                <option value="">Ano</option>
                                <?php $currentYear = (int)date('Y'); ?>
                                <?php for ($y = $currentYear; $y >= $currentYear - 90; $y--): ?>
                                    <option value="<?= $y ?>"><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="text-danger small mt-1" data-birth-error style="display:none;">Selecione dia, mês e ano.</div>
                    <input type="hidden" name="birth_date" data-birth-target="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmar senha</label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Código de indicação (opcional)</label>
                    <input type="text" name="referral" class="form-control">
                </div>
                <button class="btn btn-primary w-100" type="submit">Criar conta</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
