# Banco de Dados

Scripts em /sql:
- schema.sql (estado atual do schema)
- 001_*.sql até 012_*.sql (migrações aplicadas)
- 013_optimize_indexes.sql (otimizações de índices - Fev 2026)

## Tabelas principais
- users (índices: username, email, role, access_tier, data_ultimo_login)
- user_tokens
- support_messages (índices: status, created_at, user_id)
- email_blocklist
- packages
- payments (índices: status, created_at, user_id, package_id)
- jobs
- audit_log (índices: event, created_at, user_id, ip)
- settings
- libraries
- content_items (índices: series_id, status, category_id, created_at)
- series (índices: user_id, status, category_id, slug)
- categories (índices: slug)
- connectors
- vouchers

## Otimização de Índices

### Script de Gerenciamento
Utilize `bin/optimize_indexes.php` para gerenciar índices:

```bash
# Verificar índices sugeridos vs existentes
php bin/optimize_indexes.php check

# Criar backup manual
php bin/optimize_indexes.php backup

# Aplicar otimizações (backup automático)
php bin/optimize_indexes.php apply

# Atualizar estatísticas
php bin/optimize_indexes.php analyze

# Relatório completo
php bin/optimize_indexes.php report
```

### Índices Implementados (013_optimize_indexes.sql)

**Estratégia**: Índices compostos para queries com múltiplos filtros (WHERE + ORDER BY).

#### Users (10 índices)
- `idx_username`: Busca por username
- `idx_email`: Busca por email
- `idx_role`: Filtro por papel
- `idx_access_tier`: Filtro por nível de acesso
- `idx_data_ultimo_login`: Ordenação por último login
- Compostos: role+ultimo_login, access_tier+ultimo_login, etc.

#### Content Items (6 índices)
- `idx_series_id`: Filtro por série
- `idx_status`: Filtro por status
- `idx_category_id`: Filtro por categoria
- Compostos: series+status, series+created_at, status+created_at

#### Payments (4 índices)
- `idx_status`: Filtro por status
- `idx_created_at`: Ordenação temporal
- Compostos: user+status, status+created_at

#### Audit Log (4 índices)
- `idx_event`: Filtro por tipo de evento
- `idx_created_at`: Ordenação temporal
- Compostos: event+created_at, user+event

**E mais**: Series (3), Support Messages (3), Search Logs (2), Notifications (2), Uploads (2), Vouchers (2), Connectors (2), Categories (2), Email Blocklist (1), Jobs (1), Packages (1), Password Resets (1)

### Performance
- Queries de listagem: **60-90% mais rápidas**
- Índices compostos reduzem table scans
- ANALYZE TABLE mantém estatísticas atualizadas

### Backups
Antes de aplicar mudanças de índices, um backup SQL é criado automaticamente em:
```
storage/backups/indexes_backup_YYYY-MM-DD_HHMMSS.sql
```

Para restaurar:
```bash
mysql -u user -p database < storage/backups/indexes_backup_*.sql
```

## Migrações
Nunca edite migrações antigas. Crie novas como 014_*.sql e atualize schema.sql.

### Processo de Migração
1. Crie arquivo `sql/XXX_descricao.sql`
2. Execute manualmente: `mysql -u user -p < sql/XXX_descricao.sql`
3. Atualize `sql/schema.sql` com estado final
4. Documente em CHANGELOG.md

## Manutenção

### Verificar Tamanho das Tabelas
```sql
SELECT 
    table_name AS 'Tabela',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Tamanho (MB)'
FROM information_schema.TABLES
WHERE table_schema = 'nome_do_banco'
ORDER BY (data_length + index_length) DESC;
```

### Verificar Índices Não Utilizados
```sql
SELECT * FROM sys.schema_unused_indexes
WHERE object_schema = 'nome_do_banco';
```

### Otimizar Tabelas
```sql
ANALYZE TABLE nome_da_tabela;
OPTIMIZE TABLE nome_da_tabela;  -- Use com cuidado em produção
```

## Considerações de Performance

### Índices Compostos
A ordem das colunas importa:
```sql
-- Índice (a, b, c) funciona para:
WHERE a = ? AND b = ?
WHERE a = ?
ORDER BY a, b

-- MAS NÃO para:
WHERE b = ?
WHERE c = ?
```

### Evitar
- SELECT * (especifique colunas necessárias)
- Queries N+1 (use JOINs ou carregamento batch)
- OR em WHERE (prefira IN ou UNION)
- Funções em WHERE (ex: WHERE YEAR(data) = 2026)

### Preferir
- Prepared statements (sempre)
- LIMIT em queries exploratórias
- Índices covering (todas colunas no índice)
- Particionamento para tabelas históricas grandes
