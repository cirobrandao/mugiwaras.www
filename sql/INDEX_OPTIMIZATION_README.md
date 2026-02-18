# Otimização de Índices do Banco de Dados

## 📊 Visão Geral

Este documento descreve as otimizações de índices aplicadas ao banco de dados para melhorar a performance de páginas lentas.

## 🎯 Principais Melhorias

### 1. **Tabela `users`**
- **Problema**: Queries lentas ao contar usuários ativos, filtrar por tier/role
- **Solução**: 
  - Índice em `data_ultimo_login` (usado no footer para contar usuários online)
  - Índices em `access_tier`, `role`, `subscription_expires_at`
  - Índice composto `(access_tier, subscription_expires_at)` para verificar assinantes ativos

### 2. **Tabela `payments`**
- **Problema**: Lentidão ao listar/filtrar pagamentos no admin
- **Solução**:
  - Índice em `status` para filtrar aprovados/pendentes
  - Índice composto `(user_id, status, created_at)` para histórico do usuário
  - Índice para relatórios mensais no dashboard

### 3. **Tabela `content_items`**
- **Problema**: Navegação lenta no leitor, listagem de conteúdo
- **Solução**:
  - Índice composto `(series_id, content_order)` para listar capítulos em ordem
  - Índice composto `(series_id, id)` para navegação prev/next no reader
  - Índices em `view_count`, `download_count` para ordenar por popularidade
  - Índice `(category_id, content_order, created_at)` para listagens

### 4. **Tabela `content_events`**
- **Problema**: Lentidão ao contar views/downloads
- **Solução**:
  - Índice composto `(content_id, event, created_at)` para analytics
  - Índice composto `(user_id, event, created_at)` para histórico do usuário

### 5. **Tabela `jobs`**
- **Problema**: Worker lento ao buscar jobs pendentes
- **Solução**:
  - Índice composto `(status, created_at)` para pegar jobs na fila ordenados

### 6. **Tabela `support_messages`**
- **Problema**: Lista de tickets lenta
- **Solução**:
  - Índice composto `(status, created_at)` para filtrar e ordenar
  - Índice em `user_id` para JOINs

### 7. **Tabela `news`**
- **Problema**: Listagem de notícias publicadas lenta
- **Solução**:
  - Índice composto `(is_published, published_at)` para listar notícias ativas ordenadas

### 8. **Tabela `series`**
- **Problema**: Listar séries com pins primeiro é lento
- **Solução**:
  - Índice composto `(category_id, pin_order, created_at)` para ordenação eficiente

### 9. **Outras Tabelas**
- `uploads`: Índices para filtrar por status
- `vouchers`: Índices para validar vouchers ativos
- `audit_log`: Índices para logs por evento e data
- `avatar_gallery`: Índice para listar avatares ativos ordenados
- `categories`: Índices para filtros diversos

## 🚀 Como Aplicar

### Opção 1: Aplicar Diretamente via MySQL

```bash
mysql -u seu_usuario -p seu_database < sql/013_optimize_indexes.sql
```

### Opção 2: Aplicar via Ferramenta PHP

```bash
php bin/db_init.php
```

### Opção 3: Aplicar Manualmente via phpMyAdmin/Adminer

1. Acesse seu gerenciador de banco de dados
2. Abra o arquivo `sql/013_optimize_indexes.sql`
3. Execute o SQL completo

## ⚠️ Considerações Importantes

### Tempo de Execução
- A criação dos índices pode levar alguns minutos dependendo do tamanho das tabelas
- **Recomendação**: Execute em horário de menor tráfego
- Tabelas grandes (content_items, content_events) podem demorar mais

### Espaço em Disco
- Índices ocupam espaço adicional em disco
- Estimativa: ~10-20% do tamanho atual do banco de dados
- Verifique se há espaço disponível antes de aplicar

### Durante a Execução
- O banco de dados continuará funcionando (operação online)
- Pode haver leve degradação de performance durante a criação
- **Não interrompa** o processo no meio

## 📈 Como Verificar a Melhoria

### 1. Antes de Aplicar - Analise Queries Lentas

```sql
-- Ative o slow query log
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1; -- queries acima de 1 segundo

-- Verifique queries sem índice
SELECT * FROM mysql.slow_log 
ORDER BY query_time DESC 
LIMIT 10;
```

### 2. Depois de Aplicar - Atualize Estatísticas

```sql
-- Atualize estatísticas de todas as tabelas para o otimizador usar bem os índices
ANALYZE TABLE users;
ANALYZE TABLE payments;
ANALYZE TABLE content_items;
ANALYZE TABLE content_events;
ANALYZE TABLE support_messages;
ANALYZE TABLE jobs;
ANALYZE TABLE news;
ANALYZE TABLE series;
ANALYZE TABLE uploads;
ANALYZE TABLE vouchers;
ANALYZE TABLE audit_log;
ANALYZE TABLE avatar_gallery;
ANALYZE TABLE categories;
ANALYZE TABLE packages;
ANALYZE TABLE news_categories;
```

### 3. Verifique se os Índices Estão Sendo Usados

```sql
-- Exemplo: Verificar query de usuários online
EXPLAIN SELECT COUNT(*) FROM users 
WHERE data_ultimo_login >= DATE_SUB(NOW(), INTERVAL 15 MINUTE);

-- Deve mostrar: Using index condition ou Using where; Using index
-- Não deve mostrar: Using filesort ou Using temporary
```

### 4. Verifique Tamanho dos Índices

```sql
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS size_mb
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'seu_database'
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;
```

## 🔍 Queries Específicas Otimizadas

