<?php
use App\Core\View;
ob_start();
$message = $message ?? 'Pagina nao encontrada.';
?>
<div class="error-404-wrapper">
	<div class="error-404-card">
		<div class="error-404-icon">
			<i class="bi bi-compass" aria-hidden="true"></i>
		</div>
		<div class="error-404-badge">
			<span class="badge">Erro 404</span>
		</div>
		<h1 class="error-404-title">Página não encontrada</h1>
		<p class="error-404-message"><?= View::e($message) ?></p>
		<div class="error-404-actions">
			<a class="btn btn-primary" href="<?= base_path('/') ?>">
				<i class="bi bi-house-door me-1" aria-hidden="true"></i>Voltar ao início
			</a>
			<a class="btn btn-outline-secondary" href="<?= base_path('/home') ?>">
				<i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Página inicial
			</a>
		</div>
	</div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
