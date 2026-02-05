<?php
use App\Core\View;
ob_start();
$currentRole = $currentUser['role'] ?? 'user';
$isSuper = $currentRole === 'superadmin';
$isAdmin = $currentRole === 'admin' || $isSuper;
$isModerator = $currentRole === 'equipe' && !empty($currentUser['moderator_agent']);
$canManageAdmins = $isSuper;
$canManageSupport = $isAdmin;
$canManageUploaders = $isAdmin || $isModerator;
$canManageModerators = $isAdmin;
?>
<h1 class="h4 mb-3">Equipe</h1>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted small">Gerencie equipe e permissões.</div>
    <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addTeamMemberModal">
        Adicionar membro
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm">
        <thead>
        <tr>
            <th>Usuário</th>
            <th>Email</th>
            <th>Cargo</th>
            <th class="text-center">Suporte</th>
            <th class="text-center">Uploader</th>
            <th class="text-center">Moderador</th>
            <th class="text-end">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (($teamMembers ?? []) as $u): ?>
            <?php $isRowSuper = ($u['role'] ?? '') === 'superadmin'; ?>
            <tr>
                <td><?= View::e($u['username']) ?></td>
                <td><?= View::e($u['email']) ?></td>
                <td>
                    <form method="post" action="<?= base_path('/admin/team/update') ?>" class="d-flex gap-2">
                        <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                        <select name="role" class="form-select form-select-sm" <?= $canManageAdmins ? '' : 'disabled' ?> <?= $isRowSuper ? 'disabled' : '' ?>>
                            <option value="user" <?= ($u['role'] ?? '') === 'user' ? 'selected' : '' ?>>usuário</option>
                            <option value="equipe" <?= ($u['role'] ?? '') === 'equipe' ? 'selected' : '' ?>>equipe</option>
                            <?php if ($canManageAdmins): ?>
                                <option value="admin" <?= ($u['role'] ?? '') === 'admin' ? 'selected' : '' ?>>admin</option>
                            <?php endif; ?>
                            <?php if ($isRowSuper): ?>
                                <option value="superadmin" selected>superadmin</option>
                            <?php endif; ?>
                        </select>
                </td>
                <td class="text-center">
                        <input type="checkbox" name="support_agent" value="1" <?= !empty($u['support_agent']) ? 'checked' : '' ?> <?= $canManageSupport && !$isRowSuper ? '' : 'disabled' ?>>
                </td>
                <td class="text-center">
                        <input type="checkbox" name="uploader_agent" value="1" <?= !empty($u['uploader_agent']) ? 'checked' : '' ?> <?= $canManageUploaders && !$isRowSuper ? '' : 'disabled' ?>>
                </td>
                <td class="text-center">
                        <input type="checkbox" name="moderator_agent" value="1" <?= !empty($u['moderator_agent']) ? 'checked' : '' ?> <?= $canManageModerators && !$isRowSuper ? '' : 'disabled' ?>>
                </td>
                <td class="text-end">
                        <button class="btn btn-sm btn-primary" type="submit" <?= $isRowSuper ? 'disabled' : '' ?>>Salvar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="addTeamMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar membro à equipe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="post" action="<?= base_path('/admin/team/update') ?>">
                <div class="modal-body">
                    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                    <div class="mb-3">
                        <label class="form-label">Buscar usuário</label>
                        <input class="form-control" type="text" id="teamUserSearch" placeholder="Digite o nome ou email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Usuário</label>
                        <select name="id" id="teamUserSelect" class="form-select" size="8" required>
                            <?php foreach (($userPool ?? []) as $u): ?>
                                <option value="<?= (int)$u['id'] ?>">#<?= (int)$u['id'] ?> — <?= View::e((string)$u['username']) ?> (<?= View::e((string)$u['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Somente usuários sem equipe.</div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Cargo</label>
                            <select name="role" class="form-select">
                                <option value="equipe" selected>equipe</option>
                                <?php if ($canManageAdmins): ?>
                                    <option value="admin">admin</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Suporte</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="support_agent" value="1" id="addSupportFlag" <?= $canManageSupport ? '' : 'disabled' ?>>
                                <label class="form-check-label" for="addSupportFlag">Ativo</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Uploader</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="uploader_agent" value="1" id="addUploaderFlag" <?= $canManageUploaders ? '' : 'disabled' ?>>
                                <label class="form-check-label" for="addUploaderFlag">Ativo</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Moderador</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="moderator_agent" value="1" id="addModeratorFlag" <?= $canManageModerators ? '' : 'disabled' ?>>
                                <label class="form-check-label" for="addModeratorFlag">Ativo</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        var input = document.getElementById('teamUserSearch');
        var select = document.getElementById('teamUserSelect');
        if (!input || !select) return;
        input.addEventListener('input', function () {
            var term = input.value.toLowerCase();
            Array.prototype.slice.call(select.options).forEach(function (opt) {
                var text = opt.text.toLowerCase();
                opt.hidden = term && text.indexOf(term) === -1;
            });
        });
    })();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