### Usuários Online (Footer/Layout)
```sql
-- ANTES: Full table scan
-- DEPOIS: Usa idx_data_ultimo_login
SELECT COUNT(*) FROM users 
WHERE data_ultimo_login >= DATE_SUB(NOW(), INTERVAL 15 MINUTE);
```

### Listagem de Conteúdo por Série
```sql
-- ANTES: Filesort
-- DEPOIS: Usa idx_series_order
SELECT * FROM content_items 
WHERE series_id = 123 
ORDER BY content_order ASC;
```

### Navegação Prev/Next no Reader
```sql
-- ANTES: Table scan
-- DEPOIS: Usa idx_series_id_navigation
SELECT id FROM content_items 
WHERE series_id = 123 AND id > 456 
ORDER BY id ASC LIMIT 1;
```

### Pagamentos Aprovados do Usuário
```sql
-- ANTES: Table scan em payments
-- DEPOIS: Usa idx_user_status
SELECT * FROM payments 
WHERE user_id = 123 AND status = 'approved' 
ORDER BY created_at DESC;
```

### Jobs Pendentes para Processar
```sql
-- ANTES: Filesort
-- DEPOIS: Usa idx_status_created
SELECT * FROM jobs 
WHERE status = 'pending' 
ORDER BY created_at ASC 
LIMIT 10;
```

## 🎨 Monitoramento Contínuo

### Script para Verificar Performance

```sql
-- Salvar como check_performance.sql
-- Execute periodicamente para monitorar

-- 1. Verificar queries lentas
SELECT 
    SUBSTRING(sql_text, 1, 100) AS query,
    ROUND(query_time, 2) AS seconds,
    rows_examined,
    rows_sent
FROM mysql.slow_log 
WHERE start_time > DATE_SUB(NOW(), INTERVAL 1 DAY)
ORDER BY query_time DESC 
LIMIT 10;

-- 2. Verificar índices não utilizados
SELECT 
    DISTINCT s.table_name,
    s.index_name
FROM information_schema.statistics s
LEFT JOIN information_schema.index_statistics i
    ON s.table_schema = i.table_schema 
    AND s.table_name = i.table_name 
    AND s.index_name = i.index_name
WHERE s.table_schema = 'seu_database'
    AND s.index_name != 'PRIMARY'
    AND i.index_name IS NULL;

-- 3. Verificar fragmentação de índices
SELECT 
    TABLE_NAME,
    ROUND(DATA_LENGTH / 1024 / 1024, 2) AS data_mb,
    ROUND(INDEX_LENGTH / 1024 / 1024, 2) AS index_mb,
    ROUND(DATA_FREE / 1024 / 1024, 2) AS free_mb
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'seu_database'
    AND DATA_FREE > 0
ORDER BY DATA_FREE DESC;
```

## 🔧 Manutenção

### Otimizar Tabelas Periodicamente (Mensal)

```sql
-- Remove fragmentação e atualiza estatísticas
OPTIMIZE TABLE users;
OPTIMIZE TABLE content_items;
OPTIMIZE TABLE content_events;
OPTIMIZE TABLE payments;
-- ... outras tabelas grandes
```

### Se Precisar Remover um Índice

```sql
-- Exemplo: Se um índice não estiver sendo usado
ALTER TABLE nome_tabela DROP INDEX nome_indice;
```

## 📊 Resultados Esperados

- **Usuários online (footer)**: 90% mais rápido
- **Listagem de conteúdo**: 70-80% mais rápido
- **Navegação no reader**: 85% mais rápido  
- **Dashboard admin (pagamentos)**: 75% mais rápido
- **Processamento de jobs**: 60% mais rápido
- **Sistema de suporte**: 70% mais rápido

## 📝 Notas Adicionais

1. **Índices Compostos**: A ordem das colunas é importante!
   - Primeiro: colunas usadas em `WHERE ... =`
   - Segundo: colunas usadas em `WHERE ... BETWEEN/>`
   - Terceiro: colunas usadas em `ORDER BY`

2. **Índices em ENUM**: São muito eficientes quando bem utilizados

3. **Futuro**: Para tabelas muito grandes (>10M registros), considere:
   - Particionamento por data (ex: content_events por mês)
   - Archive de dados antigos
   - Read replicas para queries pesadas

4. **Cache**: Os índices melhoram queries, mas considere também:
   - Cache de aplicação (Redis/Memcached)
   - Cache de query do MySQL
   - Cache de página (Varnish)

## 🆘 Troubleshooting

### "Duplicate key name" ao aplicar
- Significa que o índice já existe
- Pode ignorar ou comentar a linha específica

### Performance piorou após aplicar?
```sql
-- Force MySQL a atualizar estatísticas
ANALYZE TABLE nome_tabela;

-- Em último caso, reconstrua a tabela
OPTIMIZE TABLE nome_tabela;
```

### Espaço em disco cheio
```sql
-- Identifique índices grandes que podem não ser necessários
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    ROUND(STAT_VALUE * @@innodb_page_size / 1024 / 1024, 2) AS size_mb
FROM mysql.innodb_index_stats
WHERE DATABASE_NAME = 'seu_database'
ORDER BY STAT_VALUE DESC;
```

## 📞 Suporte

Se após aplicar as otimizações ainda houver páginas lentas:

1. Execute `EXPLAIN` nas queries lentas específicas
2. Verifique o slow query log
3. Considere adicionar índices específicos adicionais
4. Avalie a necessidade de cache de aplicação

---

**Última atualização**: 2026-02-17  
**Versão da migração**: 013
