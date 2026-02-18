# MWS

Plataforma web PHP 8.3 (MVC leve) com MySQL, preparada para rodar em Nginx + PHP-FPM.

## Requisitos
- PHP 8.3 com extensões: pdo_mysql, json, mbstring, openssl, zip
- Composer
- MySQL/MariaDB
- Nginx + PHP-FPM 8.3

## Instalação
1) Instale dependências:
```
composer install
```
2) Copie `.env.example` para `.env` e ajuste as variáveis.
3) Inicialize banco:
```
php bin/db_init.php
```
4) Verifique pré-requisitos do ambiente:
```
php bin/doctor.php
```
5) Configure Nginx (exemplo em docs).

## Segurança
- CSRF em todos os POST
- Rate limit em login e suporte
- Lockout de login
- Auditoria de eventos
- Cabeçalhos de segurança (CSP, XFO, XCTO, Referrer-Policy)

## Regras de senha
- Mínimo 8 e máximo 20
- Pelo menos 1 letra e 1 número
- Símbolo é opcional

## Workers
Processamento de jobs:
```
php bin/worker.php
```

## Otimização de Banco de Dados
Sistema de gerenciamento de índices para melhor performance:

```bash
# Verificar índices recomendados
php bin/optimize_indexes.php check

# Criar backup dos índices atuais
php bin/optimize_indexes.php backup

# Aplicar otimizações (cria backup automático)
php bin/optimize_indexes.php apply

# Atualizar estatísticas das tabelas
php bin/optimize_indexes.php analyze

# Gerar relatório completo
php bin/optimize_indexes.php report
```

**Benefícios**: Melhoria de 60-90% em performance de queries otimizadas.
**Backups**: Salvos em `storage/backups/` com timestamp.

Ver [docs/CHANGELOG.md](docs/CHANGELOG.md) para histórico completo de mudanças.

## Conversao CBZ -> PDF
Script para gerar PDF a partir de CBZ mantendo ambos no servidor (nao cria novo volume no banco):
```
php bin/cbz_to_pdf.php
```
Opcoes:
```
php bin/cbz_to_pdf.php --series=123
php bin/cbz_to_pdf.php --content=456
php bin/cbz_to_pdf.php --limit=50
php bin/cbz_to_pdf.php --dry-run
php bin/cbz_to_pdf.php --force
php bin/cbz_to_pdf.php --magick=/usr/bin/magick
php bin/cbz_to_pdf.php --magick=/usr/bin/convert
```
Padroes fixos (para evitar cache exhausted):
```
--max-width=1600 --quality=85 --limit-memory=256MiB --limit-map=512MiB --limit-disk=2GiB
```
Dependencias no Ubuntu:
- PHP CLI 8.3 com extensoes: zip
- ImageMagick (binario `magick` ou `convert`) ou extensao `php-imagick`
Exemplo de instalacao (Ubuntu):
```
sudo apt-get update
sudo apt-get install -y imagemagick
sudo apt-get install -y php8.3-cli php8.3-zip php8.3-imagick
```

## Estrutura
- public/: front controller e assets
- app/: Core, Controllers, Models, Views
- config/: configuração e bootstrap
- storage/: logs, uploads, cache
- sql/: migrações e schema atual
- docs/: documentação

## Deploy Nginx (location /mws)
Ver detalhes em docs/ARCHITECTURE.md.

## Notas
- Não hardcode de base path: use APP_BASE_PATH.
- Conversores reais estão implementados e dependem de binários externos configurados.

## Upload em subdomínio dedicado
Para enviar uploads via host separado (ex.: `https://upload.exemplo.com`) e manter o restante no domínio principal:

- Configure `APP_UPLOAD_URL=https://upload.exemplo.com`
- Configure `SESSION_COOKIE_DOMAIN=.exemplo.com` para compartilhar sessão entre subdomínios

Sem `APP_UPLOAD_URL`, o sistema continua usando `APP_URL` normalmente.
