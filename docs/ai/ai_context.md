# AI Context

Projeto MWS em PHP 8.2/8.3 com MVC leve e Router simples. Base path via APP_BASE_PATH. CRUDs admin concluídos (users, packages, payments, blocklist, settings). Uploads com jobs e histórico (admin gerencia). Leitor CBZ com telemetria e limites trial via settings. Conversores reais implementados: imagens -> CBZ com ZipArchive; PDF/CBR via binários externos configuráveis (pdftoppm/unrar) e fallback 7z.

Atualizações recentes:
- Cadastro em 2 etapas com termos editáveis via settings; validação de aceite e data de nascimento por dropdowns.
- Login com branding e noindex; header removível via flag e CSP sem inline JS (handlers movidos para app.js).
- Suporte completo com threads, status, anexos, token público para acompanhamento e UI admin refinada.
- Bibliotecas com busca dedicada e filtro para esconder séries vazias; favoritos por série e por item.
- Logs de busca (search_logs/series_search_logs) e tiers adicionais (assinante/restrito) com restrições no menu.
- Dashboard revisada: boas-vindas, séries favoritas, séries mais lidas e widget de últimos usuários conectados (admin).
- Upload aceita EPUB/CBR/CBZ/ZIP (imagens) e PDF (sem conversão). Limites: 5 GB e 20 arquivos.
- PDFs: leitor via modal (same-origin iframe) no desktop/Android; no iOS abre em nova aba. Download exige token.
- Biblioteca separa séries por formato via filtro (?format=pdf|cbz); séries em PDF recebem tag.
- Botões de ação na lista de capítulos com ícones; exclusão com confirmação.
- Popularidade: leituras contabilizadas por usuário único (distinct) e PDFs contam leitura ao abrir inline.
- Categorias com cor de TAG configurável no admin; badge usa cor definida.

Infra:
- Para conversão/normalização CBZ no Linux, instalar 7-Zip via apt (p7zip-full). Binário padrão em /usr/bin/7z; também pode configurar SEVENZIP_BIN.
- Em Debian, suporte RAR pode exigir repositório non-free (p7zip-rar/unrar); unrar-free funciona como alternativa disponível via repositórios padrão.
- CSP permite iframe same-origin para leitor de PDF (frame-ancestors 'self'); X-Frame-Options SAMEORIGIN.
