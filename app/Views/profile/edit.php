<?php
use App\Core\View;
ob_start();
$form = (array)($form ?? []);
$username = (string)($form['username'] ?? ($user['username'] ?? ''));
$email = (string)($form['email'] ?? ($user['email'] ?? ''));
$phone = phone_mask((string)($form['phone'] ?? ($user['phone'] ?? '')));
$phoneCountry = (string)($form['phone_country'] ?? ($user['phone_country'] ?? ''));
$phoneWhatsApp = (int)($form['phone_has_whatsapp'] ?? ($user['phone_has_whatsapp'] ?? 0));
$birthDate = (string)($form['birth_date'] ?? ($user['birth_date'] ?? ''));
$selectedAvatarId = (int)($form['avatar_gallery_id'] ?? ($selectedAvatarId ?? 0));
$currentAvatarPath = (string)($user['avatar_path'] ?? '');
if ($birthDate === '0000-00-00') {
	$birthDate = '';
}
?>
<h1 class="h4 mb-3">Editar meu perfil</h1>
<?php if (!empty($error)): ?>
	<div class="alert alert-danger"><?= View::e((string)$error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
	<div class="alert alert-success"><?= View::e((string)$success) ?></div>
<?php endif; ?>
<div class="card">
	<div class="card-body">
		<form method="post" action="<?= base_path('/user/editar') ?>" enctype="multipart/form-data">
			<input type="hidden" name="_csrf" value="<?= View::e((string)($csrf ?? '')) ?>">
			<div class="row g-3 align-items-center mb-3">
				<div class="col-md-3">
					<div class="border rounded d-flex align-items-center justify-content-center profile-avatar-box">
						<?php if ($currentAvatarPath !== ''): ?>
							<img src="<?= base_path('/' . ltrim($currentAvatarPath, '/')) ?>" alt="Avatar" class="profile-avatar-img">
						<?php else: ?>
							<div class="text-muted small">Sem avatar</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="col-md-9">
					<label class="form-label">Enviar avatar</label>
					<input class="form-control" type="file" name="avatar" accept="image/*">
					<div class="form-text">Formatos: JPG, PNG, WEBP. Maximo 500x500px e 2MB.</div>
				</div>
			</div>
			<div class="row g-3">
				<div class="col-md-6">
					<label class="form-label">Usuario</label>
					<input class="form-control" name="username" value="<?= View::e($username) ?>" required autocapitalize="none" oninput="this.value = this.value.toLowerCase()">
				</div>
				<div class="col-md-6">
					<label class="form-label">Email</label>
					<input class="form-control" type="email" name="email" value="<?= View::e($email) ?>" required autocapitalize="none" oninput="this.value = this.value.toLowerCase()">
				</div>
				<div class="col-md-3">
					<label class="form-label">DDI</label>
					<input class="form-control" name="phone_country" value="<?= View::e($phoneCountry) ?>" placeholder="55">
				</div>
				<div class="col-md-6">
					<label class="form-label">Telefone</label>
					<input class="form-control" name="phone" value="<?= View::e($phone) ?>" placeholder="11 9 9999-9999" data-mask="phone" maxlength="14" inputmode="numeric">
					<div class="form-text">Formato: 11 9 9999-9999</div>
				</div>
				<div class="col-md-3">
					<label class="form-label d-block">WhatsApp</label>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="phone_has_whatsapp" id="phone_has_whatsapp" value="1" <?= $phoneWhatsApp > 0 ? 'checked' : '' ?>>
						<label class="form-check-label" for="phone_has_whatsapp">Ativo</label>
					</div>
				</div>
				<div class="col-md-4">
					<label class="form-label">Nascimento</label>
					<input class="form-control" type="date" name="birth_date" value="<?= View::e($birthDate) ?>">
				</div>
			</div>
			<div class="mt-4">
				<div class="d-flex align-items-center justify-content-between mb-2">
					<h2 class="h6 mb-0">Escolher da galeria</h2>
				</div>
				<?php if (empty($avatars)): ?>
					<div class="text-muted">Nenhum avatar ativo na galeria.</div>
				<?php else: ?>
					<div class="row g-3">
						<?php foreach ($avatars as $avatar): ?>
							<?php
								$avId = (int)($avatar['id'] ?? 0);
								$avPath = (string)($avatar['file_path'] ?? '');
								$isSelected = $avId > 0 && $avId === $selectedAvatarId;
							?>
							<div class="col-6 col-md-3 col-lg-2">
								<label class="w-100 border rounded p-2 d-flex flex-column align-items-center gap-2" style="cursor: pointer;">
									<img src="<?= base_path('/' . ltrim($avPath, '/')) ?>" alt="Avatar" class="avatar-gallery-thumb">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="avatar_gallery_id" value="<?= $avId ?>" <?= $isSelected ? 'checked' : '' ?>>
										<span class="form-check-label small">Selecionar</span>
									</div>
								</label>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="mt-3 d-flex gap-2">
				<button class="btn btn-primary" type="submit">Salvar</button>
				<a class="btn btn-outline-secondary" href="<?= base_path('/perfil') ?>">Cancelar</a>
			</div>
		</form>
	</div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
