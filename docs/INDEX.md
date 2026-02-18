# Índice de Documentação

Guia completo da documentação do projeto MWS (Mugiwaras Web System).

## 📚 Documentação Principal

### [README.md](../README.md)
Visão geral do projeto, requisitos, instalação e configuração básica.

**Conteúdo principal**:
- Requisitos do sistema (PHP 8.3, MySQL, Nginx)
- Instalação via Composer
- Configuração de ambiente (.env)
- Comandos principais (worker, doctor, optimize_indexes)
- Estrutura de diretórios
- Deploy em Nginx

### [CHANGELOG.md](CHANGELOG.md)
Histórico completo de mudanças, melhorias e correções.

**Últimas atualizações**:
- Otimização de banco de dados (50+ índices)
- Sistema de paginação (25 registros por página)
- Melhorias no dashboard administrativo
- Prevenção de FOUC (Flash of Unstyled Content)
- Reorganização da tabela de falhas de login

## 🏗️ Arquitetura e Estrutura

### [ARCHITECTURE.md](ARCHITECTURE.md)
Arquitetura geral do sistema, configuração de servidor e componentes.

**Tópicos abordados**:
- MVC leve com Router simples
- Configuração Nginx (location, base path)
- Sistema de conversores (PDF/CBR/Imagens)
- Storage local e interfaces
- Paginação padronizada
- Sistema de temas com prevenção de FOUC
- Permissões e segurança básica

### [DB.md](DB.md)
Documentação completa do banco de dados.

**Conteúdo**:
- Schema e tabelas (22 tabelas principais)
- Otimização de índices (50+ índices implementados)
- Script de gerenciamento (bin/optimize_indexes.php)
- Migrações (001-013)
- Manutenção e performance
- Queries SQL úteis para análise

### [INDEX_OPTIMIZATION_README.md](../sql/INDEX_OPTIMIZATION_README.md)
Guia detalhado sobre otimizações de índices do banco de dados.

**Conteúdo técnico**:
- Detalhamento de cada índice criado
- Queries específicas otimizadas
- Como verificar melhorias
- Monitoramento de performance
- Troubleshooting

## 🔒 Segurança

### [SECURITY.md](SECURITY.md)
Políticas e implementações de segurança.

**Medidas implementadas**:
- CSRF protection em todos os POST
- Sessões seguras (HttpOnly, Secure, SameSite)
- Rate limiting (login, suporte, API)
- Lockout após tentativas falhas
- Auditoria de login (audit_log)
- Cross-domain authentication
- Análise de ameaças (IPs suspeitos, usernames atacados)
- Queries SQL para análise de segurança

## 🖥️ Interface e Funcionalidades

### [DASHBOARD.md](DASHBOARD.md)
Documentação do painel administrativo.

**Funcionalidades**:
- Cards de estatísticas (usuários, conteúdo, pagamentos)
- Gráficos (crescimento de usuários, receita mensal)
- Últimos logins (15 registros com cores por tipo)
- Falhas de login (20 registros com detecção de usuário)
- IPv6 truncado e click-to-copy
- Prevenção de FOUC
- Personalização e manutenção

### [API.md](API.md)
Referência completa de todas as rotas da aplicação.

**Grupos de rotas**:
- Autenticação (login, registro, reset)
- Suporte (tickets, rastreamento)
- Dashboard (usuário e admin)
- Avatar Gallery
- Notícias e Notificações
- Loja e Pagamentos (usuário e admin)
- Vouchers
- Biblioteca (navegação, gerenciamento)
- Leitor (CBZ, PDF, EPUB)
- Upload (usuário e admin)
- Usuários (admin)
- Categorias (admin)
- Conectores/Scrapers (admin)
- Segurança (blocklists)
- Configurações (admin)
- Logs (admin)

## 🔌 Integrações e Recursos Avançados

### [CONNECTORS.md](CONNECTORS.md)
Sistema de conectores para scrapers (HakuNeko, etc).

**Funcionalidades**:
- Geração automática de conectores .mjs
- Detecção de tema WordPress (Madara, MangaStream)
- Download individual ou em lote (.zip)
- Templates suportados
- Configuração customizada

### [CROSS_DOMAIN_UPLOAD.md](CROSS_DOMAIN_UPLOAD.md)
Sistema de upload em domínio separado (bypass Cloudflare).

**Solução implementada**:
- Token de transição (30 segundos, uso único)
- Validação IP e User-Agent
- Configuração com subdomínios ou domínios diferentes
- Cookie compartilhado entre subdomínios
- Configuração de DNS e Cloudflare

