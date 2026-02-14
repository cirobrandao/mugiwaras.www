<?php
use App\Core\View;
$hideHeader = true;
$authHeroTitle = 'Como podemos ajudar?';
$authHeroText = 'Nossa equipe de suporte está pronta para responder suas dúvidas e resolver seus problemas.';
$authHeroFeatures = [
    [
        'icon' => 'bi bi-clock',
        'title' => 'Resposta rápida',
        'text' => 'Retornamos em até 24 horas'
    ],
    [
        'icon' => 'bi bi-people',
        'title' => 'Equipe dedicada',
        'text' => 'Profissionais qualificados'
    ],
    [
        'icon' => 'bi bi-check2-circle',
        'title' => 'Solução eficaz',
        'text' => 'Resolvemos seu problema'
    ]
];
ob_start();
?>
<div class="auth-header">
    <h1>Suporte</h1>
    <p>Envie sua mensagem</p>
</div>

<?php if (!empty($_GET['sent'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-start">
            <i class="bi bi-check-circle me-2"></i>
            <div style="font-size: 0.8125rem;">
                <strong>Mensagem enviada!</strong> Responderemos em breve.
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($_GET['sent']) && !empty($_GET['track']) && $_GET['track'] === '0'): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Acompanhamento de chamado não está disponível no momento.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <?= View::e($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle text-primary me-2"></i>
            <div style="font-size: 0.8125rem;">
                <strong>Dica:</strong> Descreva seu problema com detalhes.
            </div>
        </div>
    </div>
</div>

<form method="post" action="<?= base_path('/support') ?>" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
    
    <div class="mb-3">
        <label class="form-label" for="support-email">
            E-mail <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-envelope"></i>
            </span>
            <input 
                id="support-email" 
                type="email" 
                name="email" 
                class="form-control" 
                placeholder="seu@email.com"
                required 
                autocomplete="email"
                autocapitalize="none" 
                oninput="this.value = this.value.toLowerCase()"
            >
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label" for="support-subject">
            Assunto <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-chat-left-text"></i>
            </span>
            <input 
                id="support-subject" 
                type="text" 
                name="subject" 
                class="form-control" 
                placeholder="Resuma seu problema em poucas palavras"
                required 
                maxlength="120"
            >
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label" for="support-message">
            Mensagem <span class="text-danger">*</span>
        </label>
        <textarea 
            id="support-message" 
            name="message" 
            class="form-control" 
            rows="4" 
            placeholder="Descreva seu problema"
            required
        ></textarea>
    </div>
    
    <div class="mb-4">
        <label class="form-label" for="support-attachment">
            Anexo <span class="text-muted">(opcional)</span>
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-paperclip"></i>
            </span>
            <input 
                id="support-attachment" 
                type="file" 
                name="attachment" 
                class="form-control" 
                accept="image/*,application/pdf"
            >
        </div>
    </div>
    
    <div class="d-grid gap-2">
        <button class="btn btn-primary" type="submit">
            <i class="bi bi-send me-2"></i>
            Enviar
        </button>
        <a class="btn btn-outline-secondary" href="<?= base_path('/') ?>">
            <i class="bi bi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>
</form>

<div class="auth-footer">
    <span>Já tem uma conta?</span>
    <a href="<?= base_path('/') ?>">Faça login</a>
    <div class="mt-2">
        <span>Ainda não é cadastrado?</span>
        <a href="<?= base_path('/register') ?>">Cadastre-se gratuitamente</a>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';

