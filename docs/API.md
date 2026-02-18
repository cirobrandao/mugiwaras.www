# API - Rotas da Aplicação

## Autenticação

### Público
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/` | Formulário de login |
| POST | `/login` | Processa login |
| GET | `/register` | Formulário de registro |
| POST | `/register/accept` | Aceitar termos de serviço |
| POST | `/register` | Criar conta |
| GET | `/logout` | Fazer logout |
| GET | `/reset` | Formulário de reset de senha |
| POST | `/reset` | Enviar email de reset |
| GET | `/recover` | Formulário de recuperação |

## Suporte

### Usuários e Visitantes
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/support` | Formulário de suporte |
| POST | `/support` | Enviar ticket |
| GET | `/support/track/{token}` | Rastrear ticket (sem login) |
| POST | `/support/track/{token}/reply` | Responder ticket (visitante) |
| GET | `/support/{id}` | Ver ticket (autenticado) |
| POST | `/support/{id}/reply` | Responder ticket (autenticado) |

### Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/support` | Lista de tickets |
| GET | `/admin/support/{id}` | Detalhes do ticket |
| POST | `/admin/support/status` | Alterar status |
| POST | `/admin/support/note` | Adicionar nota interna |
| POST | `/admin/support/{id}/reply` | Responder ticket |

## Dashboard

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/home` | Dashboard do usuário |
| GET | `/admin` | Dashboard administrativo |

## Avatar Gallery

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/avatar-gallery` | Galeria de avatares para seleção |

### Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/avatar-gallery` | Gerenciar avatares |
| POST | `/admin/avatar-gallery/upload` | Upload de avatar |
| POST | `/admin/avatar-gallery/update` | Atualizar avatar |
| POST | `/admin/avatar-gallery/delete` | Deletar avatar |

## Notícias

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/news/{id}` | Ver notícia |

### Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/news` | Lista de notícias |
| GET | `/admin/news/create` | Formulário de criação |
| GET | `/admin/news/edit/{id}` | Formulário de edição |
| POST | `/admin/news/create` | Criar notícia |
| POST | `/admin/news/update` | Atualizar notícia |
| POST | `/admin/news/delete` | Deletar notícia |
| POST | `/admin/news/body-image` | Upload de imagem no corpo |
| GET | `/admin/images` | Gerenciar imagens |
| POST | `/admin/images/delete` | Deletar imagem |
| POST | `/admin/news/category/create` | Criar categoria |
| POST | `/admin/news/category/update` | Atualizar categoria |
| POST | `/admin/news/category/delete` | Deletar categoria |

## Notificações

### Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/notifications` | Gerenciar notificações |
| POST | `/admin/notifications/save` | Salvar notificação |
| POST | `/admin/notifications/delete` | Deletar notificação |

## Loja e Pagamentos

### Usuários
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/loja` | Lista de pacotes disponíveis |
| GET | `/loja/checkout/{id}` | Checkout de pacote |
| POST | `/loja/request` | Solicitar pagamento |
| POST | `/loja/voucher` | Resgatar voucher |
| GET | `/loja/history` | Histórico de pagamentos |
| POST | `/loja/proof` | Upload de comprovante |

### Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/packages` | Gerenciar pacotes |
| POST | `/admin/packages/create` | Criar pacote |
| POST | `/admin/packages/update` | Atualizar pacote |
| POST | `/admin/packages/delete` | Deletar pacote |
| GET | `/admin/payments` | Lista de pagamentos |
| GET | `/admin/payments/proof/{id}` | Ver comprovante |
| GET | `/admin/payments/{id}/details` | Detalhes do pagamento |
| POST | `/admin/payments/approve` | Aprovar pagamento |
| POST | `/admin/payments/reject` | Rejeitar pagamento |
| POST | `/admin/payments/{id}/revoke` | Revogar pagamento |
| POST | `/admin/payments/revoke` | Revogar pagamento (alt) |
| POST | `/admin/payments/revoke-cancel` | Cancelar revogação |

## Vouchers

### Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/vouchers` | Gerenciar vouchers |
| POST | `/admin/vouchers/save` | Salvar voucher |
| POST | `/admin/vouchers/remove` | Remover voucher |

## Biblioteca

### Navegação
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/lib` | Biblioteca principal |
| GET | `/search` | Buscar conteúdo |
| GET | `/lib/search` | Buscar conteúdo (alt) |
| GET | `/lib/{category}` | Ver categoria |
| GET | `/lib/{category}/{series}` | Ver série |

### Gerenciamento de Conteúdo
| Método | Rota | Descrição |
|--------|------|-----------|
| POST | `/lib/content/update` | Atualizar conteúdo |
| POST | `/lib/content/order` | Reordenar conteúdo |
| POST | `/lib/content/delete` | Deletar conteúdo |
| POST | `/lib/favorite` | Favoritar conteúdo |
| POST | `/lib/read` | Marcar como lido |
| POST | `/lib/progress` | Atualizar progresso |

### Gerenciamento de Séries
| Método | Rota | Descrição |
|--------|------|-----------|
| POST | `/lib/series/update` | Atualizar série |
| POST | `/lib/series/favorite` | Favoritar série |
| POST | `/lib/series/pin` | Fixar série |
| POST | `/lib/series/read` | Marcar série como lida |
| POST | `/lib/series/unread` | Marcar série como não lida |
| POST | `/lib/series/adult` | Marcar série como adulto |
| POST | `/lib/series/delete` | Deletar série |

## Leitor

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/reader/{id}` | Abrir leitor (CBZ/imagens) |
| GET | `/reader/{id}/page/{page}` | Carregar página específica |
| GET | `/epub/{id}` | Abrir leitor EPUB |
| GET | `/pdf/{id}` | Abrir leitor PDF |
| GET | `/reader/pdf/{id}` | Abrir leitor PDF (alt) |
| GET | `/reader/pdf/{id}/page/{page}` | Página específica PDF |
| GET | `/download/{id}` | Download de arquivo |
| GET | `/download-pdf/{id}` | Download de PDF |
| GET | `/reader/file` | Abrir arquivo direto |
| GET | `/reader/file/page/{page}` | Página de arquivo direto |