## 🎨 Temas e Estilos

### [CSS_REORGANIZATION.md](CSS_REORGANIZATION.md)
Reorganização do sistema de CSS.

**Escopo**: Detalhes sobre a reestruturação de estilos.

### [THEME_MIGRATION_COMPLETE.md](THEME_MIGRATION_COMPLETE.md)
Migração completa do sistema de temas.

**Escopo**: Histórico da implementação dark mode.

## 📝 Exemplos e Snippets

### [examples/](examples/)
Exemplos de código e uso de funcionalidades.

### [theme-examples/](theme-examples/)
Exemplos de implementação de temas.

### [ai/](ai/)
Documentação gerada por IA ou para uso com IA.

## 🛠️ Scripts e Ferramentas

### Principais Comandos

```bash
# Instalação e inicialização
composer install                     # Instalar dependências
php bin/db_init.php                  # Inicializar banco de dados
php bin/doctor.php                   # Verificar ambiente

# Worker e processamento
php bin/worker.php                   # Processar jobs (conversões)

# Otimização de banco
php bin/optimize_indexes.php check   # Verificar índices
php bin/optimize_indexes.php apply   # Aplicar otimizações
php bin/optimize_indexes.php analyze # Atualizar estatísticas
php bin/optimize_indexes.php report  # Relatório completo

# Conversão CBZ para PDF
php bin/cbz_to_pdf.php               # Converter CBZ para PDF
php bin/cbz_to_pdf.php --series=123  # Converter série específica
php bin/cbz_to_pdf.php --dry-run     # Simular sem executar

# Importação de usuários
php bin/import_users.php             # Importar usuários via CLI
```

## 📊 Métricas e Resultados

### Performance
- **Queries otimizadas**: 60-90% mais rápidas
- **Paginação**: 25 registros por página
- **Índices**: 50+ índices estratégicos
- **Cache**: Theme cache via localStorage

### Segurança
- **Auditoria**: Todos eventos de login registrados
- **Rate limiting**: Login, suporte e API
- **CSRF**: Todas rotas POST protegidas
- **Sessões**: HttpOnly, Secure, SameSite

### UI/UX
- **FOUC**: Prevenção completa de flash de tema
- **Responsividade**: Bootstrap 5.3
- **Cores por função**: 5 cores de indicadores de usuário
- **IPv6**: Truncamento automático (>25 chars)

## 🔄 Fluxo de Trabalho

### Para Desenvolvedores
1. Ler [README.md](../README.md) - Setup inicial
2. Ler [ARCHITECTURE.md](ARCHITECTURE.md) - Entender estrutura
3. Ler [DB.md](DB.md) - Schema e otimizações
4. Consultar [API.md](API.md) - Rotas disponíveis
5. Verificar [SECURITY.md](SECURITY.md) - Políticas de segurança

### Para Administradores
1. Ler [README.md](../README.md) - Instalação
2. Executar `php bin/doctor.php` - Verificar ambiente
3. Aplicar `php bin/optimize_indexes.php apply` - Otimizar banco
4. Ler [DASHBOARD.md](DASHBOARD.md) - Usar painel admin
5. Configurar [CROSS_DOMAIN_UPLOAD.md](CROSS_DOMAIN_UPLOAD.md) se necessário

### Para Usuários Upload
1. Ler [CONNECTORS.md](CONNECTORS.md) - Gerar conectores
2. Usar `/upload` - Enviar conteúdo
3. Usar `/upload-admin` - Upload direto (bypass)

## 📞 Suporte e Manutenção

### Limpeza de Logs
```sql
-- Remover logs antigos (>90 dias)
DELETE FROM audit_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### Monitoramento
```sql
-- IPs com mais falhas de login
SELECT ip, COUNT(*) FROM audit_log 
WHERE event = 'login_fail' 
GROUP BY ip ORDER BY COUNT(*) DESC LIMIT 10;
```

### Backup
- Índices: `storage/backups/indexes_backup_*.sql`
- Banco completo: Usar mysqldump ou ferramenta de backup

## 🗺️ Roadmap

Ver [CHANGELOG.md](../CHANGELOG.md) seção "Roadmap" para:
- Notificações em tempo real
- Sistema de badges/conquistas
- Analytics avançado
- API REST para integrações

---

**Última atualização**: Fevereiro 2026  
**Versão atual**: 1.0 (Otimizada)
