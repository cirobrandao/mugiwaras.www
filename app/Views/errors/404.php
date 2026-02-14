<?php
use App\Core\View;
ob_start();
$message = $message ?? 'Pagina nao encontrada.';
?>
<div class="section-card app-page">
	<div class="d-flex flex-column gap-2">
		<div class="badge bg-secondary">Erro 404</div>
		<h1 class="h4 mb-1">Nao foi possivel acessar esta pagina</h1>
		<p class="text-muted mb-3"><?= View::e($message) ?></p>
		<div class="d-flex flex-wrap gap-2">
			<a class="btn btn-primary" href="<?= base_path('/') ?>">Voltar ao inicio</a>
			<a class="btn btn-outline-secondary" href="<?= base_path('/home') ?>">Ir para a página inicial</a>
		</div>
	</div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
