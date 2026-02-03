# Arquitetura

## Visão geral
MVC leve com Router simples. Entrada única em public/index.php.

## Base path
APP_BASE_PATH define o subdiretório (ex: /mws). Não hardcode em views.

## Nginx (location /mws)
Exemplo básico:

```
location /mws {
	alias /var/www/mws/public;
	try_files $uri $uri/ /mws/index.php?$query_string;
}

location ~ \.php$ {
	include fastcgi_params;
	fastcgi_param SCRIPT_FILENAME $request_filename;
	fastcgi_pass unix:/run/php/php8.3-fpm.sock;
}
```

## Permissões
- storage/ (logs, uploads) precisa de escrita pelo usuário do PHP-FPM.
- public/ deve ser somente leitura para o usuário web.

## Conversores
Conversores de PDF/CBR/Imagens estão implementados em app/Core/Converters. O pipeline usa jobs (tabela jobs) e worker CLI (bin/worker.php).

### Integrações externas
Conversores usam binários externos configuráveis:

- PDFTOPPM_BIN (pdftoppm) para PDF -> imagens
- UNRAR_BIN (unrar) para CBR -> imagens

CBZ é gerado com ZipArchive.

Os conversores dependem de binários externos configurados via env e da extensão ZipArchive habilitada no PHP.

## Storage
StorageLocal escreve em storage/uploads. Outros drivers podem implementar StorageInterface.

## Segurança
Ver docs/SECURITY.md.

## Verificação de ambiente
Use o script abaixo para checar extensões, binários e permissões:

```
php bin/doctor.php
```
