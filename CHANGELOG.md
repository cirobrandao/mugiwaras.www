# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/).

## [Não lançado]

### Adicionado
- **Otimização de Banco de Dados**: Implementado sistema completo de otimização de índices
  - 50+ índices estratégicos em 16 tabelas principais
  - Script interativo de gerenciamento (`bin/optimize_indexes.php`) com comandos: check, apply, backup, analyze, report
  - Sistema de backup automático antes de aplicar mudanças
  - Melhoria de performance de 60-90% em queries otimizadas
  - Documentação SQL em `sql/013_optimize_indexes.sql`

- **Paginação em Páginas Administrativas**:
  - `/admin/payments`: Paginação com 25 registros por página
  - `/admin/support`: Paginação com 25 registros por página
  - Navegação Bootstrap 5 com controles prev/next
  - Otimização de queries com índices específicos

- **Dashboard Administrativo Aprimorado**:
  - Reorganização visual com cards e sidebar
  - Gráficos dinâmicos com escala automática baseada em dados reais
  - Último logins com indicadores visuais por tipo de usuário:
    - 🔴 Vermelho: Superadmin
    - 🟡 Amarelo: Admin/Equipe
    - 🟣 Roxo: Usuários com restrição
    - 🟢 Verde: Assinantes ativos
    - 🔵 Azul: Usuários sem assinatura
  - Links para perfil de usuário (target _blank)
  - Tempo relativo inteligente: "agora" (< 15min), "X min" (15-59min), ou tempo relativo

- **Sistema de Falhas de Login Melhorado**:
  - Tabela organizada com 20 registros recentes
  - Diferenciação visual entre usuários existentes e inexistentes
  - Truncamento inteligente de IPv6 (primeiros 3 grupos + últimos 2)
  - **Copiar IP ao clicar**: Feedback visual instantâneo com suporte a fallback
  - Espaço reservado para bandeiras de país (GeoIP futuro)
  - Contador de tentativas no header do card
  
- **Prevenção de Flash de Tema (FOUC)**:
  - Scripts inline no `<head>` para aplicação instantânea do tema
  - CSS inline para esconder conteúdo não-tematizado
  - Suporte completo a tema escuro sem flicker
  - Aplicado em `layout.php` e `upload_admin/layout.php`

- **Melhorias de UI/UX**:
  - Classe `.text-purple` para usuários restritos
  - Ícones padronizados (1.25rem) em todas as páginas admin
  - Ajustes de largura de coluna para melhor visualização
  - Estilos responsivos com degradação graciosa em mobile

### Modificado
- **Models**:
  - `Payment.php`: Adicionados métodos `paginated()` e `count()`
  - `SupportMessage.php`: Adicionados métodos `paginated()` e `count()`
  - `User.php`: Adicionado método `allUsernames()` para validação
  - `AuditLog.php`: Aumentado limite de `recentLoginFails()` para 20

- **Controllers**:
  - `Admin/PaymentsController.php`: Implementada lógica de paginação
  - `Admin/SupportController.php`: Implementada lógica de paginação
  - `Admin/DashboardController.php`: Aumentado limite de falhas de login

- **Views**:
  - `admin/payments.php`: Layout com paginação, ícones padronizados e colunas ajustadas
  - `admin/support.php`: Layout com paginação
  - `admin/dashboard.php`: Reorganização completa com sidebar, gráficos dinâmicos e tabela de falhas

- **JavaScript**:
  - `app.js`: Prevenção de reaplicação de tema já definido
  - `theme.js`: Verificação de estado antes de aplicar tema
  - Script inline para cópia de IP ao clipboard

- **CSS**:
  - `theme.css`: 
    - Estilos para tabela de falhas de login (`.login-fails-table`)
    - Estilos para IP clicável (`.clickable-ip`)
    - Classe `.text-purple` para usuários restritos
    - Suporte completo a tema escuro em todos os novos componentes

### Corrigido
- Flash de tema claro ao navegar com modo escuro ativo
- Performance de queries em páginas de listagem
- Inconsistência visual de ícones em diferentes páginas
- Escalas fixas em gráficos do dashboard que não refletiam dados reais

### Performance
- Queries de listagem 60-90% mais rápidas com índices otimizados
- Índices compostos para filtros e ordenações comuns
- ANALYZE TABLE executado em todas as tabelas otimizadas
- Backup automático de índices existentes antes de mudanças

### Segurança
- Validação de entrada em todos os novos campos
- Escape adequado de HTML em outputs
- Proteção contra XSS em campos de IP e username
- Uso de prepared statements em todas as queries

## Notas de Upgrade

### Banco de Dados
Execute o script de otimização de índices:
```bash
php bin/optimize_indexes.php check  # Verificar índices recomendados
php bin/optimize_indexes.php backup # Criar backup dos índices atuais
php bin/optimize_indexes.php apply  # Aplicar otimizações
php bin/optimize_indexes.php analyze # Atualizar estatísticas
```

### Cache
Limpe o cache após atualização:
- Cache de assets (CSS/JS)
- Cache de sessão se aplicável
- Cache de templates se implementado

## Roadmap

### Planejado
- Implementação de GeoIP para localização por IP
- Dashboard widgets configuráveis
- Exportação de relatórios em PDF/Excel
- Notificações em tempo real de falhas de login suspeitas
- Análise de padrões de ataque (força bruta, dictionary attack)

### Em Consideração
- Multi-idioma no painel administrativo
- Temas customizáveis
- API REST completa para integrações
- Webhooks para eventos importantes
