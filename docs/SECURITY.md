# Segurança

- CSRF em todos os POST
- Sessão segura (HttpOnly, Secure, SameSite)
- Rate limit básico para login e suporte
- Lockout após tentativas falhas
- Auditoria de eventos (audit_log)
- Cabeçalhos: CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy

## Auditoria de Login

### Eventos Registrados
- `login_success`: Login bem-sucedido
- `login_fail`: Tentativa de login falhou
- `logout`: Usuário fez logout

### Monitoramento
Dashboard administrativo exibe:
- **Últimos Logins**: 15 logins bem-sucedidos mais recentes
- **Falhas de Login**: 20 tentativas falhas mais recentes

### Informações Capturadas
- Username (tentado ou autenticado)
- IP (IPv4 ou IPv6)
- User-Agent (navegador/SO)
- Timestamp preciso
- Resultado da tentativa

### Detecção de Ameaças
Sistema diferencia:
- ⚠️ **Tentativas com username válido**: Ataque direcionado ou erro de senha
- ⛔ **Tentativas com username inexistente**: Scanner automático ou força bruta

## Remember me
Tokens armazenados como hash (sha256) na tabela user_tokens. Rotação a cada uso.

## Rate Limiting

### Limites Aplicados
- **Login**: Máximo de tentativas por IP/período
- **Suporte**: Limite de mensagens por usuário/período
- **API**: Rate limit por endpoint

### Armazenamento
- Storage: `storage/ratelimit/`
- Formato: Hash do identificador (IP ou user_id)
- Limpeza automática de registros expirados

## Lockout de Conta

Após N tentativas falhas consecutivas:
1. Conta temporariamente bloqueada
2. Email de notificação enviado (se configurado)
3. Registrado em `audit_log`
4. Requer reset de senha ou espera de timeout

## Cross-Domain Authentication

Sistema de tokens de transição para manter autenticação entre domínios:

### Caso de Uso
Upload em domínio separado (bypass Cloudflare) mantendo sessão:
```
www.mugiverso.com → dash.mugiverso.com
```

### Segurança do Token
- Válido por apenas 30 segundos
- Uso único (consumido após validação)
- Verifica IP e User-Agent
- Armazenado em sessão, não em banco
- Limpeza automática de tokens expirados

### Implementação
Ver `docs/CROSS_DOMAIN_UPLOAD.md` para detalhes completos.

## Boas práticas
- Não commit .env
- Use senhas fortes e hashes via password_hash
- Atualize CSP conforme necessidade de fontes
- Monitore `audit_log` regularmente
- Configure alertas para múltiplas falhas de login
- Revise User-Agents suspeitos
- Mantenha logs por pelo menos 90 dias

## Análise de Segurança

### Identificar IPs Suspeitos
```sql
-- IPs com mais de 10 falhas de login
SELECT ip, COUNT(*) as attempts, 
       MAX(created_at) as last_attempt
FROM audit_log
WHERE event = 'login_fail'
GROUP BY ip
HAVING attempts > 10
ORDER BY attempts DESC;
```

### Usernames Mais Atacados
```sql
-- Usernames alvos de ataques
SELECT details->>'$.username' as username,
       COUNT(*) as attempts
FROM audit_log
WHERE event = 'login_fail'
GROUP BY username
ORDER BY attempts DESC
LIMIT 20;
```

### Bloqueio Manual de IP
Para bloquear IPs no Nginx:
```nginx
# /etc/nginx/block-ips.conf
deny 192.0.2.10;
deny 198.51.100.0/24;
deny 2001:db8::/32;
```

Incluir em configuração:
```nginx
include /etc/nginx/block-ips.conf;
```
