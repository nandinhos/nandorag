# Design: Upload >100MB + Filas + Horizon + Bug Fix Chat

**Data:** 2026-04-07
**Branch:** `feature/upload-queue-refactor`
**Status:** Aprovado

---

## Contexto

O sistema RAG atual processa documentos de forma síncrona no ciclo de request HTTP, limitado a 10MB. O chat apresenta bug onde o texto da resposta não é capturado por uso incorreto da API de streaming do Laravel AI. Esta spec cobre a correção do bug e a refatoração completa do pipeline de upload e processamento de documentos.

---

## Problemas a Resolver

1. **Bug chat**: `$chunk->text` não existe no event object `TextDelta` — deve ser `$chunk->delta`
2. **Upload síncrono**: `EmbeddingService::processDocument()` é chamado em `afterCreate()` bloqueando a request HTTP
3. **Limite de 10MB**: `maxSize(10240)` no `FileUpload` do Filament
4. **Sem fila**: nenhum Job existe, `jobs` table criada mas nunca usada
5. **Sem progresso**: usuário não tem feedback do andamento do processamento
6. **Sem retry**: falhas exigem recriar o documento manualmente
7. **Sem prioridade**: todos os documentos processam na mesma ordem FIFO

---

## Arquitetura Geral

```
Browser
  │
  │ TUS Protocol (chunks de 5MB)
  ▼
POST /tus/upload  (TusController — autenticado)
  │  recebe chunks → monta arquivo → storage/app/private/documents/{uuid}.{ext}
  │
  ▼
[Job] ProcessDocumentJob           ← fila: documents
  │  extrai texto via DocumentImportService
  │  salva content + total_chunks no Document
  │  deleta arquivo temporário TUS
  │
  ▼
[Job] GenerateEmbeddingsJob        ← fila: embeddings
  │  processa chunks em batches de 10
  │  gera embedding via Ollama por batch
  │  atualiza processed_chunks e progress (%)
  │
  ▼
Document status = completed

Redis + Horizon
  ├── workers: documents   (1 processo, timeout 600s)
  ├── workers: embeddings  (2 processos, timeout 300s)
  └── workers: priority    (1 processo dedicado, timeout 600s)
       └── processa filas priority → documents → embeddings
```

---

## Componentes

### 1. Bug Fix — `app/Filament/Admin/Pages/Chats.php:256`

```php
// Antes (incorreto)
$fullResponse .= $chunk->text;

// Depois (correto)
if ($chunk instanceof \Laravel\Ai\Streaming\Events\TextDelta) {
    $fullResponse .= $chunk->delta;
}
```

### 2. TUS Upload Server

**Pacote:** `ankitpokhrel/tus-php`

**Rota:** `POST /tus/upload` — protegida por middleware `auth`

**TusController:** recebe o webhook de upload completo, cria registro `Document` com `status = pending` e despacha `ProcessDocumentJob` na fila `documents`.

**Configurações de servidor (VPS):**

```nginx
# Nginx
client_max_body_size 0;
```

```ini
; php.ini
upload_max_filesize = 500M
post_max_size = 0
max_execution_time = 0
```

### 3. Componente Frontend TUS

**Biblioteca:** `tus-js-client` (via npm/CDN)

**Componente Livewire:** `TusUploadComponent` — substitui o `FileUpload` nativo na `DocumentForm`.

Funcionalidades:
- Barra de progresso de upload em tempo real
- Exibe nome do arquivo e tamanho
- Suporte a pause/resume
- Retry automático em falha de rede (até 5 tentativas)
- Ao completar: dispara evento Livewire `uploadCompleted` com metadados do arquivo

### 4. Migrations — campos novos em `documents`

```php
$table->unsignedTinyInteger('progress')->default(0);
$table->unsignedInteger('processed_chunks')->default(0);
$table->unsignedInteger('total_chunks')->default(0);
$table->text('error_log')->nullable();
$table->timestamp('queued_at')->nullable();
```

### 5. Jobs

#### `ProcessDocumentJob`
- **Fila:** `documents` (ou `priority` se marcado)
- **Timeout:** 600s
- **Tries:** 3
- **Backoff:** `[60, 300, 600]`
- **Responsabilidades:**
  1. `status → processing`, registra `queued_at`
  2. Extrai texto via `DocumentImportService`
  3. Salva `content` e `total_chunks` no `Document`
  4. Deleta os chunks temporários do TUS (mantém o arquivo final em `storage/app/private/documents/`)
  5. Despacha `GenerateEmbeddingsJob`