## Upload

### Usuários com Permissão
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/upload` | Formulário de upload |
| POST | `/upload` | Enviar upload |
| POST | `/upload/process-pending` | Processar pendente |

### Upload Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/upload-admin/login` | Login upload admin |
| POST | `/upload-admin/login` | Processar login |
| GET | `/upload-admin/logout` | Logout upload admin |
| GET | `/upload-admin` | Formulário upload admin |

### Gerenciamento Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/uploads` | Lista de uploads |
| POST | `/admin/uploads/update` | Atualizar upload |
| POST | `/admin/uploads/approve` | Aprovar upload |
| POST | `/admin/uploads/approve-multiple` | Aprovar múltiplos |
| POST | `/admin/uploads/delete` | Deletar upload |
| POST | `/admin/uploads/delete-multiple` | Deletar múltiplos |
| POST | `/admin/uploads/delete-failed` | Deletar falhas |
| POST | `/admin/uploads/delete-failed-selected` | Deletar falhas selecionadas |

## Usuários

### Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/users` | Gerenciar usuários |
| GET | `/admin/users/import` | Importar usuários |
| POST | `/admin/users/update` | Atualizar usuário |
| POST | `/admin/users/restrict` | Restringir usuário |
| POST | `/admin/users/assign-package` | Atribuir pacote |
| POST | `/admin/users/import-preview` | Preview de importação |
| POST | `/admin/users/import-apply` | Aplicar importação |
| POST | `/admin/users/lock` | Bloquear usuário |
| POST | `/admin/users/unlock` | Desbloquear usuário |
| POST | `/admin/users/reset` | Resetar senha |
| POST | `/admin/users/team-toggle` | Toggle status equipe |
| GET | `/admin/team` | Gerenciar equipe |
| POST | `/admin/team/update` | Atualizar membro equipe |

## Categorias

### Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/categories` | Gerenciar categorias |
| POST | `/admin/categories/create` | Criar categoria |
| POST | `/admin/categories/update` | Atualizar categoria |
| POST | `/admin/categories/delete` | Deletar categoria |

## Conectores (Scrapers)

### Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/connectors` | Gerenciar conectores |
| POST | `/admin/connectors/detect` | Detectar tema WordPress |
| POST | `/admin/connectors/create` | Criar conector |
| POST | `/admin/connectors/update` | Atualizar conector |
| POST | `/admin/connectors/delete` | Deletar conector |
| GET | `/admin/connectors/download` | Download individual |
| GET | `/admin/connectors/download-all` | Download todos (.zip) |

## Segurança

### Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/security/email-blocklist` | Lista de emails bloqueados |
| POST | `/admin/security/email-blocklist/add` | Adicionar email |
| POST | `/admin/security/email-blocklist/remove` | Remover email |
| GET | `/admin/security/user-blocklist` | Lista de usuários bloqueados |
| POST | `/admin/security/user-blocklist/add` | Adicionar usuário |
| POST | `/admin/security/user-blocklist/remove` | Remover usuário |

## Configurações

### Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/settings` | Configurações do sistema |
| POST | `/admin/settings/save` | Salvar configurações |

## Logs

### Administrativo
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/log` | Visualizar logs do sistema |

## Assets (CDN Integrado)

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/assets/bootstrap.min.css` | Bootstrap CSS |
| GET | `/assets/bootstrap.min.css.map` | Bootstrap CSS Map |
| GET | `/assets/bootstrap.bundle.min.js` | Bootstrap JS |
| GET | `/assets/bootstrap.bundle.min.js.map` | Bootstrap JS Map |

## Middleware e Permissões

### Níveis de Acesso
- **Público**: Sem autenticação
- **requireAuth()**: Usuário autenticado
- **requireActiveAccess()**: Acesso ativo (assinatura válida)
- **requireUploadAccess()**: Permissão de upload
- **requireSupportStaff()**: Equipe de suporte
- **requireTeamAccess()**: Admin ou equipe
- **requireAdmin()**: Somente administrador
- **requireRole(['superadmin'])**: Superadministrador

### CSRF Protection
Todas as rotas POST requerem token CSRF válido.

### Rate Limiting
- Login: Limite por IP
- Suporte: Limite por usuário
- API: Limite por endpoint

## Paginação

Rotas com listagens suportam paginação:
- **Parâmetro**: `?page=N`
- **Registros por página**: 25 (padrão)
- **Primeira página**: `page=1` ou sem parâmetro

**Módulos com paginação**:
- `/upload` (uploads pendentes/processados)
- `/admin/upload` (gerenciamento de uploads)
- `/admin/users` (lista de usuários)
- `/admin/payments` (pagamentos)
- `/admin/support` (tickets)
- `/lib` (biblioteca)
