# SPEC - Internacionalização e Tradução (pt-BR)

## 🎯 Objetivo
Traduzir integralmente a interface do usuário (front-end) para Português do Brasil (pt-BR), garantindo que todos os componentes nativos do Filament, mensagens de sistema, labels de formulários e textos customizados estejam localizados corretamente.

---

## 🎨 DIRETRIZES DE LINGUAGEM
- **Tom de Voz**: Profissional, direto e técnico (adequado para uma ferramenta de IA sênior).
- **Terminologia RAG (Mantida em Inglês)**: 
  - *Chunks*, *Embedding*, *Retrieval*, *Chat*, *Model*, *Scoped*, *Mime Type*, *Metadata*.
- **Localização de Fluxo**: Traduzir apenas ações, botões, labels de interface e mensagens de status.
- **Tradução de Badges**: Todos os badges que representam estados ou categorias (ex: Status de processamento) devem ser traduzidos para português, exceto quando o conteúdo for um termo técnico mantido em inglês.
- **Mensagens de Validação**: Todas as mensagens de erro de formulário (ex: campos obrigatórios, formatos inválidos) devem seguir o padrão gramatical do Português do Brasil.
  - Ex: "The [field] field is required" -> "O campo [field] é obrigatório."

---

## 🛠️ O QUE IMPLEMENTAR (REQUISITOS)

### 1. Configuração do Framework (Laravel)
- Alterar o `locale` padrão no arquivo `config/app.php` para `pt_BR`.
- Configurar o `faker_locale` para `pt_BR`.

### 2. Componentes Nativos (Filament)
- Instalar ou configurar o pacote de idiomas do Filament para pt-BR.
- Garantir que paginação, busca, filtros e mensagens de validação nativas estejam traduzidas.

### 3. Recursos e Páginas Customizadas
- **Documentos**:
  - Labels de colunas (Filename -> Nome do Arquivo, Status -> Status, etc).
  - Botões de ação (Novo Documento, Editar, Excluir).
- **Chat**:
  - Interface de conversas (New Chat -> Nova Conversa, Send -> Enviar).
  - Placeholders de inputs.
- **Ajuda/Status**:
  - Tradução das verificações de conectividade do Ollama.

### 4. Menu de Navegação (Sidebar)
- Traduzir os nomes dos recursos no painel lateral.

---

## 🛠️ COMO IMPLEMENTAR (A TÉCNICA)

### 1. Publicação de Traduções do Filament
```bash
php artisan vendor:publish --tag=filament-panels-translations
```

### 2. Labels no Resource (Exemplo sênior)
Em vez de strings fixas, utilizaremos o método `label()` e `pluralLabel()` nos Resources:
```php
public static function getLabel(): string { return 'Documento'; }
public static function getPluralLabel(): string { return 'Documentos'; }
```

### 3. Arquivos de Idioma JSON
Criar/Atualizar `lang/pt_BR.json` para strings genéricas usadas em views Blade customizadas.

---

## ✅ CRITÉRIOS DE ACEITE
- [ ] Interface do painel administrativo 100% em português.
- [ ] Mensagens de erro e sucesso (Toasts) traduzidas.
- [ ] Datas e formatos numéricos seguindo o padrão brasileiro (DD/MM/AAAA).
- [ ] Breadcrumbs e títulos de página localizados.

---

## 📅 PRÓXIMOS PASSOS
1. Configurar `config/app.php`.
2. Traduzir o `DocumentResource` (Labels e Mensagens).
3. Traduzir as views customizadas de `Chats` e `Help`.
4. Validar se o Filament carregou os arquivos de `lang/vendor/filament`.