- **Em falha:** salva stacktrace em `error_log`, `status → failed`

#### `GenerateEmbeddingsJob`
- **Fila:** `embeddings` (ou `priority` se marcado)
- **Timeout:** 300s por tentativa
- **Tries:** 3
- **Backoff:** `[30, 120, 300]`
- **Responsabilidades:**
  1. Carrega chunks do documento em batches de 10
  2. Para cada batch: gera embedding via Ollama, salva `DocumentChunk`
  3. Incrementa `processed_chunks` e recalcula `progress` após cada batch
  4. `status → completed` ao finalizar
- **Em falha:** salva stacktrace em `error_log`, `status → failed`

### 6. Horizon — `config/horizon.php`

```php
'environments' => [
    'production' => [
        'documents-supervisor' => [
            'queue'     => ['priority', 'documents'],
            'processes' => 1,
            'timeout'   => 600,
            'tries'     => 3,
        ],
        'embeddings-supervisor' => [
            'queue'     => ['priority', 'embeddings'],
            'processes' => 2,
            'timeout'   => 300,
            'tries'     => 3,
        ],
    ],
    'local' => [
        'local-supervisor' => [
            'queue'     => ['priority', 'documents', 'embeddings'],
            'processes' => 1,
            'timeout'   => 600,
        ],
    ],
],
```

### 7. Filament UI

#### Página de criação (`CreateDocument`)
- Substitui `FileUpload` nativo por `TusUploadComponent`
- Após upload concluído: cria `Document` e redireciona para a lista com notificação

#### Lista de documentos (`ListDocuments`)
- Polling a cada 3s em registros com `status = processing`
- Coluna de status com ícone + badge colorido
- Coluna de progresso: barra `processed_chunks / total_chunks` (visível em `processing`)
- Ações inline por status:
  - `failed` → botão **Retry** (deleta `DocumentChunks` existentes e re-despacha `ProcessDocumentJob`) + botão **Ver Erro** (modal)
  - `pending` / `failed` → botão **Priorizar** (atualiza fila para `priority`)
  - `completed` → nenhuma ação de reprocessamento

#### Modal de erro
- Timestamp da falha
- Stacktrace completo do campo `error_log`
- Botão Retry inline

---

## Fluxo de Dados Completo

```
1. Usuário seleciona arquivo (>100MB OK)
2. tus-js-client envia em chunks de 5MB → /tus/upload
3. TusController: monta arquivo → cria Document (pending) → despacha ProcessDocumentJob
4. Horizon/Worker: ProcessDocumentJob extrai texto → despacha GenerateEmbeddingsJob
5. Horizon/Worker: GenerateEmbeddingsJob processa batches → atualiza progress em tempo real
6. Filament polling: usuário vê barra de progresso atualizar a cada 3s
7. Ao completar: status = completed, barra some, documento disponível para chat
```

---

## Tratamento de Erros

| Cenário | Comportamento |
|---|---|
| Falha de rede no upload | tus-js-client retoma automaticamente (até 5x) |
| Timeout do job | Re-enfileira automaticamente (até 3x com backoff) |
| Ollama indisponível | Job falha após 3 tentativas → `status = failed`, erro salvo |
| PDF corrompido | Falha no `ProcessDocumentJob` → `status = failed`, erro salvo |
| Retry manual | Re-despacha `ProcessDocumentJob` do zero (limpa chunks existentes antes) |

---

## Testes

- `ProcessDocumentJobTest`: mock do `DocumentImportService`, verifica transições de status e despacho do `GenerateEmbeddingsJob`
- `GenerateEmbeddingsJobTest`: mock do `EmbeddingEngine`, verifica atualização de progresso e chunks criados
- `TusControllerTest`: simula webhook de upload completo, verifica criação do Document e despacho do job
- `ChatsStreamingTest`: verifica que o `TextDelta` é capturado corretamente e `fullResponse` não fica vazio

---

## Fora do Escopo

- Suporte a DOCX, XLSX, ou outros formatos além de PDF/TXT/MD
- Multi-tenant (documentos por usuário)
- Notificações por email ao completar processamento
- Cancelamento de processamento em andamento

---

## Dependências Novas

| Pacote | Uso |
|---|---|
| `ankitpokhrel/tus-php` | Servidor TUS (PHP) |
| `tus-js-client` | Cliente TUS (browser, via npm) |
| `laravel/horizon` | Dashboard e gestão de workers Redis |
