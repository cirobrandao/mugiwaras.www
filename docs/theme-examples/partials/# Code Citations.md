# Code Citations

## License: MIT
https://github.com/mi-classroom/mi-web-technologien-beiboot-ss2023-finnge/blob/10e2f030c7b3751252524d0a954bb58295858109/src/styles/variables.scss

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem
```


## License: MIT
https://github.com/mi-classroom/mi-web-technologien-beiboot-ss2023-finnge/blob/10e2f030c7b3751252524d0a954bb58295858109/src/styles/variables.scss

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem
```


## License: MIT
https://github.com/mi-classroom/mi-web-technologien-beiboot-ss2023-finnge/blob/10e2f030c7b3751252524d0a954bb58295858109/src/styles/variables.scss

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem
```


## License: MIT
https://github.com/mi-classroom/mi-web-technologien-beiboot-ss2023-finnge/blob/10e2f030c7b3751252524d0a954bb58295858109/src/styles/variables.scss

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem
```


## License: MIT
https://github.com/mi-classroom/mi-web-technologien-beiboot-ss2023-finnge/blob/10e2f030c7b3751252524d0a954bb58295858109/src/styles/variables.scss

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem
```


## License: MIT
https://github.com/mi-classroom/mi-web-technologien-beiboot-ss2023-finnge/blob/10e2f030c7b3751252524d0a954bb58295858109/src/styles/variables.scss

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem
```


## License: MIT
https://github.com/mi-classroom/mi-web-technologien-beiboot-ss2023-finnge/blob/10e2f030c7b3751252524d0a954bb58295858109/src/styles/variables.scss

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem
```


## License: MIT
https://github.com/mi-classroom/mi-web-technologien-beiboot-ss2023-finnge/blob/10e2f030c7b3751252524d0a954bb58295858109/src/styles/variables.scss

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem
```


## License: MIT
https://github.com/mi-classroom/mi-web-technologien-beiboot-ss2023-finnge/blob/10e2f030c7b3751252524d0a954bb58295858109/src/styles/variables.scss

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem
```


## License: MIT
https://github.com/mi-classroom/mi-web-technologien-beiboot-ss2023-finnge/blob/10e2f030c7b3751252524d0a954bb58295858109/src/styles/variables.scss

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem
```


## License: MIT
https://github.com/mi-classroom/mi-web-technologien-beiboot-ss2023-finnge/blob/10e2f030c7b3751252524d0a954bb58295858109/src/styles/variables.scss

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem
```


## License: MIT
https://github.com/mi-classroom/mi-web-technologien-beiboot-ss2023-finnge/blob/10e2f030c7b3751252524d0a954bb58295858109/src/styles/variables.scss

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem
```


## License: MIT
https://github.com/mi-classroom/mi-web-technologien-beiboot-ss2023-finnge/blob/10e2f030c7b3751252524d0a954bb58295858109/src/styles/variables.scss

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/eddimas/eddimas.github.io/blob/dc9643d8f21971f80a822f09b22de966b5c03b7a/src/styles/vars.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
```


## License: unknown
https://github.com/eddimas/eddimas.github.io/blob/dc9643d8f21971f80a822f09b22de966b5c03b7a/src/styles/vars.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     
```


## License: unknown
https://github.com/eddimas/eddimas.github.io/blob/dc9643d8f21971f80a822f09b22de966b5c03b7a/src/styles/vars.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     
```


## License: unknown
https://github.com/eddimas/eddimas.github.io/blob/dc9643d8f21971f80a822f09b22de966b5c03b7a/src/styles/vars.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --
```


## License: unknown
https://github.com/eddimas/eddimas.github.io/blob/dc9643d8f21971f80a822f09b22de966b5c03b7a/src/styles/vars.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.
```


## License: unknown
https://github.com/eddimas/eddimas.github.io/blob/dc9643d8f21971f80a822f09b22de966b5c03b7a/src/styles/vars.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.
```


## License: unknown
https://github.com/eddimas/eddimas.github.io/blob/dc9643d8f21971f80a822f09b22de966b5c03b7a/src/styles/vars.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: unknown
https://github.com/eddimas/eddimas.github.io/blob/dc9643d8f21971f80a822f09b22de966b5c03b7a/src/styles/vars.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: unknown
https://github.com/eddimas/eddimas.github.io/blob/dc9643d8f21971f80a822f09b22de966b5c03b7a/src/styles/vars.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
```


## License: unknown
https://github.com/eddimas/eddimas.github.io/blob/dc9643d8f21971f80a822f09b22de966b5c03b7a/src/styles/vars.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
  --font-weight-normal: 400;
  --font
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
  --font-weight-normal: 400;
  --font
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
  --font-weight-normal: 400;
  --font
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
  --font-weight-normal: 400;
  --font
```


