<?php
use App\Core\View;
$hideHeader = true;
$form = is_array($form ?? null) ? $form : [];
$username = (string)($form['username'] ?? '');
$email = (string)($form['email'] ?? '');
$phone = (string)($form['phone'] ?? '');
$birthDay = (string)($form['birth_day'] ?? '');
$birthMonth = (string)($form['birth_month'] ?? '');
$birthYear = (string)($form['birth_year'] ?? '');
ob_start();
?>
<h1 class="h4 mb-3">Recuperar conta</h1>
<p class="text-muted small">Confirme usuário, email, data de nascimento e telefone para redefinir sua senha.</p>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger d-flex flex-column gap-2">
        <span><?= View::e($error) ?></span>
        <a class="btn btn-sm btn-outline-danger align-self-start" href="<?= base_path('/support') ?>">Abrir suporte</a>
    </div>
<?php endif; ?>
<form method="post" action="<?= base_path('/recover') ?>">
    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
    <div class="mb-3">
        <label class="form-label" for="recover-username">Usuário</label>
        <input id="recover-username" type="text" name="username" class="form-control" required autocapitalize="none" oninput="this.value = this.value.toLowerCase()" value="<?= View::e($username) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label" for="recover-email">Email</label>
        <input id="recover-email" type="email" name="email" class="form-control" required autocapitalize="none" oninput="this.value = this.value.toLowerCase()" value="<?= View::e($email) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label" for="recover-birth-day">Data de nascimento</label>
        <div class="row g-2 align-items-center">
            <div class="col-4">
                <select id="recover-birth-day" class="form-select" name="birth_day" required data-birth-select="day">
                    <option value="">Dia</option>
                    <?php for ($d = 1; $d <= 31; $d++): ?>
                        <option value="<?= $d ?>" <?= ((string)$d === $birthDay) ? 'selected' : '' ?>><?= $d ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-4">
                <select id="recover-birth-month" class="form-select" name="birth_month" required data-birth-select="month">
                    <option value="">Mês</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= ((string)$m === $birthMonth) ? 'selected' : '' ?>><?= $m ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-4">
                <select id="recover-birth-year" class="form-select" name="birth_year" required data-birth-select="year">
                    <option value="">Ano</option>
                    <?php $currentYear = (int)date('Y'); ?>
                    <?php for ($y = $currentYear; $y >= $currentYear - 90; $y--): ?>
                        <option value="<?= $y ?>" <?= ((string)$y === $birthYear) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="text-danger small mt-1" data-birth-error style="display:none;">Selecione dia, mês e ano.</div>
        <input type="hidden" name="birth_date" data-birth-target="1" value="<?= View::e(($birthDay !== '' && $birthMonth !== '' && $birthYear !== '') ? sprintf('%02d-%02d-%04d', (int)$birthDay, (int)$birthMonth, (int)$birthYear) : '') ?>">
    </div>
    <div class="mb-3">
        <label class="form-label" for="recover-phone">Telefone</label>
        <input id="recover-phone" type="text" name="phone" class="form-control" required placeholder="11 9 9999-9999" data-mask="phone" maxlength="14" inputmode="numeric" value="<?= View::e($phone) ?>">
    </div>
    <button class="btn btn-primary" type="submit">Validar dados</button>
    <a class="btn btn-outline-secondary ms-2" href="<?= base_path('/') ?>">Voltar</a>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
