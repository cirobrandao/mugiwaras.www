# 📋 CHECKLIST DE QA VISUAL & MIGRAÇÃO

## ✅ FASE 1: DESIGN SYSTEM & ESTÉTICA

### 1.1 Paleta de Cores
- [ ] CSS Variables funcionando em todos os navegadores
- [ ] Contraste mínimo 4.5:1 entre texto e fundo (WCAG AA)
- [ ] Cores de status (success, warning, danger) consistentes
- [ ] Dark mode alternando corretamente (se implementado)
- [ ] Gradientes suaves e profissionais (não muito saturados)

### 1.2 Tipografia
- [ ] Font-family carregando corretamente (-apple-system fallbacks)
- [ ] Tamanhos de fonte responsivos (16px mínimo no mobile)
- [ ] Line-height adequado para leitura (1.5-1.7)
- [ ] Font-weight consistente (400, 500, 600, 700)
- [ ] Hierarquia clara entre títulos (h1, h2, h3)

### 1.3 Espaçamentos
- [ ] Padding consistente em cards (16px-24px)
- [ ] Gaps uniformes em grids (16px, 24px, 32px)
- [ ] Margens verticais proporcionais
- [ ] Espaço respirável ao redor de elementos interativos

### 1.4 Bordas & Sombras
- [ ] Border-radius suaves (12px-16px para cards)
- [ ] Sombras leves (não exageradas)
- [ ] Bordas consistentes (1px, cor neutra)
- [ ] Hover states com sombra elevada

---

## ✅ FASE 2: LAYOUT & ESTRUTURA

### 2.1 Sidebar
- [ ] Largura fixa de 260px no desktop
- [ ] Collapse corretamente no mobile (<992px)
- [ ] Logo visível e clicável
- [ ] Navegação com estado "active" funcionando
- [ ] Scroll interno quando conteúdo excede altura
- [ ] Badges de notificação visíveis
- [ ] Footer fixo no bottom (logout)

### 2.2 Topbar
- [ ] Altura fixa de 72px
- [ ] Sticky ao fazer scroll
- [ ] Search expansível e funcional
- [ ] Ícones de ação (notificações, tema) funcionando
- [ ] Avatar com nome e role visíveis (desktop)
- [ ] Toggle sidebar visível apenas no mobile

### 2.3 Content Area
- [ ] Largura máxima de 1400px
- [ ] Padding adequado (32px desktop, 16px mobile)
- [ ] Grid 2 colunas (8/4) funcionando
- [ ] Coluna direita (widgets) empilha embaixo no mobile

### 2.4 Cards
- [ ] Sombra suave e profissional
- [ ] Hover state com lift (+2px translateY)
- [ ] Espaçamento interno consistente
- [ ] Headers com divider quando necessário
- [ ] Cards horizontais com ícone + texto

---

## ✅ FASE 3: COMPONENTES & WIDGETS

### 3.1 Widget Avisos
- [ ] Cores de fundo por tipo (success, warning, danger, info)
- [ ] Ícones adequados para cada status
- [ ] Texto legível sobre fundo colorido
- [ ] Border de 1px combinando com fundo
- [ ] Padding suficiente (12px-16px)

### 3.2 Widget Top 10
- [ ] Badges numerados com gradiente azul
- [ ] Ícone de visualizações (olho) visível
- [ ] Título truncado com ellipsis se necessário
- [ ] Badge de categoria com cor correta
- [ ] Hover state sutil

### 3.3 Widget Últimos Lançamentos
- [ ] Ícone de arquivo/estrela visível
- [ ] Timestamp relativo (Há X horas)
- [ ] Link funcional para série
- [ ] Espaçamento entre itens (8px-12px)

### 3.4 Cards Favoritos
- [ ] Ícones com gradiente colorido
- [ ] Título + subtítulo bem separados
- [ ] Badge "Novo" quando aplicável
- [ ] Grid responsivo (2 cols desktop, 1 col mobile)
- [ ] Hover com elevação suave

### 3.5 Cards Notícias
- [ ] Imagem com proporção 16:9 (opcional)
- [ ] Pills de categoria coloridas
- [ ] Timestamp relativo
- [ ] Excerpt limitado (150 chars)
- [ ] Botão "Ler mais" com outline-primary