## License: unknown
https://github.com/eddimas/eddimas.github.io/blob/dc9643d8f21971f80a822f09b22de966b5c03b7a/src/styles/vars.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
  --font-weight-normal: 400;
  --font
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
  --font-weight-normal: 400;
  --font
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
  --font-weight-normal: 400;
  --font
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
  --font-weight-normal: 400;
  --font
```


## License: unknown
https://github.com/eddimas/eddimas.github.io/blob/dc9643d8f21971f80a822f09b22de966b5c03b7a/src/styles/vars.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
  --font-weight-normal: 400;
  --font
```


## License: MIT
https://github.com/mauricekleine/mozza/blob/1917302df8bc1ebd0c6eb6c3be3e46d7143b7b29/apps/mauricekleine.com/src/app/layout.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
  --font-weight-normal: 400;
  --font
```


## License: unknown
https://github.com/code-shaper/code-shaper/blob/8e2f5b6fa9040233c72daab23454bf0fbabdfd4e/plugins/react/src/reactLibraryGenerator/templates/main/src/styles/main.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
  --font-weight-normal: 400;
  --font
```


## License: unknown
https://github.com/sadam-hussien/tomo-dashboard/blob/d85acfa853df5f51ed5ba892afc934383e2aca6b/src/styles/variables.css

```
# Plano de Reformulação do Tema - Dashboard Moderna

## 📋 ETAPAS DO PROJETO

### **Fase 1: Preparação e Design System** (1-2h)
1. Criar estrutura de arquivos CSS/JS
2. Definir CSS Variables (paleta, tipografia, espaçamentos)
3. Criar base do theme.css com reset e utilitários
4. Configurar Bootstrap Icons via CDN

### **Fase 2: Estrutura de Layout** (2-3h)
5. Criar layout.php base com sidebar + main
6. Desenvolver partials: header.php, sidebar.php, topbar.php, footer.php
7. Implementar sidebar clara com navegação em pills
8. Topbar com search grande e avatar

### **Fase 3: Componentes e Widgets** (3-4h)
9. Estilizar cards soft (sombra suave, radius 16px)
10. Criar widgets: Avisos, Top 10, Últimos Lançamentos
11. Seção "Meus Favoritos" (cards horizontais)
12. Seção "Blog" (lista com thumbnails)

### **Fase 4: Responsividade** (2h)
13. Adaptar sidebar (collapse no mobile)
14. Ajustar grid de cards (1/2/3 colunas conforme breakpoint)
15. Testar touch targets e navegação mobile

### **Fase 5: Polish e QA** (1-2h)
16. Estados hover/focus/active
17. Dark mode (opcional)
18. Checklist de acessibilidade e contraste
19. Testes cross-browser

---

## 📁 ESTRUTURA DE ARQUIVOS PROPOSTA

```
/public
  /assets
    /css
      theme.css           ← CSS principal com variables
      theme.min.css       ← Versão minificada (produção)
    /js
      theme.js            ← Sidebar toggle, search, interactions
    /images
      /avatars
      /placeholders

/app
  /Views
    layout.php            ← Template base com sidebar + topbar
    /partials
      header.php          ← <head> com meta, CSS, fonts
      sidebar.php         ← Navegação lateral clara
      topbar.php          ← Barra superior com search e avatar
      footer.php          ← Scripts e rodapé
    /dashboard
      index.php           ← Dashboard principal
    /profile
    /libraries
    ...

/config
  theme.php              ← Configurações do tema (opcional)
```

---

## 🎨 DESIGN SYSTEM

### **CSS Variables - Paleta**

```css
:root {
  /* Background & Surfaces */
  --bg-page: #f5f7fb;
  --bg-surface: #ffffff;
  --bg-sidebar: #fefefe;
  --bg-hover: #f8f9fb;
  
  /* Borders */
  --border-color: #e5e8ef;
  --border-hover: #d1d5dd;
  --border-active: #3b82f6;
  
  /* Primary & Brand */
  --color-primary: #3b82f6;
  --color-primary-dark: #2563eb;
  --color-primary-light: #60a5fa;
  
  /* Status Colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #ef4444;
  --color-info: #06b6d4;
  
  /* Text */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --text-inverse: #ffffff;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  
  /* Border Radius */
  --radius-sm: 8px;
  --radius-base: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-pill: 9999px;
  
  /* Spacing */
  --spacing-xs: 0.5rem;   /* 8px */
  --spacing-sm: 0.75rem;  /* 12px */
  --spacing-md: 1rem;     /* 16px */
  --spacing-lg: 1.5rem;   /* 24px */
  --spacing-xl: 2rem;     /* 32px */
  --spacing-2xl: 3rem;    /* 48px */
  
  /* Typography */
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-size-xs: 0.75rem;     /* 12px */
  --font-size-sm: 0.875rem;    /* 14px */
  --font-size-base: 1rem;      /* 16px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-xl: 1.25rem;     /* 20px */
  --font-size-2xl: 1.5rem;     /* 24px */
  --font-size-3xl: 1.875rem;   /* 30px */
  
  --font-weight-normal: 400;
  --font
```

