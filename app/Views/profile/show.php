<?php
use App\Core\View;
ob_start();
?>
<h1 class="h4 mb-3">Meu perfil</h1>
<div class="card">
    <div class="card-body">
        <div><strong>Usuário:</strong> <?= View::e((string)($user['username'] ?? '')) ?></div>
        <div><strong>Email:</strong> <?= View::e((string)($user['email'] ?? '')) ?></div>
        <div><strong>Telefone:</strong> <?= View::e((string)($user['phone'] ?? '')) ?></div>
        <div><strong>Nascimento:</strong> <?= View::e((string)($user['birth_date'] ?? '')) ?></div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