---

## ✅ FASE 4: RESPONSIVIDADE

### 4.1 Breakpoints
- [ ] **Mobile (<576px)**: Single column, sidebar oculta
- [ ] **Tablet (576-991px)**: Sidebar oculta, toggle visível
- [ ] **Desktop (992px+)**: Sidebar fixa, layout 2 colunas
- [ ] **Large (1200px+)**: Conteúdo centralizado com max-width

### 4.2 Sidebar Mobile
- [ ] Overlay escuro ao abrir (opcional)
- [ ] Slide-in animation suave
- [ ] Fecha ao clicar fora
- [ ] Fecha ao redimensionar para desktop
- [ ] Botão toggle visível no topbar

### 4.3 Grid Mobile
- [ ] Cards empilham verticalmente
- [ ] Imagens em largura total
- [ ] Texto legível sem zoom
- [ ] Scroll vertical suave

### 4.4 Touch Targets
- [ ] Botões com mínimo 44x44px (Apple HIG)
- [ ] Espaçamento entre links (8px mínimo)
- [ ] Áreas clicáveis extendem além do texto
- [ ] Hover states não aparecem no touch

---

## ✅ FASE 5: INTERATIVIDADE & JAVASCRIPT

### 5.1 Sidebar Toggle
- [ ] Ícone "hamburger" funcional
- [ ] Classe `show` adicionada/removida corretamente
- [ ] Prevenção de scroll body quando aberto (opcional)
- [ ] ESC key fecha sidebar (nice-to-have)

### 5.2 Theme Toggle
- [ ] Ícone alterna (moon ↔ sun)
- [ ] Tema salvo no localStorage
- [ ] Tema aplicado antes do render (evita flash)
- [ ] Todas as cores alternando corretamente

### 5.3 Search
- [ ] Enter submete busca
- [ ] Query escapada corretamente na URL
- [ ] Focus state visível
- [ ] Clear button (opcional)

### 5.4 Animações
- [ ] Cards fade-in ao carregar (opcional)
- [ ] Transições suaves (150-300ms)
- [ ] Sem flicker ou jumps
- [ ] Performance 60fps

---

## ✅ FASE 6: ACESSIBILIDADE (WCAG 2.1 AA)

### 6.1 Contraste
- [ ] Texto normal: 4.5:1 mínimo
- [ ] Texto grande (18px+): 3:1 mínimo
- [ ] Ícones e gráficos: 3:1 mínimo
- [ ] Links distinguíveis (cor + underline no hover)

### 6.2 Navegação por Teclado
- [ ] Tab order lógico
- [ ] Focus visible em todos os elementos interativos
- [ ] Skip to content link (opcional)
- [ ] Sidebar navegável com Tab

### 6.3 Screen Readers
- [ ] `aria-label` em botões com apenas ícones
- [ ] `alt` text em todas as imagens
- [ ] Landmarks semânticos (`<nav>`, `<main>`, `<aside>`)
- [ ] Estado "active" anunciado na navegação

### 6.4 Formulários
- [ ] Labels associados a inputs
- [ ] Placeholders não substituem labels
- [ ] Erros de validação visíveis e descritivos
- [ ] Autocomplete adequado

---

## ✅ FASE 7: PERFORMANCE

### 7.1 CSS
- [ ] Minificado em produção
- [ ] Sem !important desnecessários
- [ ] Seletores simples (evitar profundidade >3)
- [ ] CSS crítico inline (opcional)

### 7.2 JavaScript
- [ ] Minificado em produção
- [ ] Event listeners delegados quando possível
- [ ] Debounce em scroll/resize handlers
- [ ] Lazy loading de imagens

### 7.3 Imagens
- [ ] Formatos modernos (WebP) com fallback
- [ ] Dimensões adequadas (não gigantes)
- [ ] Lazy loading com Intersection Observer
- [ ] Placeholders enquanto carrega

### 7.4 Fontes
- [ ] System fonts por padrão (performance)
- [ ] font-display: swap (se usar web fonts)
- [ ] Subset de caracteres se possível

---

## ✅ FASE 8: CROSS-BROWSER

