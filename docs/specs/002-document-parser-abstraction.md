# SPEC-002: Abstração de Extração de Documentos

## 1. Contexto Técnico
Atualmente, o `DocumentImportService` contém lógica de extração para PDF e Texto codificada diretamente em seus métodos privados. Para seguir o padrão de arquitetura limpa do NandoRAG, vamos mover essas implementações para classes especializadas (**Parsers**) que implementam a interface `DocumentParser`.

## 2. Mudanças de Arquitetura

### A. DocumentImportService (Orquestrador)
- **De**: Implementação direta de lógica de extração via `match($mimeType)`.
- **Para**: Um serviço que recebe uma coleção de `DocumentParser` via Injeção de Dependência e delega a extração ao parser que der "match" com o MimeType.

### B. Novos Componentes (Implementações)
1. **`App\Services\Parsers\PdfDocumentParser`**: Encapsula a lógica de extração de PDFs (usando `Spatie\PdfToText\Pdf`).
2. **`App\Services\Parsers\TextDocumentParser`**: Encapsula a lógica de arquivos de texto puro (.txt, .md).

## 3. Plano de Implementação (Fases)

### Fase 1: Criação dos Parsers
1. Criar diretório `app/Services/Parsers`.
2. Implementar `PdfDocumentParser` movendo a lógica atual do `extractFromPdf`.
3. Implementar `TextDocumentParser` movendo a lógica atual do `extractFromText`.

### Fase 2: Configuração de Bindings (Multi-binding)
1. No `AppServiceProvider`, registrar os parsers disponíveis para o `DocumentImportService`.
   ```php
   $this->app->when(DocumentImportService::class)
             ->needs('$parsers')
             ->give([
                 PdfDocumentParser::class,
                 TextDocumentParser::class,
             ]);
   ```

### Fase 3: Refatoração do DocumentImportService
1. Atualizar o construtor para receber `array $parsers`.
2. Implementar o método `extract` percorrendo os parsers e chamando `supports($mimeType)`.

## 4. Estratégia de Testes (TDD)
- **Unitários**: Testar cada parser individualmente com arquivos de exemplo (PDF e TXT).
- **Integração**: Validar que o `DocumentImportService` lança exceção ao receber um MimeType não suportado.

## 5. Arquivos Afetados
- `app/Services/DocumentImportService.php`
- `app/Providers/AppServiceProvider.php`
- (Novo) `app/Services/Parsers/PdfDocumentParser.php`
- (Novo) `app/Services/Parsers/TextDocumentParser.php`
