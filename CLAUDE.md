# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**NandoRAG** is a self-hosted RAG (Retrieval-Augmented Generation) tool for querying personal knowledge bases. Documents (PDF, MD, TXT) are imported, chunked, embedded via Ollama, and stored in PostgreSQL with pgvector. Users interact via a Filament 5 admin chat interface.

Stack: Laravel 13, Filament 5, Pest 4, PostgreSQL + pgvector, `laravel/ai`, Ollama (local, no cloud API).

## Commands

```bash
# Development server (runs Laravel, queue, logs, Vite concurrently)
composer dev

# Run all tests
composer test

# Run a single test file
php artisan test tests/Feature/ChunkingServiceTest.php

# Run a single test by name
php artisan test --filter "test name here"

# Code formatting
./vendor/bin/pint

# Migrations
php artisan migrate

# Seed admin user (nando@example.com / password)
php artisan db:seed
```

## Architecture

### RAG Pipeline

1. **Upload** → `DocumentImportService` extracts text from PDF/MD/TXT, tracking page/line metadata
2. **Chunk** → `ChunkingService` splits into overlapping fixed-size chunks (default 512 tokens, 50 overlap), recording `source_location` as `p.3-4` (PDF) or `L120-L180` (text)
3. **Embed** → `EmbeddingService` calls `laravel/ai` Embeddings API against Ollama `nomic-embed-text` (768 dims) and stores vectors in pgvector
4. **Retrieve** → `RagRetrievalService` embeds the query, runs `whereVectorSimilarTo` with cosine similarity ≥ 0.5, returns top 10 chunks
5. **Chat** → `ChatAgent` (implements `laravel/ai` `Agent + Conversational`) sends RAG context + conversation history to Ollama `llama3.2:3b`

### Filament Admin Panel (`/admin`)

| Page | Class | Purpose |
|---|---|---|
| Document Resource | `Filament/Admin/Resources/Documents/` | CRUD + upload + embedding trigger |
| Chats | `Filament/Admin/Pages/Chats.php` | Multi-tab AI chat with RAG |
| Help | `Filament/Admin/Pages/Help.php` | Ollama status + setup guide |

The Chats page uses Livewire computed properties (`#[Computed]`) for reactive chat/message lists. Streaming is handled synchronously (not SSE) — `$isStreaming` is a UI flag only.

### Key Models

- `Document` → has many `DocumentChunk` (via `document_id`)
- `Chat` → belongs to `User`, optionally scoped to `Document`; has many `ChatMessage`
- `DocumentChunk` → stores `embedding` as pgvector column; queried via `whereVectorSimilarTo` / `selectVectorDistance`

### Configuration

- `config/rag.php` — all RAG tunables (`chunk_size`, `chunk_overlap`, `similarity_threshold`, `top_k`, `embedding_model`, `embedding_dimensions`, `chat_model`). All overridable via `.env` with `RAG_*` prefix.
- `config/ai.php` — `laravel/ai` provider config; default provider is `ollama`, pointing to `OLLAMA_BASE_URL`.
- Uploaded documents are stored at `storage/app/documents/`.

## Environment Setup

Required `.env` additions beyond defaults:

```env
DB_CONNECTION=pgsql
DB_DATABASE=nandorag
DB_USERNAME=nandorag
DB_PASSWORD=secret

OLLAMA_BASE_URL=http://localhost:11434
```

Ollama must be running with models pulled:
```bash
ollama pull nomic-embed-text
ollama pull llama3.2:3b
```

## Testing

Tests use Pest 4. Feature tests mock Ollama calls — check existing tests like `EmbeddingServiceTest` and `ChatsPageTest` for mock patterns. The `DocumentImportService` tests use fixture files. Run with `--parallel` for speed.
