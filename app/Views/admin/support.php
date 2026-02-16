<?php
use App\Core\View;
ob_start();
$labelMap = [
    'open' => 'Aberto',
    'in_progress' => 'Em andamento',
    'closed' => 'Fechado',
];
$badgeMap = [
    'open' => 'bg-secondary',
    'in_progress' => 'bg-warning text-dark',
    'closed' => 'bg-success',
];

$openMessages = [];
$closedMessages = [];
foreach ($messages ?? [] as $m) {
    if (($m['status'] ?? 'open') === 'closed') {
        $closedMessages[] = $m;
    } else {
        $openMessages[] = $m;
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Suporte</h1>
    <div class="text-muted small">
        <span class="badge bg-secondary"><?= count($openMessages) ?></span> Abertos
        <span class="ms-2 badge bg-success"><?= count($closedMessages) ?></span> Fechados
    </div>
</div>
<hr class="text-success mb-3" />

<?php if (empty($messages)): ?>
    <div class="alert alert-secondary">Sem mensagens.</div>
<?php else: ?>
    <!-- Abertos / Em andamento -->
    <?php if (!empty($openMessages)): ?>
    <div class="card mb-3">
        <div class="card-header bg-transparent py-2">
            <h2 class="h6 mb-0">Abertos / Em andamento (<?= count($openMessages) ?>)</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle admin-support-table small mb-0">
                <thead class="table-light" style="font-size: 0.75rem;">
                <tr>
                    <th scope="col" style="width: 60px;">ID</th>
                    <th scope="col" style="width: 120px;">Usuário</th>
                    <th scope="col">Assunto</th>
                    <th scope="col" style="width: 90px;" class="text-center">Status</th>
                    <th scope="col" class="text-center" style="width: 100px;">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($openMessages as $m): ?>
                    <tr class="<?= ($m['status'] ?? 'open') === 'in_progress' ? 'table-warning' : 'table-secondary' ?>">
                        <td class="fw-bold" style="font-size: 0.8rem;">#<?= str_pad((string)(int)$m['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td style="font-size: 0.8rem;">
                            <?php if (!empty($m['username']) && !empty($m['user_id'])): ?>
                                <button type="button" class="btn btn-link p-0 text-decoration-none user-info-btn" 
                                        data-user-id="<?= (int)$m['user_id'] ?>"
                                        data-username="<?= View::e((string)$m['username']) ?>"
                                        title="Ver informações do usuário">
                                    <i class="bi bi-person-circle me-1"></i><?= View::e((string)$m['username']) ?>
                                </button>
                            <?php elseif (!empty($m['username'])): ?>
                                <span class="text-muted">
                                    <i class="bi bi-person me-1"></i><?= View::e((string)$m['username']) ?>
                                </span>
                            <?php else: ?>
                                <?php $externalName = explode('@', (string)($m['email'] ?? ''))[0] ?? ''; ?>
                                <span class="text-muted">
                                    <i class="bi bi-envelope me-1"></i><?= View::e($externalName) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-link text-decoration-none p-0 text-start subject-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#ticketModal" 
                                    data-ticket-id="<?= (int)$m['id'] ?>">
                                <?= View::e(mb_strimwidth((string)$m['subject'], 0, 60, '…')) ?>
                            </button>
                            <?php if (!empty($m['admin_note'])): ?>
                                <i class="bi bi-sticky-fill text-warning ms-1" title="<?= View::e((string)$m['admin_note']) ?>"></i>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?= $badgeMap[$m['status'] ?? 'open'] ?? 'bg-secondary' ?>" style="font-size: 0.7rem;">
                                <?= View::e($labelMap[$m['status'] ?? 'open'] ?? 'Aberto') ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm action-buttons" role="group">
                                <button type="button" class="btn btn-outline-primary rounded-start" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#ticketModal" 
                                        data-ticket-id="<?= (int)$m['id'] ?>"
                                        title="Ver e responder">
                                    <i class="bi bi-chat-dots"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success rounded-end btn-quick-close" 
                                        data-ticket-id="<?= (int)$m['id'] ?>"
                                        title="Fechar">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-info">Nenhum chamado em aberto.</div>
    <?php endif; ?>

    <!-- Fechados -->
    <?php if (!empty($closedMessages)): ?>
    <div class="card">
        <div class="card-header bg-transparent py-2">
            <h2 class="h6 mb-0">Fechados (<?= count($closedMessages) ?>)</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle admin-support-table small mb-0">
                <thead class="table-light" style="font-size: 0.75rem;">
                <tr>
                    <th scope="col" style="width: 60px;">ID</th>
                    <th scope="col" style="width: 120px;">Usuário</th>
                    <th scope="col">Assunto</th>
                    <th scope="col" style="width: 90px;" class="text-center">Status</th>
                    <th scope="col" class="text-center" style="width: 100px;">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($closedMessages as $m): ?>
                    <tr class="table-light">
                        <td class="text-muted" style="font-size: 0.8rem;">#<?= str_pad((string)(int)$m['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td class="text-muted" style="font-size: 0.8rem;">
                            <?php if (!empty($m['username']) && !empty($m['user_id'])): ?>
                                <button type="button" class="btn btn-link p-0 text-decoration-none text-muted user-info-btn" 
                                        data-user-id="<?= (int)$m['user_id'] ?>"
                                        data-username="<?= View::e((string)$m['username']) ?>"
                                        title="Ver informações do usuário">
                                    <i class="bi bi-person-circle me-1"></i><?= View::e((string)$m['username']) ?>
                                </button>
                            <?php elseif (!empty($m['username'])): ?>
                                <span class="text-muted">
                                    <i class="bi bi-person me-1"></i><?= View::e((string)$m['username']) ?>
                                </span>
                            <?php else: ?>
                                <?php $externalName = explode('@', (string)($m['email'] ?? ''))[0] ?? ''; ?>
                                <span class="text-muted">
                                    <i class="bi bi-envelope me-1"></i><?= View::e($externalName) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-link text-decoration-none p-0 text-start text-muted subject-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#ticketModal" 
                                    data-ticket-id="<?= (int)$m['id'] ?>">
                                <?= View::e(mb_strimwidth((string)$m['subject'], 0, 60, '…')) ?>
                            </button>
                            <?php if (!empty($m['admin_note'])): ?>
                                <i class="bi bi-sticky-fill text-warning ms-1" title="<?= View::e((string)$m['admin_note']) ?>"></i>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?= $badgeMap[$m['status'] ?? 'closed'] ?? 'bg-success' ?>" style="font-size: 0.7rem;">
                                <?= View::e($labelMap[$m['status'] ?? 'closed'] ?? 'Fechado') ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm action-buttons" role="group">
                                <button type="button" class="btn btn-outline-primary rounded-start" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#ticketModal" 
                                        data-ticket-id="<?= (int)$m['id'] ?>"
                                        title="Ver">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary rounded-end btn-quick-reopen" 
                                        data-ticket-id="<?= (int)$m['id'] ?>"
                                        title="Reabrir">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Modal de Ticket -->
<div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketModalLabel">
                    <span id="modalTicketTitle">Carregando...</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="ticketModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Informações do Usuário -->
<div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userInfoModalLabel">
                    <i class="bi bi-person-circle me-2"></i>Informações do Usuário
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="userInfoModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="#" class="btn btn-primary" id="userProfileLink" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Abrir Perfil
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Forms ocultos para ações rápidas -->
<form method="post" action="<?= base_path('/admin/support/status') ?>" id="quickStatusForm" style="display: none;">
    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
    <input type="hidden" name="id" id="quickStatusId">
    <input type="hidden" name="status" id="quickStatusValue">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('ticketModal');
    const modalTitle = document.getElementById('modalTicketTitle');
    const modalBody = document.getElementById('ticketModalBody');
    const csrfToken = '<?= View::e($csrf) ?>';
    
    if (!modal) {
        console.error('Modal #ticketModal não encontrado!');
        return;
    }
    
    console.log('Sistema de modal inicializado');
    
    // Carregar ticket no modal
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const ticketId = button.getAttribute('data-ticket-id');
        
        console.log('Abrindo ticket ID:', ticketId);
        console.log('Botão clicado:', button);
        
        if (!ticketId) {
            console.error('Ticket ID não encontrado no botão');
            return;
        }
        
        // Reset modal
        modalTitle.textContent = 'Carregando...';
        modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>';
        
        // Construir URL correta usando base_path do PHP
        const url = '<?= base_path('/admin/support/') ?>' + ticketId + '?ajax=1';
        
        console.log('URL completa da requisição:', url);
        console.log('Base path configurado:', '<?= base_path('') ?>');
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                console.log('Resposta recebida:', response.status, response.statusText);
                if (!response.ok) {
                    throw new Error('Erro HTTP: ' + response.status);
                }
                return response.text();
            })
            .then(html => {
                console.log('HTML recebido, tamanho:', html.length);
                console.log('Primeiros 200 caracteres:', html.substring(0, 200));
                
                // Verificar se veio o layout completo (indicando que AJAX não foi detectado)
                if (html.includes('<!DOCTYPE') || html.includes('<html')) {
                    console.warn('⚠️ Aviso: Recebeu página completa ao invés do conteúdo. Detecção AJAX pode ter falhou.');
                }
                
                // Extrair título
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const h1 = doc.querySelector('h1');
                if (h1) {
                    modalTitle.textContent = h1.textContent.trim();
                    h1.remove();
                } else {
                    console.warn('H1 não encontrado no HTML');
                    modalTitle.textContent = 'Chamado #' + ticketId;
                }
                
                // Remover HR e outros elementos de layout
                const hrs = doc.querySelectorAll('hr');
                hrs.forEach(hr => hr.remove());
                
                // Remover elementos vazios no topo
                const firstChild = doc.body.firstElementChild;
                if (firstChild && firstChild.textContent.trim() === '') {
                    firstChild.remove();
                }
                
                // Inserir conteúdo
                const bodyContent = doc.body.innerHTML;
                if (!bodyContent || bodyContent.trim().length === 0) {
                    throw new Error('Conteúdo vazio recebido do servidor');
                }
                modalBody.innerHTML = bodyContent;
                
                console.log('Modal atualizado com sucesso');
                
                // Adicionar evento ao botão cancelar dentro do modal
                const cancelBtn = modalBody.querySelector('[data-bs-dismiss="modal"]');
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const modalEl = document.getElementById('ticketModal');
                        const bsModal = bootstrap.Modal.getInstance(modalEl);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Erro ao carregar ticket:', error);
                console.error('Stack trace:', error.stack);
                
                // URL correta para fallback
                const fallbackUrl = '<?= base_path('/admin/support/') ?>' + ticketId;
                
                // Oferecer opção de abrir em página nova
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Erro ao carregar dados do ticket:</strong> ${error.message}
                        <br><small>Verifique o console para mais detalhes.</small>
                    </div>
                    <div class="text-center mt-3">
                        <a href="${fallbackUrl}" class="btn btn-primary" target="_blank">
                            <i class="bi bi-box-arrow-up-right me-2"></i>Abrir em nova página
                        </a>
                    </div>
                `;
            });
    });
    
    // Ações rápidas (fechar/reabrir)
    document.querySelectorAll('.btn-quick-close, .btn-quick-reopen').forEach(btn => {
        btn.addEventListener('click', function() {
            const ticketId = this.getAttribute('data-ticket-id');
            const status = this.classList.contains('btn-quick-close') ? 'closed' : 'open';
            
            if (confirm('Tem certeza que deseja ' + (status === 'closed' ? 'fechar' : 'reabrir') + ' este chamado?')) {
                document.getElementById('quickStatusId').value = ticketId;
                document.getElementById('quickStatusValue').value = status;
                document.getElementById('quickStatusForm').submit();
            }
        });
    });
    
    // Modal de informações do usuário
    const userInfoModal = document.getElementById('userInfoModal');
    const userInfoModalBody = document.getElementById('userInfoModalBody');
    const userProfileLink = document.getElementById('userProfileLink');
    
    document.querySelectorAll('.user-info-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const userId = this.getAttribute('data-user-id');
            const username = this.getAttribute('data-username');
            
            console.log('Abrindo informações do usuário:', userId, username);
            
            // Resetar modal
            userInfoModalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>';
            
            // Atualizar link do perfil
            userProfileLink.href = '<?= base_path('/perfil/') ?>' + encodeURIComponent(username);
            
            // Abrir modal
            const bsModal = new bootstrap.Modal(userInfoModal);
            bsModal.show();
            
            // Buscar dados do usuário via fetch
            const url = '<?= base_path('/admin/users') ?>?q=' + encodeURIComponent(username);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro HTTP: ' + response.status);
                return response.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Extrair informações da tabela de usuários
                const userRow = doc.querySelector('table tbody tr');
                if (userRow) {
                    const cells = userRow.querySelectorAll('td');
                    if (cells.length >= 5) {
                        const userInfo = `
                            <div class="user-info-card">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bi bi-person-circle fs-1 text-primary me-3"></i>
                                            <div>
                                                <h5 class="mb-0">${username}</h5>
                                                <small class="text-muted">ID: ${userId}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Email</label>
                                        <p class="mb-0">${cells[1].textContent.trim()}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Pacote Atual</label>
                                        <p class="mb-0">${cells[2].textContent.trim()}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Cadastro</label>
                                        <p class="mb-0">${cells[3].textContent.trim()}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Último Acesso</label>
                                        <p class="mb-0">${cells[4].textContent.trim()}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        userInfoModalBody.innerHTML = userInfo;
                    } else {
                        userInfoModalBody.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Usuário encontrado. Clique em "Abrir Perfil" para ver todos os detalhes.</div>';
                    }
                } else {
                    userInfoModalBody.innerHTML = '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Usuário não encontrado na listagem.</div>';
                }
            })
            .catch(error => {
                console.error('Erro ao carregar informações do usuário:', error);
                userInfoModalBody.innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Usuário:</strong> ${username}<br>
                        <strong>ID:</strong> ${userId}<br><br>
                        <small>Clique em "Abrir Perfil" para ver todos os detalhes.</small>
                    </div>
                `;
            });
        });
    });
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
