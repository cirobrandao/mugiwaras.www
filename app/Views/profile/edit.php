<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Editar meu perfil</h1>
<div class="alert alert-secondary">Em desenvolvimento.</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
