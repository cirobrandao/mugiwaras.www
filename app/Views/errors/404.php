<?php
ob_start();
?>
<h1 class="h4 mb-3">404</h1>
<p>Página não encontrada.</p>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
