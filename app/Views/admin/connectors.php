<?php
use App\Core\View;
$metaRobots = 'noindex, nofollow, noarchive, nosnippet';
ob_start();
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-2">
                <i class="bi bi-plug"></i>
                Conectores de Scraper
            </h1>
            <p class="text-muted mb-0">Gerador de conectores para hakuneko/scraper - suporte a sites WordPress Madara</p>
        </div>
    </div>

    <?php if (!empty($setupError)): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Tabela de conectores não encontrada. Execute a migração <code>012_connectors.sql</code>.
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['created'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            Conector criado com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['updated'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            Conector atualizado!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['deleted'])): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <i class="bi bi-info-circle me-2"></i>
            Conector deletado.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php
                $errorMsg = match ($_GET['error']) {
                    'required' => 'Preencha todos os campos obrigatórios.',
                    'exists' => 'Já existe um conector para este site.',
                    'notfound' => 'Conector não encontrado.',
                    'empty' => 'Nenhum conector disponível para download.',
                    'zip' => 'Erro ao criar arquivo ZIP.',
                    'permission' => 'Você não tem permissão para executar esta ação.',
                    'notwp' => 'Site não é WordPress ou não pode ser acessado.',
                    'lowconfidence' => 'Confiança de detecção muito baixa. Verifique se o site é realmente WordPress Madara ou MangaStream.',
                    default => 'Erro desconhecido.',
                };
                echo View::e($errorMsg);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Create Form -->
    <?php if ($canCreate): ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-plus-circle me-2"></i>
                Criar Novo Conector
            </h5>
        </div>
        <div class="card-body">
            <form method="post" action="<?= base_path('/admin/connectors/create') ?>" id="createForm">
                <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-link-45deg"></i>
                            URL do Site
                        </label>
                        <div class="input-group">
                            <input type="url" class="form-control" name="url" id="siteUrl" 
                                   placeholder="https://exemplo.com" required>
                            <button type="button" class="btn btn-outline-secondary" id="detectBtn">
                                <i class="bi bi-search"></i>
                                Detectar Tema
                            </button>
                        </div>
                        <small class="text-muted">URL completa do site WordPress</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-tag"></i>
                            Nome/Label
                        </label>
                        <input type="text" class="form-control" name="label" id="siteLabel" 
                               placeholder="Nome do Site" required>
                        <small class="text-muted">Nome exibido no scraper</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-palette"></i>
                            Template/Tema
                        </label>
                        <select class="form-select" name="template" id="templateSelect">
                            <option value="WordPressMadara" selected>WordPress Madara</option>
                            <option value="WordPressMangaStream">WordPress MangaStream</option>
                            <option value="Custom" disabled>Custom (em breve)</option>
                        </select>
                        <small class="text-muted">Tema WordPress detectado</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-tags"></i>
                            Tags
                        </label>
                        <input type="text" class="form-control" name="tags" 
                               placeholder="manga, portuguese, webtoon" value="manga">
                        <small class="text-muted">Separadas por vírgula</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-code-slash"></i>
                            Seletor CSS Customizado (opcional)
                        </label>
                        <input type="text" class="form-control" name="custom_selector" id="customSelector"
                               placeholder="div.profile-manga div.post-title h1">
                        <small class="text-muted">Seletor CSS para queryTitleForURI (deixe vazio para usar padrão)</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-signpost"></i>
                            Path Customizado (opcional)
                        </label>
                        <input type="text" class="form-control" name="custom_path" id="customPath"
                               placeholder="/read/list-mode/">
                        <small class="text-muted">Apenas para MangaStream (auto-detectado se vazio)</small>
                    </div>
                </div>

                <div id="detectionResult" class="alert d-none mb-3"></div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Criar Conector
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('createForm').reset()">
                        <i class="bi bi-x-circle me-1"></i>
                        Limpar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info mb-4">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Somente visualização:</strong> Você pode visualizar e baixar conectores, mas não pode criar novos. Entre em contato com um moderador ou administrador.
    </div>
    <?php endif; ?>

    <!-- Connectors List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="bi bi-list-ul me-2"></i>
                Conectores Disponíveis (<?= count($items ?? []) ?>)
            </h5>
            <?php if (!empty($items)): ?>
                <a href="<?= base_path('/admin/connectors/download-all') ?>" class="btn btn-sm btn-success">
                    <i class="bi bi-download me-1"></i>
                    Baixar Todos (.zip)
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <?php if (empty($items)): ?>
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p class="mb-0">Nenhum conector criado ainda</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Nome</th>
                                <th style="width: 30%;">URL</th>
                                <th style="width: 15%;">Template</th>
                                <th style="width: 15%;">Criado em</th>
                                <th style="width: 15%;" class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?= View::e((string)$item['label']) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <code><?= View::e((string)$item['class_name']) ?></code>
                                        </small>
                                        <?php if (!empty($item['tags'])): ?>
                                            <br>
                                            <?php 
                                                $tags = json_decode((string)$item['tags'], true) ?: [];
                                                foreach ($tags as $tag):
                                            ?>
                                                <span class="badge bg-secondary me-1"><?= View::e($tag) ?></span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= View::e((string)$item['url']) ?>" target="_blank" class="text-decoration-none">
                                            <?= View::e((string)$item['url']) ?>
                                            <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= View::e((string)$item['template']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y H:i', strtotime((string)$item['created_at'])) ?></small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_path('/admin/connectors/download?id=' . (int)$item['id']) ?>" 
                                               class="btn btn-success" title="Download .mjs">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <?php if ($canCreate): ?>
                                            <button type="button" class="btn btn-primary" 
                                                    onclick="editConnector(<?= (int)$item['id'] ?>)"
                                                    data-bs-toggle="modal" data-bs-target="#editModal">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <?php endif; ?>
                                            <?php if ($canDelete): ?>
                                            <button type="button" class="btn btn-danger" 
                                                    onclick="deleteConnector(<?= (int)$item['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>
                    Editar Conector
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= base_path('/admin/connectors/update') ?>" id="editForm">
                <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
                <input type="hidden" name="id" id="editId">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome/Label</label>
                        <input type="text" class="form-control" name="label" id="editLabel" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL</label>
                        <input type="url" class="form-control" name="url" id="editUrl" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Template</label>
                        <select class="form-select" name="template" id="editTemplate">
                            <option value="WordPressMadara">WordPress Madara</option>
                            <option value="WordPressMangaStream">WordPress MangaStream</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tags</label>
                        <input type="text" class="form-control" name="tags" id="editTags">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Seletor CSS Customizado</label>
                        <input type="text" class="form-control" name="custom_selector" id="editSelector">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Path Customizado (MangaStream)</label>
                        <input type="text" class="form-control" name="custom_path" id="editPath" placeholder="/read/list-mode/">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form method="post" action="<?= base_path('/admin/connectors/delete') ?>" id="deleteForm" style="display: none;">
    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
const connectors = <?= json_encode($items ?? []) ?>;

// Auto-detect theme
document.getElementById('detectBtn').addEventListener('click', async () => {
    const url = document.getElementById('siteUrl').value;
    const resultDiv = document.getElementById('detectionResult');
    const btn = document.getElementById('detectBtn');
    
    if (!url) {
        resultDiv.className = 'alert alert-warning';
        resultDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Digite uma URL primeiro';
        resultDiv.classList.remove('d-none');
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Detectando...';
    resultDiv.className = 'alert alert-info';
    resultDiv.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Analisando site...';
    resultDiv.classList.remove('d-none');
    
    try {
        const formData = new FormData();
        formData.append('url', url);
        
        const response = await fetch('<?= base_path('/admin/connectors/detect') ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.error) {
            resultDiv.className = 'alert alert-danger';
            resultDiv.innerHTML = '<i class="bi bi-x-circle me-2"></i>' + data.error;
        } else {
            const confidence = data.confidence || 'low';
            const alertClass = confidence === 'high' ? 'alert-success' : 
                              confidence === 'medium' ? 'alert-warning' : 'alert-danger';
            
            resultDiv.className = 'alert ' + alertClass;
            
            let html = '<strong>Detecção concluída:</strong><br>';
            html += 'WordPress: ' + (data.isWordPress ? '✓ Sim' : '✗ Não') + '<br>';
            html += 'Tema: ' + (data.theme || 'Desconhecido') + '<br>';
            html += 'Template sugerido: <code>' + (data.template || 'WordPressMadara') + '</code><br>';
            html += 'Confiança: <strong>' + confidence.toUpperCase() + '</strong>';
            
            // Mostrar warning se não for alta confiança
            if (data.warning) {
                html += '<br><br><i class="bi bi-exclamation-triangle me-2"></i>' + data.warning;
            }
            
            if (confidence === 'high') {
                html += '<br><br><i class="bi bi-check-circle me-2"></i>Pode criar o conector!';
            }
            
            resultDiv.innerHTML = html;
            
            // Auto-preencher template
            if (data.template) {
                document.getElementById('templateSelect').value = data.template;
            }
            
            // Auto-preencher path se MangaStream
            if (data.template === 'WordPressMangaStream' && data.path) {
                document.getElementById('customPath').value = data.path;
            }
            
            // Auto-fill label from URL if empty
            if (!document.getElementById('siteLabel').value) {
                try {
                    const urlObj = new URL(url);
                    const hostname = urlObj.hostname.replace('www.', '');
                    const name = hostname.split('.')[0];
                    document.getElementById('siteLabel').value = name.charAt(0).toUpperCase() + name.slice(1);
                } catch (e) {}
            }
        }
    } catch (error) {
        resultDiv.className = 'alert alert-danger';
        resultDiv.innerHTML = '<i class="bi bi-x-circle me-2"></i>Erro ao consultar: ' + error.message;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-search"></i> Detectar Tema';
    }
});

function editConnector(id) {
    const connector = connectors.find(c => c.id == id);
    if (!connector) return;
    
    document.getElementById('editId').value = connector.id;
    document.getElementById('editLabel').value = connector.label;
    document.getElementById('editUrl').value = connector.url;
    document.getElementById('editTemplate').value = connector.template;
    
    const tags = connector.tags ? JSON.parse(connector.tags) : [];
    document.getElementById('editTags').value = tags.join(', ');
    
    const config = connector.custom_config ? JSON.parse(connector.custom_config) : {};
    document.getElementById('editSelector').value = config.queryTitleForURI || '';
    document.getElementById('editPath').value = config.path || '';
}

function deleteConnector(id) {
    const connector = connectors.find(c => c.id == id);
    if (!connector) return;
    
    if (confirm('Deletar conector "' + connector.label + '"?\n\nEsta ação não pode ser desfeita.')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
