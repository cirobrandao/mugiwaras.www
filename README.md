# MWS

Plataforma web PHP 8.3 (MVC leve) com MySQL, preparada para rodar em Nginx + PHP-FPM.

## Requisitos
- PHP 8.3 com extensões: pdo_mysql, json, mbstring, openssl, zip
- Composer
- MySQL/MariaDB (host padrão: 10.7.0.235:3306)
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
