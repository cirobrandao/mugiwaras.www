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

## Paginação

Sistema de paginação padronizado implementado nos principais módulos:

### Configuração
- **Registros por página**: 25 (padrão)
- **Parâmetro de URL**: `?page=N`
- **Primeira página**: `page=1` (ou sem parâmetro)

### Módulos com Paginação
- Upload de conteúdo (`/upload`)
- Upload administrativo (`/admin/upload`)
- Biblioteca pessoal (`/biblioteca`)
- Visualização de séries
- Pagamentos mensais (admin)
- Notificações

### Implementação
```php
// Exemplo de paginação em controller
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 25;
$totalItems = Model::countAll();
$items = Model::paginate($page, $perPage);
$totalPages = ceil($totalItems / $perPage);
```

### Performance
Com índices otimizados (013_optimize_indexes.sql):
- `LIMIT` + `OFFSET` executam 60-90% mais rápido
- Índices compostos evitam table scans
- `COUNT(*)` otimizado com índices apropriados

## Tema e Prevenção de FOUC

Sistema de tema claro/escuro com prevenção de Flash of Unstyled Content:

### Aplicação Imediata
Scripts inline no `<head>` aplicam tema antes do render:
```javascript
const theme = localStorage.getItem('theme');
if (theme === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
}
```

### Arquivos Afetados
- `app/Views/layout.php`: Layout principal
- `app/Views/upload_admin/layout.php`: Layout de upload admin
- `public/assets/js/app.js`: Aplicação de tema no DOMContentLoaded
- `public/assets/js/theme.js`: Toggle de tema
- `public/assets/css/theme.css`: Estilos de tema

### Persistência
Tema armazenado em `localStorage` para manter preferência entre sessões.

## Verificação de ambiente
Use o script abaixo para checar extensões, binários e permissões:

```
php bin/doctor.php
```
