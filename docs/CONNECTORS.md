# Sistema de Conectores para Scraper

Sistema administrativo para gerar conectores .mjs para scrapers de mangá (HakuNeko, etc).

## 📋 Sumário

1. [O que são Conectores](#o-que-são-conectores)
2. [Instalação](#instalação)
3. [Como Usar](#como-usar)
4. [Estrutura de Código Gerado](#estrutura-de-código-gerado)
5. [Templates Suportados](#templates-suportados)

## 🔌 O que são Conectores

Conectores são módulos JavaScript (.mjs) que permitem que scrapers acessem e baixem conteúdo de sites específicos. Este sistema gera automaticamente esses conectores para sites WordPress.

## 🚀 Instalação

### 1. Executar Migração SQL

```bash
mysql -u usuario -p database < sql/012_connectors.sql
```

Ou execute no phpMyAdmin/HeidiSQL:

```sql
CREATE TABLE IF NOT EXISTS connectors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    label VARCHAR(255) NOT NULL,
    url TEXT NOT NULL,
    template VARCHAR(50) NOT NULL DEFAULT 'WordPressMadara',
    class_name VARCHAR(255) NOT NULL,
    tags JSON NULL,
    custom_config JSON NULL,
    generated_code LONGTEXT NOT NULL,
    created_by INT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2. Acessar Página Administrativa

Acesse: `/admin/connectors`

Ou pelo dashboard admin → **Conectores**

## 📖 Como Usar

### Criar Novo Conector

1. **Digite a URL do site**
   ```
   https://exemplo.com
   ```

2. **Clique em "Detectar Tema"**
   - O sistema tentará identificar automaticamente o tema WordPress
   - **IMPORTANTE**: Só é possível criar conectores com confiança ALTA
   - Sites que não forem WordPress serão rejeitados
   - Preencherá o nome do site automaticamente
   - Sugerirá o template apropriado (Madara ou MangaStream)

3. **Configure os campos**:
   - **Nome/Label**: Nome exibido no scraper (ex: "Exemplo Scan")
   - **Template**: Tema WordPress detectado (ex: WordPressMadara ou WordPressMangaStream)
   - **Tags**: Categorias separadas por vírgula (ex: `manga, portuguese, webtoon`)
   - **Seletor CSS** (opcional): Seletor customizado para queryTitleForURI
   - **Path** (opcional, MangaStream): Caminho customizado (ex: `/read/list-mode/`)

4. **Criar Conector**
   - Clique em "Criar Conector"
   - Sistema validará se é WordPress e se tem confiança alta
   - O código .mjs será gerado automaticamente

### Download de Conectores

#### Download Individual
- Na lista de conectores, clique no botão **Download** (ícone ⬇️)
- Arquivo `NomeDoConector.mjs` será baixado

#### Download em Lote
- Clique em **"Baixar Todos (.zip)"**
- Um arquivo ZIP com todos os conectores será gerado
- Extraia os arquivos .mjs no diretório do scraper

### Editar Conector

1. Clique no botão **Editar** (ícone ✏️)
2. Modifique os campos necessários
3. O código será regenerado automaticamente ao salvar

### Deletar Conector

1. Clique no botão **Deletar** (ícone 🗑️)
2. Confirme a exclusão
3. **Atenção**: Esta ação não pode ser desfeita

## 📝 Estrutura de Código Gerado

### Exemplo: WordPressMadara

```javascript
import WordPressMadara from './templates/WordPressMadara.mjs';

export default class ExemploScan extends WordPressMadara {

    constructor() {
        super();
        super.id = 'exemploscan';
        super.label = 'Exemplo Scan';
        this.tags = ["manga", "portuguese"];
        this.url = 'https://exemplo.com/';
        this.requestOptions.headers.set('x-referer', this.url);

        // Configurações customizadas (opcional)
        this.queryTitleForURI = 'div.profile-manga div.post-title h1';
    }
}
```

### Exemplo: WordPressMangaStream

```javascript
import WordPressMangaStream from './templates/WordPressMangaStream.mjs';

export default class WhiteCloudPavilion extends WordPressMangaStream {

    constructor() {
        super();
        super.id = 'whitecloudpavilion';
        super.label = 'White Cloud Pavilion';
        this.tags = [ 'manga', 'high-quality', 'english', 'scanlation' ];
        this.url = 'https://www.whitecloudpavilion.com';
        this.path = '/read/list-mode/';
    }
}
```

### Componentes do Código

**Comum a todos os templates:**
- **id**: Identificador único (gerado automaticamente do domínio)
- **label**: Nome exibido no scraper
- **tags**: Array de categorias do conector
- **url**: URL base do site

**Específico do WordPressMadara:**
- **x-referer**: Header HTTP para evitar bloqueios
- **queryTitleForURI**: Seletor CSS para extração de títulos (opcional)

**Específico do WordPressMangaStream:**
- **path**: Caminho para lista de manga (ex: `/read/list-mode/`)
- Não usa `x-referer`

## 🎨 Templates Suportados

### ✅ WordPressMadara
- **Status**: ✓ Implementado
- **Descrição**: Template para sites WordPress com tema Madara
- **Detecção**: Automática via análise HTML (requer confiança ALTA)
- **Características**: 
  - Usa header `x-referer`
  - Seletor CSS customizável
  - Suporta series e chapters
- **Exemplos**: Sites de scan populares

### ✅ WordPressMangaStream
- **Status**: ✓ Implementado
- **Descrição**: Template para tema MangaStream
- **Detecção**: Automática via análise HTML (requer confiança ALTA)
- **Características**:
  - Requer propriedade `path` (ex: `/read/list-mode/`)
  - Auto-detecta path se disponível
  - Não usa header `x-referer`
- **Exemplos**: White Cloud Pavilion, sites similar

### 🚧 Em Desenvolvimento

#### Custom
- **Status**: Planejado
- **Descrição**: Template genérico customizável
- **Previsão**: Próxima versão

## 🔒 Validação e Segurança

### Requisitos para Criar Conectores

O sistema só permite criar conectores se **TODOS** os critérios forem atendidos:

#### 1. Site deve ser WordPress
- Verifica presença de `wp-content`, `wp-includes`
- Busca meta tag generator do WordPress
- Analisa estrutura de diretórios

#### 2. Confiança deve ser ALTA
Sistema de pontuação para determinar confiança:

**Confiança ALTA** (necessária para criação):
- WordPress Madara:
  - Presença de palavra "madara" no HTML (2 pontos)
  - Tema no path `/wp-content/themes/[algo]madara/` (2 pontos)
  - Classes CSS específicas como `manga-action`, `c-chapter`, etc (1 ponto cada)
  - **Mínimo 3 pontos**

- WordPress MangaStream:
  - Presença de "mangastream" no HTML (2 pontos)
  - Path `/read/list-mode/` detectado (2 pontos)
  - Tema no path `/wp-content/themes/[algo]mangastream/` (2 pontos)
  - Classes específicas de lista de manga (1 ponto)
  - **Mínimo 3 pontos**

**Confiança MÉDIA** (não permite criação):
- WordPress detectado (1-2 indicadores)
- Tema incerto

**Confiança BAIXA** (não permite criação):
- WordPress duvidoso (0-1 indicadores)
- Não é WordPress

### Mensagens de Erro

- `Site não é WordPress ou não pode ser acessado` - Site não passou na validação WordPress
- `Confiança de detecção muito baixa` - Sistema não tem certeza suficiente do tema
- `Preencha todos os campos obrigatórios` - Campos vazios
- `Já existe um conector para este site` - Conector duplicado

## 🔍 Detecção Automática de Tema

O sistema analisa o HTML do site procurando por:

1. **Indicadores WordPress** (obrigatório):
   - Presença de `/wp-content/`, `/wp-includes/` nos recursos
   - Meta tag generator do WordPress
   - API REST `/wp-json/`
   - Scripts/CSS característicos
   - **Mínimo 2 indicadores** para ser considerado WordPress

2. **Indicadores Madara** (para confiança ALTA):
   - Classes CSS específicas: `.manga-action`, `.c-chapter`, `.post-title`
   - Palavra "madara" no HTML
   - Pasta de tema `/themes/[algo]madara/`
   - Estrutura HTML característica
   - **Mínimo 3 pontos** para confiança alta

3. **Indicadores MangaStream** (para confiança ALTA):
   - Palavra "mangastream" no HTML
   - Path `/read/list-mode/` detectado
   - Pasta de tema `/themes/[algo]mangastream/`
   - Classes de lista de manga
   - **Mínimo 3 pontos** para confiança alta

4. **Níveis de Confiança**:
   - **High** ✓: Pode criar conector (múltiplos indicadores fortes)
   - **Medium** ✗: Não pode criar (WordPress detectado, tema incerto)
   - **Low** ✗: Não pode criar (análise inconclusiva ou não é WordPress)

## 🛠️ Uso dos Conectores

### HakuNeko

1. Baixe o conector .mjs
2. Coloque em: `[HakuNeko]/src/engine/websites/`
3. Adicione o import no arquivo de índice
4. Recompile o HakuNeko

### Scraper Personalizado

```javascript
import ExemploScan from './connectors/ExemploScan.mjs';

const connector = new ExemploScan();
const mangas = await connector.getMangas();
```

## 📊 Informações Armazenadas

Para cada conector, o sistema armazena:

- **name**: Identificador único
- **label**: Nome de exibição
- **url**: URL do site
- **template**: Template usado
- **class_name**: Nome da classe JavaScript
- **tags**: Tags JSON
- **custom_config**: Configurações customizadas JSON
- **generated_code**: Código .mjs completo
- **created_by**: ID do admin que criou
- **created_at**: Data de criação
- **updated_at**: Data da última edição

## 🔐 Segurança

- ✅ Acesso restrito a administradores
- ✅ Proteção CSRF em todas as operações
- ✅ Validação de URLs
- ✅ Sanitização de inputs
- ✅ Timeout em requisições de detecção (10s)

## 🐛 Troubleshooting

### "Tabela de conectores não encontrada"
**Solução**: Execute a migração `012_connectors.sql`

### "Não foi possível acessar o site"
**Possíveis causas**:
- Site está offline
- Firewall bloqueando requisições
- SSL inválido
- Timeout (mais de 10s)

**Solução**: Verifique se o site está acessível e tente novamente

### "Erro ao criar arquivo ZIP"
**Solução**: Verifique permissões da pasta temporária do PHP

### Conector não funciona no scraper
**Checklist**:
1. Template correto foi selecionado?
2. Seletor CSS customizado está correto?
3. Site mudou de estrutura recentemente?
4. Headers necessários estão configurados?

## 📞 Suporte

Para problemas ou sugestões:
- Verifique o log do sistema: `/admin/log`
- Teste a detecção automática novamente
- Tente criar manualmente com seletor customizado

## 🔄 Próximas Versões

- [ ] Suporte a mais templates (MangaStream, etc)
- [ ] Editor de código inline
- [ ] Teste de conectores direto na interface
- [ ] Histórico de versões
- [ ] Import/export de conectores
- [ ] API REST para geração automatizada
