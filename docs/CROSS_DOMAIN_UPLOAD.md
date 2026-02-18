# Cross-Domain Upload Configuration

## Problema Original

Uploads acima de 200MB falham pelo Cloudflare. Para contornar, usa-se um domínio bypass (sem proxy) via `APP_UPLOAD_URL`.

**Desafio**: Ao mudar de domínio, o cookie de sessão pode ser perdido e o usuário é desconectado.

## Solução Implementada

Sistema de **token de transição** que mantém autenticação ao mudar entre domínios:

### 1. Token de Transição

Quando o usuário clica em "Upload", o sistema:
1. Gera um token temporário (válido por 30 segundos)
2. Armazena na sessão: user_id, IP, User-Agent, expiração
3. Adiciona `?_t=token` na URL de upload
4. Redireciona para `APP_UPLOAD_URL` com o token

### 2. Validação no Destino

Ao chegar no domínio de upload com token:
1. Valida token (não expirado, IP e UA corretos)
2. Restaura sessão do usuário
3. Remove token (uso único)
4. Redireciona para URL limpa (sem token)

### 3. Segurança

- Token válido por apenas 30 segundos
- Verifica IP e User-Agent
- Uso único (consumido após validação)
- Limpeza automática de tokens expirados

## Configuração Necessária

### Opção 1: Subdomínios do Mesmo Domínio (Recomendado)

```env
# Domínio principal (atrás do Cloudflare)
APP_URL=https://www.example.com

# Domínio de upload (bypass Cloudflare - sem proxy laranja)
APP_UPLOAD_URL=https://dash.example.com

# Cookie compartilhado entre subdomínios (com ponto inicial!)
SESSION_COOKIE_DOMAIN=.example.com
```

**Vantagens**: Cookie funciona nativamente + token como fallback.

### Opção 2: Domínios Completamente Diferentes

```env
APP_URL=https://example.com
APP_UPLOAD_URL=https://upload-direto.example.com
SESSION_COOKIE_DOMAIN=
```

**Vantagens**: Token de transição garante autenticação mesmo sem cookie compartilhado.

### Opção 3: Mesmo Domínio (Sem Bypass)

```env
APP_URL=https://example.com
APP_UPLOAD_URL=https://example.com
# ou deixe APP_UPLOAD_URL vazio
```

Sem separação de domínio, usa URL normal. Sem necessidade de token.

## Configuração do Cloudflare

### No domínio principal (www.example.com):
- ☁️ Proxy ativo (laranja)
- Todas otimizações habilitadas

### No domínio de upload (dash.example.com):
- 🌐 DNS only (cinza/sem proxy)
- Aponta direto para o servidor

## DNS Configuration Example

```
A    www     -> Cloudflare IP (proxy on)
A    dash    -> 203.0.113.10 (proxy off - DNS only)
CNAME @      -> www.mugiverso.com
```

## Como Funciona na Prática

1. **Usuário navega normalmente**: `www.example.com` (atrás do Cloudflare)
2. **Clica em "Upload"**: 
   - Sistema detecta que precisa mudar domínio
   - Gera token: `https://dash.example.com/upload?_t=abc123...`
3. **Chega no domínio de upload**:
   - Token validado
   - Sessão restaurada
   - Redirect para: `https://dash.example.com/upload` (URL limpa)
4. **Faz upload**: Direto no servidor, sem passar pelo Cloudflare
5. **Volta para navegação**: Links automáticos retornam para `www.example.com`

## Rotas que Usam Upload Domain

- `/upload` - Página de upload
- `/upload` (POST) - Submit do upload
- `/loja/request` (POST) - Submit de comprovante de pagamento
- `/loja/proof` (POST) - Upload de comprovante

Todas as outras rotas permanecem no domínio principal.

## Arquivos da Implementação

- `app/Core/CrossDomainAuth.php` - Gerenciamento de tokens
- `config/helpers.php` - Função `upload_url()` atualizada
- `public/index.php` - Validação de token e redirecionamento
- `public/index.php` - Lógica de redirecionamento entre domínios

## Troubleshooting

### Usuário ainda sendo desconectado?

1. **Verifique SESSION_COOKIE_DOMAIN**:
   ```bash
   # No .env
   SESSION_COOKIE_DOMAIN=.example.com  # COM O PONTO INICIAL!
   ```

2. **Teste o token manualmente**:
   - Acesse: `https://www.example.com/upload`
   - Verifique se URL contém `?_t=...`
   - Se não contém, usuário não está autenticado

3. **Verifique logs de auditoria**:
   ```sql
   SELECT * FROM audit_log WHERE event = 'cross_domain_auth' ORDER BY created_at DESC LIMIT 10;
   ```

4. **Teste com curl**:
   ```bash
   # Obtenha um token válido (faça login primeiro)
   curl -v 'https://dash.example.com/upload?_t=TOKEN_AQUI'
   ```

### Upload ainda falhando com arquivos grandes?

1. **Verifique DNS**: `dash.example.com` deve estar SEM proxy (cinza)
2. **PHP limits**: 
   ```ini
   upload_max_filesize = 512M
   post_max_size = 520M
   max_execution_time = 600
   ```
3. **Nginx limits**:
   ```nginx
   client_max_body_size 512M;
   client_body_timeout 600s;
   ```

## Benefícios da Solução

✅ Mantém usuário logado ao mudar de domínio
✅ Seguro (token temporário com validação)
✅ Funciona até sem cookie compartilhado
✅ Transparente para o usuário
✅ Permite uploads >200MB contornando Cloudflare
✅ URL limpa após transição
✅ Código reutilizável para futuras necessidades cross-domain
