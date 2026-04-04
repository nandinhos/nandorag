# AGENTS.md

This file provides guidance for agentic coding agents operating in this repository.

## Project Overview

**NandoRAG** is a self-hosted RAG (Retrieval-Augmented Generation) tool for querying personal knowledge bases. Documents (PDF, MD, TXT) are imported, chunked, embedded via Ollama, and stored in PostgreSQL with pgvector. Users interact via a Filament 5 admin chat interface.

Stack: Laravel 13, Filament 5, Pest 4, PostgreSQL + pgvector, `laravel/ai`, Ollama (local, no cloud API).

---

## Commands

### Development

```bash
# Run all services concurrently (Laravel, queue, logs, Vite)
composer dev

# Setup project from scratch
composer setup
```

### Testing

```bash
# Run all tests
composer test
php artisan test

# Run single test file
php artisan test tests/Feature/ChunkingServiceTest.php

# Run tests matching name pattern
php artisan test --filter "test name here"

# Run tests in parallel (faster)
php artisan test --parallel
```

### Code Quality

```bash
# Format code (Laravel Pint)
./vendor/bin/pint
```

### Database

```bash
# Run migrations
php artisan migrate

# Seed admin user (nando@example.com / password)
php artisan db:seed
```

### Environment

```bash
# Required environment variables
DB_CONNECTION=pgsql
DB_DATABASE=nandorag
DB_USERNAME=nandorag
DB_PASSWORD=secret
OLLAMA_BASE_URL=http://localhost:11434
```

---

## Code Style Guidelines

### General

- Follow Laravel conventions and PSR-4 autoloading
- Use PHP 8.3+ syntax (typed properties, match expressions, etc.)
- Run `./vendor/bin/pint` before committing

### Naming Conventions

- **Classes**: `PascalCase` (e.g., `DocumentImportService`)
- **Methods/Properties**: `camelCase` (e.g., `getChunks()`, `$isStreaming`)
- **Constants**: `UPPER_SNAKE_CASE` (e.g., `DEFAULT_CHUNK_SIZE`)
- **Tables/Columns**: `snake_case` (e.g., `document_chunks`, `source_location`)

### Imports

- Use fully qualified class names in type hints
- Group imports: built-in first, then third-party, then local
- Sort alphabetically within groups

```php
use App\Services\ChunkingService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;
use function Illuminate\Support\collect;
```

### Types

- Always declare return types on methods unless dynamic
- Use nullable types with `?Type` syntax
- Prefer union types over mixed where applicable

### Error Handling

- Use Laravel's exception handling (`throw new \Exception`)
- Return typed responses in API contexts
- Log errors via `Log::error()` for debugging

### Controller/Service Patterns

- Controllers should be thin - delegate to services
- Use Form Requests for validation
- Use Service classes for business logic
- Return JSON/API responses via resource classes

### Database & Models

- Use Eloquent relationships (`belongsTo`, `hasMany`)
- Define scopes as methods (`public function scopeActive($query)`)
- Use migrations for schema changes
- Store vector embeddings in `vector` column (pgvector)

### Testing (Pest)

- Tests go in `tests/Feature/` (uses RefreshDatabase)
- Mock external services (Ollama calls) - see `EmbeddingServiceTest`
- Use fixture files for file parsing tests
- Follow Pest conventions with `test()` and `expect()`

```php
test('chunks text into overlapping pieces', function () {
    $service = new ChunkingService();
    $chunks = $service->chunk('Sample text here');
    expect($chunks)->toHaveCount(2);
});
```

### Filament

- Resources go in `Filament/Admin/Resources/`
- Pages go in `Filament/Admin/Pages/`
- Use Livewire components for complex behavior
- Use computed properties (`#[Computed]`) for reactive data

### Configuration

- RAG settings in `config/rag.php` (overridable via `RAG_*` env vars)
- AI provider config in `config/ai.php`
- Use `config('rag.chunk_size')` for tunable parameters

---

## Architecture Reference

### RAG Pipeline Flow

1. Upload → `DocumentImportService` (PDF/MD/TXT extraction)
2. Chunk → `ChunkingService` (fixed-size, overlapping chunks)
3. Embed → `EmbeddingService` (Ollama nomic-embed-text, 768 dims)
4. Retrieve → `RagRetrievalService` (cosine similarity ≥ 0.5, top 10)
5. Chat → `ChatAgent` (Ollama llama3.2:3b)

### Key Models

- `Document` → has many `DocumentChunk`
- `Chat` → belongs to `User`, has many `ChatMessage`
- `DocumentChunk` → stores `embedding` (pgvector)

---

## File Locations

- Documents: `storage/app/documents/`
- Tests: `tests/Feature/` and `tests/Unit/`
- Migrations: `database/migrations/`
- Seeders: `database/seeders/`
