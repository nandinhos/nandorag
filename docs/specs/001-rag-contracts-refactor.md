# SPEC-001: Inversão de Dependência no Pipeline RAG

## 1. Contexto Técnico
O sistema atual possui acoplamento forte entre a lógica de negócio e as implementações de IA (Ollama/laravel-ai). Para garantir extensibilidade, vamos refatorar os serviços core para dependerem de **Interfaces (Contracts)** em vez de classes concretas.

## 2. Mudanças de Arquitetura

### A. EmbeddingService
- **De**: Depende de `Laravel\Ai\Embeddings`.
- **Para**: Depender de `App\Contracts\EmbeddingEngine`.
- **Binding**: O `AppServiceProvider` fará o bind de `EmbeddingEngine` para uma nova classe `App\Services\Adapters\LaravelAiEmbeddingAdapter`.

### B. RagRetrievalService
- **De**: Depende de `Laravel\Ai\Embeddings`.
- **Para**: Depender de `App\Contracts\EmbeddingEngine`.

### C. DocumentImportService
- **De**: Implementação direta de `Spatie\PdfToText\Pdf`.
- **Para**: Implementar `App\Contracts\DocumentParser`.

## 3. Plano de Implementação (Fases)

### Fase 1: Criação de Adaptadores (Adapters)
1. Criar `App\Services\Adapters\OllamaEmbeddingAdapter` implementando `EmbeddingEngine`.
2. Criar `App\Services\Adapters\PdfDocumentParser` implementando `DocumentParser`.

### Fase 2: Configuração de Bindings
1. Adicionar no `AppServiceProvider::register()`:
   ```php
   $this->app->bind(EmbeddingEngine::class, OllamaEmbeddingAdapter::class);
   $this->app->bind(DocumentParser::class, PdfDocumentParser::class);
   ```

### Fase 3: Refatoração de Serviços
1. Atualizar o construtor do `EmbeddingService` para injetar `EmbeddingEngine` e `DocumentParser`.
2. Atualizar o construtor do `RagRetrievalService` para injetar `EmbeddingEngine`.

## 4. Estratégia de Testes (TDD)
- **Regressão**: Rodar `php artisan test` para garantir que o fluxo de upload -> chunk -> retrieve permanece funcional.
- **Novos Testes**: Criar unit tests para os novos Adaptadores isoladamente.

## 5. Arquivos Afetados
- `app/Services/EmbeddingService.php`
- `app/Services/RagRetrievalService.php`
- `app/Services/DocumentImportService.php`
- `app/Providers/AppServiceProvider.php`
- (Novos) `app/Services/Adapters/*`
