# Dashboard Administrativo

Painel principal em `/admin` com estatísticas, gráficos e monitoramento em tempo real.

## Visão Geral

O dashboard fornece:
- Estatísticas de usuários, conteúdo e pagamentos
- Gráficos de crescimento e receita
- Monitoramento de atividade recente
- Detecção de falhas de login

## Seções Principais

### 1. Cards de Estatísticas (Topo)

Exibe métricas principais:
- Total de usuários cadastrados
- Total de conteúdo publicado
- Pagamentos mensais (mês atual)
- Usuários assinantes ativos

**Atualização**: Recarregue a página para dados atualizados.

### 2. Gráficos

**Crescimento de Usuários** (30 dias):
- Gráfico de linha mostrando registros diários
- Eixo X: Dias do mês
- Eixo Y: Número de novos usuários

**Receita Mensal** (12 meses):
- Gráfico de barras com receita por mês
- Valores em R$ (Real Brasileiro)
- Comparação ano a ano

### 3. Últimos Logins (15 registros)

Exibe atividade de login recente com:

**Indicador Visual por Tipo de Usuário**:
- 🔴 **Vermelho**: Superadmin
- 🟡 **Amarelo**: Admin ou Equipe
- 🟣 **Roxo**: Restrito
- 🟢 **Verde**: Assinante
- 🔵 **Azul**: Usuário padrão

**Informações Exibidas**:
- Username (link para perfil em nova aba)
- Timestamp inteligente:
  - `agora` - menos de 15 minutos
  - `X min` - entre 15-59 minutos
  - `X horas atrás` - 1-23 horas
  - `X dias atrás` - 24+ horas
- Endereço IP (clique para copiar)
- User-Agent (navegador/SO)

**IP com Truncamento IPv6**:
- IPv4: Exibido completo
- IPv6 > 25 caracteres: `2001:db8:cafe:...:dead:beef` (primeiros 3 grupos + últimos 2)

### 4. Falhas de Login (20 registros)

Tabela organizada com tentativas de login malsucedidas:

**Colunas**:
- **Status**: Ícone indicando se usuário existe
  - ⚠️ **Amarelo**: Username existe no banco
  - ⛔ **Vermelho**: Username não encontrado
- **Username**: Nome de usuário da tentativa
- **IP**: Endereço de origem (clique para copiar)
- **User-Agent**: Dispositivo/navegador usado
- **Quando**: Timestamp relativo

**Funcionalidades**:
- **Click-to-Copy IP**: Clique no endereço IP para copiar
  - ✅ Verde por 2s: Copiado com sucesso
  - ❌ Vermelho por 2s: Erro ao copiar
- **Detecção de Usuário**: Diferencia tentativas com usernames válidos vs. inválidos
- **IPv6 Truncado**: Endereços longos são abreviados automaticamente

**Design Responsivo**:
- Desktop: Tabela completa com 5 colunas
- Mobile: Layout adaptado com informações essenciais

**Estilo Aprimorado**:
- Cabeçalho com gradiente
- Hover effects nas linhas
- Alternância de cores para melhor legibilidade
- Suporte a tema escuro

## Prevenção de Flash de Conteúdo (FOUC)

O sistema previne o "piscar" entre tema claro e escuro:

**Implementação**:
1. **Script inline no `<head>`**: Lê `localStorage` e aplica tema antes do render
2. **CSS inline**: Esconde conteúdo não estilizado temporariamente
3. **Script inline após `<body>`**: Aplica classe `theme-dark` e remove loading

**Arquivos Afetados**:
- `app/Views/layout.php`
- `app/Views/upload_admin/layout.php`

**JavaScript Otimizado**:
- `public/assets/js/app.js`: Evita reaplicação de tema já definido
- `public/assets/js/theme.js`: Toggle com verificação de estado

## Segurança

- Acesso restrito a administradores
- Queries auditadas em `audit_log`
- IPs exibidos para investigação de fraudes
- Proteção CSRF em todas ações

## Performance

Com otimizações de índices (013_optimize_indexes.sql):
- Últimos logins: 60-90% mais rápido via `idx_created_at`
- Falhas de login: Query otimizada com `idx_event + idx_created_at`
- Paginação eficiente: 15-25 registros por query

## Personalização

**Ajustar Limites**:
```php
// app/Controllers/Admin/DashboardController.php

$recentLogins = User::recentLogins(15);      // Padrão: 15
$loginFailAttempts = AuditLog::recentLoginFails(20); // Padrão: 20
```

**Estilização**:
```css
/* public/assets/css/theme.css */

.text-purple { color: #8b5cf6; }            /* Usuários restritos */
.login-fails-table { ... }                  /* Tabela de falhas */
.clickable-ip { cursor: pointer; }          /* IPs clicáveis */
```

## Manutenção

**Limpeza de Logs Antigos**:
```sql
-- Remover falhas de login com mais de 90 dias
DELETE FROM audit_log 
WHERE event = 'login_fail' 
AND created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

**Análise de Tentativas**:
```sql
-- Top 10 IPs com mais falhas
SELECT ip, COUNT(*) as attempts 
FROM audit_log 
WHERE event = 'login_fail' 
GROUP BY ip 
ORDER BY attempts DESC 
LIMIT 10;
```

## Compatibilidade

- PHP 8.3+
- Bootstrap 5.3
- Navegadores modernos (Chrome 90+, Firefox 88+, Safari 14+)
- Suporte a IPv4 e IPv6
- Clipboard API com fallback para `document.execCommand`
