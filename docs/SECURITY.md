# Segurança

- CSRF em todos os POST
- Sessão segura (HttpOnly, Secure, SameSite)
- Rate limit básico para login e suporte
- Lockout após tentativas falhas
- Auditoria de eventos (audit_log)
- Cabeçalhos: CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy

## Remember me
Tokens armazenados como hash (sha256) na tabela user_tokens. Rotação a cada uso.

## Boas práticas
- Não commit .env
- Use senhas fortes e hashes via password_hash
- Atualize CSP conforme necessidade de fontes