### 8.1 Navegadores Suportados
- [ ] Chrome/Edge (últimas 2 versões)
- [ ] Firefox (últimas 2 versões)
- [ ] Safari (últimas 2 versões)
- [ ] Mobile Safari (iOS 14+)
- [ ] Chrome Mobile (Android 10+)

### 8.2 Fallbacks
- [ ] CSS Grid com flexbox fallback
- [ ] CSS Variables com fallback em cores fixas (opcional)
- [ ] Intersection Observer com polyfill (se necessário)

---

## ✅ FASE 9: MIGRAÇÃO DO SISTEMA EXISTENTE

### 9.1 Preparação
- [ ] Backup completo do sistema atual
- [ ] Branch Git separado para novo tema
- [ ] Checklist de todas as páginas a migrar

### 9.2 Arquivos a Criar
- [ ] `/public/assets/css/theme.css`
- [ ] `/public/assets/js/theme.js`
- [ ] `/app/Views/partials/header.php`
- [ ] `/app/Views/partials/sidebar.php`
- [ ] `/app/Views/partials/topbar.php`
- [ ] `/app/Views/partials/footer.php`
- [ ] `/app/Views/layout-new.php` (base template)

### 9.3 Páginas a Migrar (ordem sugerida)
1. [ ] Dashboard (`/dashboard`)
2. [ ] Biblioteca - Categorias (`/libraries`)
3. [ ] Biblioteca - Séries (`/libraries/{category}/{series}`)
4. [ ] Perfil (`/profile`)
5. [ ] Notícias (`/news`, `/news/{id}`)
6. [ ] Favoritos (`/favorites`)
7. [ ] Suporte (`/support`)
8. [ ] Loja (`/loja`)
9. [ ] Admin (`/admin/*`)

### 9.4 Por Página, Verificar:
- [ ] Layout usando `layout-new.php`
- [ ] Variáveis passadas corretamente ($pageTitle, $currentPath, etc)
- [ ] Queries do banco funcionando
- [ ] Links internos apontando corretamente
- [ ] Formulários funcionais
- [ ] Uploads de arquivos (se aplicável)
- [ ] Autenticação/autorização funcionando

### 9.5 Comparação Antes/Depois
- [ ] Screenshot de cada página (antes)
- [ ] Screenshot de cada página (depois)
- [ ] Lista de mudanças visuais/funcionais
- [ ] Aprovação do time/cliente

---

## ✅ FASE 10: TESTES FINAIS

### 10.1 Funcionalidade
- [ ] Login/Logout funcionando
- [ ] Navegação entre páginas
- [ ] Busca global funcional
- [ ] Favoritos adicionando/removendo
- [ ] Upload de conteúdo (admin)
- [ ] Pagamentos processando (se aplicável)

### 10.2 Visual
- [ ] Alinhamento perfeito em todas as breakpoints
- [ ] Cores consistentes
- [ ] Tipografia legível
- [ ] Sem elementos cortados ou sobrepostos

### 10.3 Performance
- [ ] Lighthouse Score >90 (Performance)
- [ ] First Contentful Paint <1.5s
- [ ] Largest Contentful Paint <2.5s
- [ ] Time to Interactive <3.5s

### 10.4 SEO (se aplicável)
- [ ] Meta tags corretas
- [ ] Heading hierarchy (h1, h2, h3)
- [ ] URLs descritivas
- [ ] Sitemap atualizado

---

## 🚀 DEPLOY

### Pré-produção
- [ ] Testar em ambiente staging
- [ ] Validar todas as funcionalidades
- [ ] Teste de carga (se aplicável)
- [ ] Rollback plan documentado

### Produção
- [ ] Deploy em horário de baixo tráfego
- [ ] Monitorar erros (logs, Sentry, etc)
- [ ] Cache invalidado (se usar CDN)
- [ ] Comunicar usuários sobre mudanças (opcional)

---

## 📊 MÉTRICAS DE SUCESSO

- [ ] Bounce rate mantido ou reduzido
- [ ] Tempo na página aumentado
- [ ] Conversões mantidas/aumentadas
- [ ] Feedback positivo dos usuários
- [ ] Redução de tickets de suporte relacionados a UI

---

**Última atualização:** 2026-02-14
**Responsável:** Tech Lead Frontend/UX
