# 🔄 GUIA DE MIGRAÇÃO: Sistema Atual → Novo Tema

## 📚 ÍNDICE

1. [Visão Geral](#visão-geral)
2. [Preparação](#preparação)
3. [Estrutura de Arquivos](#estrutura-de-arquivos)
4. [Migração Passo a Passo](#migração-passo-a-passo)
5. [Exemplos de Conversão](#exemplos-de-conversão)
6. [Troubleshooting](#troubleshooting)

---

## 1. VISÃO GERAL

### Antes (Sistema Atual)
```
/app/Views/layout.php         ← Layout com includes diretos
/app/Views/dashboard/index.php ← View com HTML + PHP misturado
/public/assets/css/app.css    ← CSS existente com classes específicas
```

### Depois (Novo Tema)
```
/app/Views/layout-new.php           ← Layout modular com partials
/app/Views/partials/
  ├── header.php                    ← <head> separado
  ├── sidebar.php                   ← Navegação lateral
  ├── topbar.php                    ← Barra superior
  └── footer.php                    ← Scripts e </body>
/app/Views/dashboard/index-new.php  ← View usando novo layout
/public/assets/css/theme.css        ← CSS novo (não sobrescreve app.css)
/public/assets/js/theme.js          ← JS novo
```

### Estratégia
- ✅ Manter arquivos atuais intactos
- ✅ Criar versões `-new` lado a lado
- ✅ Testar exaustivamente antes de substituir
- ✅ Rollback fácil se necessário

---

## 2. PREPARAÇÃO

### 2.1 Backup
```bash
# Criar branch Git
git checkout -b feature/novo-tema

# Backup do banco (opcional)
mysqldump -u user -p database > backup_$(date +%Y%m%d).sql

# Backup de arquivos críticos
cp -r app/Views app/Views.backup
cp -r public/assets public/assets.backup
```

### 2.2 Análise do Sistema Atual

#### Identificar páginas existentes:
```bash
find app/Views -name "*.php" -type f
```

#### Páginas típicas a migrar:
- `/dashboard` - Dashboard principal
- `/libraries` - Listagem de categorias
- `/libraries/{category}` - Séries por categoria
- `/libraries/{category}/{series}` - Volumes de uma série
- `/profile` - Perfil do usuário
- `/news` - Notícias
- `/support` - Suporte
- `/loja` - Loja
- `/admin/*` - Painel administrativo

#### Componentes comuns:
- Sidebar (navegação)
- Topbar (busca, notificações, avatar)
- Cards (favoritos, notícias, séries)
- Widgets (avisos, top 10, lançamentos)
- Paginação
- Formulários

---

## 3. ESTRUTURA DE ARQUIVOS

### 3.1 Criar diretórios
```bash
mkdir -p app/Views/partials
mkdir -p docs/theme-examples
```

### 3.2 Copiar arquivos do guia
```
docs/theme-examples/
├── layout-new.php           → app/Views/layout-new.php
├── partials/
│   ├── header.php           → app/Views/partials/header.php
│   ├── sidebar.php          → app/Views/partials/sidebar.php
│   ├── topbar.php           → app/Views/partials/topbar.php
│   └── footer.php           → app/Views/partials/footer.php
├── dashboard-new.php        → app/Views/dashboard/index-new.php
public/assets/
├── css/theme.css            (já criado)
└── js/theme.js              (já criado)
```

### 3.3 Incluir CSS/JS no header.php

Editar `app/Views/partials/header.php`:
```php
<!-- Bootstrap 5.3 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<!-- App CSS (existente) -->
<link href="<?= base_path('assets/css/app.css') ?>" rel="stylesheet">

<!-- Theme CSS (novo) -->
<link href="<?= base_path('assets/css/theme.css') ?>" rel="stylesheet">
```

---

## 4. MIGRAÇÃO PASSO A PASSO

### PASSO 1: Migrar Dashboard

#### 4.1.1 Analisar Controller atual
`app/Controllers/DashboardController.php`:
```php
public function index()
{
    $userId = Auth::id();
    
    // Buscar favoritos
    $favoritesStmt = $this->db->prepare("...");
    $favorites = $favoritesStmt->fetchAll();
    
    // Buscar notícias
    $news = /* ... */;
    
    // Buscar top 10
    $topSeries = /* ... */;
    
    // Renderizar view
    include APP_PATH . '/Views/dashboard/index.php';
}
```

#### 4.1.2 Adaptar Controller para novo layout
```php
public function index()
{
    $userId = Auth::id();
    $user = Auth::user(); // Dados completos do usuário
    
    // Buscar dados (mesma lógica)
    $favorites = /* ... */;
    $recentNews = /* ... */;
    $topSeries = /* ... */;
    $recentContent = /* ... */;
    
    // Avisos de acesso
    $accessAlerts = $this->getAccessAlerts($user);
    
    // Variáveis para o layout
    $pageTitle = 'Dashboard';
    $pageDescription = 'Painel principal';
    $currentPath = '/dashboard';
    $favoritesCount = count($favorites);
    $unreadNotifications = $this->getUnreadNotificationsCount($userId);
    $notificationCount = $unreadNotifications;
    
    // Capturar view content
    ob_start();
    include APP_PATH . '/Views/dashboard/index-content.php';
    $content = ob_get_clean();
    
    // Incluir layout
    include APP_PATH . '/Views/layout-new.php';
}

private function getAccessAlerts($user)
{
    $alerts = [];
    
    if (!empty($user['package_expires_at'])) {
        $expiresAt = strtotime($user['package_expires_at']);
        $daysLeft = ceil(($expiresAt - time()) / 86400);
        
        if ($daysLeft > 7) {
            $alerts[] = [
                'type' => 'success',
                'icon' => 'bi-check-circle-fill',
                'title' => 'Acesso Premium Ativo',
                'text' => 'Seu plano está ativo até ' . date('d/m/Y', $expiresAt)
            ];
        } elseif ($daysLeft > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'bi-exclamation-triangle-fill',
                'title' => 'Acesso Premium expira em breve',
                'text' => "Restam apenas {$daysLeft} dias"
            ];
        } else {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'bi-x-circle-fill',
                'title' => 'Acesso Premium Expirado',
                'text' => 'Renove seu plano'
            ];
        }
    }
    
    return $alerts;
}
```

#### 4.1.3 Criar view de conteúdo
`app/Views/dashboard/index-content.php`:
```php
<!-- Header -->
<div class="content-header">
    <h1 class="content-title">Olá, <?= View::e($user['name'] ?? 'Usuário') ?>! 👋</h1>
    <p class="content-subtitle">Bem-vindo de volta. Aqui está o que está acontecendo hoje.</p>
</div>

<!-- Main Grid -->
<div class="row g-4">
    
    <!-- LEFT: Main Content -->
    <div class="col-12 col-xl-8">
        
        <!-- Favoritos -->
        <section class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="section-title mb-0">
                    <i class="bi bi-star-fill text-warning me-2"></i>
                    Meus Favoritos
                </h2>
                <a href="<?= base_path('/favorites') ?>" class="text-decoration-none">
                    Ver todos →
                </a>
            </div>
            
            <?php if (empty($favorites)): ?>
                <div class="card-soft text-center py-5">
                    <i class="bi bi-star display-1 text-muted mb-3"></i>
                    <p class="text-muted">Você ainda não tem favoritos.</p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($favorites as $fav): ?>
                        <div class="col-12 col-md-6">
                            <a href="<?= base_path("/libraries/{$fav['category_slug']}/{$fav['series_slug']}") ?>" 
                               class="card-horizontal">
                                <div class="card-horizontal-icon">
                                    <i class="bi bi-book-fill"></i>
                                </div>
                                <div class="card-horizontal-content">
                                    <div class="card-horizontal-title"><?= View::e($fav['name']) ?></div>
                                    <div class="card-horizontal-subtitle">
                                        <?= View::e($fav['latest_chapter']) ?> • <?= View::e($fav['category_name']) ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        
        <!-- Notícias -->
        <section>
            <h2 class="section-title">
                <i class="bi bi-newspaper text-primary me-2"></i>
                Últimas Notícias
            </h2>
            <?php foreach ($recentNews as $news): ?>
                <div class="card-soft mb-3">
                    <!-- ... conteúdo da notícia ... -->
                </div>
            <?php endforeach; ?>
        </section>
        
    </div>
    
    <!-- RIGHT: Widgets -->
    <div class="col-12 col-xl-4">
        
        <!-- Avisos -->
        <?php if (!empty($accessAlerts)): ?>
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title">
                    <i class="bi bi-bell-fill"></i>
                    Avisos
                </h3>
            </div>
            <?php foreach ($accessAlerts as $alert): ?>
                <div class="widget-alert widget-alert-<?= $alert['type'] ?>">
                    <div class="widget-alert-icon">
                        <i class="bi <?= $alert['icon'] ?>"></i>
                    </div>
                    <div class="widget-alert-content">
                        <div class="widget-alert-title"><?= View::e($alert['title']) ?></div>
                        <div class="widget-alert-text"><?= View::e($alert['text']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Top 10 -->
        <div class="widget">
            <!-- ... -->
        </div>
        
        <!-- Lançamentos -->
        <div class="widget">
            <!-- ... -->
        </div>
        
    </div>
    
</div>
```

#### 4.1.4 Testar rota temporária
`config/routes.php`:
```php
// Rota temporária para testar novo layout
$router->get('/dashboard-new', 'DashboardController@indexNew');
```

`DashboardController.php`:
```php
public function indexNew()
{
    // Mesma lógica do index(), mas usando layout-new.php
    // ...
    include APP_PATH . '/Views/layout-new.php';
}
```

Acessar: `https://seu-dominio.com/dashboard-new`

---

### PASSO 2: Migrar Biblioteca (Categorias)

#### 4.2.1 Converter view atual
ANTES (`app/Views/libraries/category.php`):
```php
<div class="container">
    <h1>Categoria: <?= $categoryName ?></h1>
    <div class="row">
        <?php foreach ($series as $s): ?>
            <div class="col-md-4">
                <div class="card">
                    <h3><?= $s['name'] ?></h3>
                    <span><?= $s['volume_count'] ?> volumes</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

DEPOIS (`app/Views/libraries/category-content.php`):
```php
<!-- Header -->
<div class="content-header">
    <h1 class="content-title"><?= View::e($categoryName) ?></h1>
    <p class="content-subtitle"><?= count($series) ?> séries disponíveis</p>
</div>

<!-- Grid de Séries -->
<div class="row g-4">
    <?php foreach ($series as $s): ?>
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <a href="<?= base_path("/libraries/{$categorySlug}/" . slugify($s['name'])) ?>" 
               class="card-horizontal">
                <div class="card-horizontal-icon" style="background: <?= $s['gradient'] ?>;">
                    <i class="bi bi-book-fill"></i>
                </div>
                <div class="card-horizontal-content">
                    <div class="card-horizontal-title"><?= View::e($s['name']) ?></div>
                    <div class="card-horizontal-subtitle">
                        <?= $s['volume_count'] ?> volumes
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>
```

Controller adapta variáveis e inclui layout:
```php
public function category($categorySlug)
{
    // ... buscar dados ...
    
    $pageTitle = $categoryName;
    $currentPath = "/libraries/{$categorySlug}";
    $user = Auth::user();
    
    ob_start();
    include APP_PATH . '/Views/libraries/category-content.php';
    $content = ob_get_clean();
    
    include APP_PATH . '/Views/layout-new.php';
}
```

---

### PASSO 3: Migrar Perfil

#### 4.3.1 Formulários
Use classes do Bootstrap 5.3 + classes de card soft:

```php
<div class="card-soft">
    <div class="card-soft-header">
        <h3 class="card-soft-title">Informações Pessoais</h3>
    </div>
    <div class="card-soft-body">
        <form method="POST" action="<?= base_path('/profile/update') ?>">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" 
                       class="form-control" 
                       id="name" 
                       name="name" 
                       value="<?= View::e($user['name']) ?>" 
                       required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" 
                       class="form-control" 
                       id="email" 
                       name="email" 
                       value="<?= View::e($user['email']) ?>" 
                       required>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-2"></i>
                Salvar Alterações
            </button>
        </form>
    </div>
</div>
```

---

## 5. EXEMPLOS DE CONVERSÃO

### 5.1 Lista Antiga → Widget List

ANTES:
```php
<ul class="list-group">
    <?php foreach ($items as $item): ?>
        <li class="list-group-item">
            <span><?= $item['title'] ?></span>
            <span class="badge"><?= $item['count'] ?></span>
        </li>
    <?php endforeach; ?>
</ul>
```

DEPOIS:
```php
<ul class="widget-list">
    <?php foreach ($items as $index => $item): ?>
        <li class="widget-list-item">
            <div class="widget-list-rank"><?= $index + 1 ?></div>
            <div class="widget-list-content">
                <div class="widget-list-title"><?= View::e($item['title']) ?></div>
            </div>
            <div class="widget-list-meta"><?= $item['count'] ?></div>
        </li>
    <?php endforeach; ?>
</ul>
```

### 5.2 Alert Antigo → Widget Alert

ANTES:
```php
<div class="alert alert-warning">
    <strong>Atenção!</strong> Seu acesso expira em 3 dias.
</div>
```

DEPOIS:
```php
<div class="widget-alert widget-alert-warning">
    <div class="widget-alert-icon">
        <i class="bi bi-exclamation-triangle-fill"></i>
    </div>
    <div class="widget-alert-content">
        <div class="widget-alert-title">Atenção!</div>
        <div class="widget-alert-text">Seu acesso expira em 3 dias.</div>
    </div>
</div>
```

### 5.3 Card Grid Antigo → Card Horizontal

ANTES:
```php
<div class="row">
    <?php foreach ($items as $item): ?>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5><?= $item['title'] ?></h5>
                    <p><?= $item['subtitle'] ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
```

DEPOIS:
```php
<div class="row g-3">
    <?php foreach ($items as $item): ?>
        <div class="col-12 col-md-6">
            <a href="<?= $item['url'] ?>" class="card-horizontal">
                <div class="card-horizontal-icon">
                    <i class="bi bi-<?= $item['icon'] ?>"></i>
                </div>
                <div class="card-horizontal-content">
                    <div class="card-horizontal-title"><?= View::e($item['title']) ?></div>
                    <div class="card-horizontal-subtitle"><?= View::e($item['subtitle']) ?></div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>
```

---

## 6. TROUBLESHOOTING

### 6.1 Sidebar não aparece no desktop
**Problema:** Sidebar some mesmo em tela grande.
**Solução:** Verificar se `.app-sidebar.collapsed` está sendo adicionado. Remover classe no desktop:
```css
@media (min-width: 992px) {
    .app-sidebar.collapsed {
        transform: translateX(0) !important;
    }
}
```

### 6.2 CSS Variables não funcionam
**Problema:** Cores não aplicam, ou aparecem como `var(--color-primary)` no HTML.
**Solução:** 
1. Verificar se navegador suporta (IE11 não suporta)
2. Adicionar fallbacks:
```css
.card-soft {
    background-color: #ffffff;
    background-color: var(--bg-surface);
}
```

### 6.3 Layout quebra no mobile
**Problema:** Elementos sobrepostos ou cortados.
**Solução:**
1. Verificar `margin-left` do `.app-main` (deve ser 0 no mobile)
2. Garantir que sidebar tem `transform: translateX(-100%)` quando não `.show`
3. Verificar `max-width: 100%` em imagens

### 6.4 Theme toggle não persiste
**Problema:** Ao recarregar página, tema volta para light.
**Solução:** Aplicar tema antes do DOMContentLoaded:
```js
// No início do <body> (inline script)
(function() {
    const theme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', theme);
})();
```

### 6.5 Hover states no mobile
**Problema:** Cards ficam "travados" em hover state no touch.
**Solução:** Usar media query hover:
```css
@media (hover: hover) {
    .card-horizontal:hover {
        transform: translateY(-2px);
    }
}
```

### 6.6 Performance lenta ao carregar
**Problema:** Página demora muito para renderizar.
**Solução:**
1. Lazy load de imagens: `<img loading="lazy" ...>`
2. Minificar CSS/JS em produção
3. Usar CDN para Bootstrap e ícones
4. Otimizar queries do banco (LIMIT, índices)

---

## 📝 CHECKLIST DE MIGRAÇÃO POR PÁGINA

### Para cada página:
- [ ] Controller adaptado com variáveis do layout
- [ ] View content separada (sem `<html>`, `<body>`)
- [ ] Sidebar mostrando item ativo correto
- [ ] Topbar com search funcional
- [ ] Cards usando classes novas
- [ ] Widgets usando estrutura nova
- [ ] Responsivo testado (mobile, tablet, desktop)
- [ ] Links funcionando
- [ ] Formulários submetendo
- [ ] Screenshots antes/depois tiradas
- [ ] Aprovação do time

---

**Última atualização:** 2026-02-14
