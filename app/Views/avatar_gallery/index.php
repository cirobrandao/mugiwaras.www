<?php
use App\Core\View;
ob_start();
$title = 'Galeria de Avatar';
?>
<h1 class="h4 mb-3">Galeria de Avatar</h1>
<div class="card">
    <div class="card-body">
        <p class="text-muted small mb-3">Use estes avatares publicos para personalizar seu perfil.</p>
        <?php if (empty($avatars)): ?>
            <div class="text-muted">Nenhum avatar ativo na galeria.</div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($avatars as $avatar): ?>
                    <?php
                        $avPath = (string)($avatar['file_path'] ?? '');
                        $avatarTitle = (string)($avatar['title'] ?? '');
                    ?>
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="border rounded p-2 d-flex flex-column align-items-center gap-2 h-100">
                            <img src="<?= base_path('/' . ltrim($avPath, '/')) ?>" alt="<?= View::e($avatarTitle !== '' ? $avatarTitle : 'Avatar') ?>" class="avatar-gallery-thumb">
                            <?php if ($avatarTitle !== ''): ?>
                                <div class="small text-muted text-center"><?= View::e($avatarTitle) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
