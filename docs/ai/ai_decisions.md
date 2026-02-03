# AI Decisions

- MVC leve sem framework pesado.
- Pipeline de conversão baseado em jobs + worker.
- Security headers configuráveis via config/security.php.
- Reset de senha via token com tabela password_resets.
- Conversores reais implementados para imagens -> CBZ com ZipArchive; PDF/CBR via binários externos configuráveis (pdftoppm/unrar) e fallback 7z.
- CSP ativa sem inline JS; interações em JS central (public/assets/js/app.js e reader.js).
- Para leitor de PDF, permitir iframe same-origin (CSP frame-ancestors 'self' e X-Frame-Options SAMEORIGIN).
