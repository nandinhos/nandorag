# Upload >100MB + Filas + Horizon + Bug Fix Chat — Plano de Implementação

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Substituir o processamento síncrono de documentos por um pipeline assíncrono com TUS (upload >100MB resumível), Redis+Horizon (filas/workers com dashboard) e gerenciamento avançado no Filament, além de corrigir o bug de streaming do chat.

**Architecture:** Upload via protocolo TUS (chunks de 5MB) → TusController cria Document e despacha ProcessDocumentJob (fila `documents`) → job extrai texto e cria DocumentChunks sem embedding → GenerateEmbeddingsJob (fila `embeddings`) gera embeddings em batches de 10 e atualiza `progress` → Filament exibe progresso via polling + ações Retry/Priorizar/Ver Erro.

**Tech Stack:** Laravel 12, Filament v3, Livewire 4, Laravel Horizon, Redis, `ankitpokhrel/tus-php`, `tus-js-client` (npm), Alpine.js, PostgreSQL + pgvector, Pest PHP

---

## Mapa de Arquivos

### Criados
| Arquivo | Responsabilidade |
|---|---|
| `app/Jobs/ProcessDocumentJob.php` | Extrai texto, chunka, cria DocumentChunks, despacha GenerateEmbeddingsJob |
| `app/Jobs/GenerateEmbeddingsJob.php` | Gera embeddings em batches, atualiza progress do Document |
| `app/Http/Controllers/TusController.php` | Lida com protocolo TUS, cria Document ao completar upload |
| `app/Livewire/TusUpload.php` | Componente Livewire que recebe evento de upload completo e redireciona |
| `resources/views/livewire/tus-upload.blade.php` | Template Alpine.js + tus-js-client |
| `database/migrations/XXXX_add_progress_fields_to_documents.php` | Adiciona progress, processed_chunks, total_chunks, error_log, queued_at |
| `database/factories/DocumentFactory.php` | Factory para testes |
| `tests/Feature/Jobs/ProcessDocumentJobTest.php` | Testa transições de status e despacho do GenerateEmbeddingsJob |
| `tests/Feature/Jobs/GenerateEmbeddingsJobTest.php` | Testa atualização de progresso e criação de embeddings |
| `tests/Feature/TusControllerTest.php` | Testa criação de Document via webhook TUS |

### Modificados
| Arquivo | O que muda |
|---|---|
| `app/Filament/Admin/Pages/Chats.php:256` | `$chunk->text` → `TextDelta` check + `$chunk->delta` |
| `app/Filament/Admin/Resources/Documents/Pages/CreateDocument.php` | Remove `afterCreate()` + integra `TusUpload` component |
| `app/Filament/Admin/Resources/Documents/Schemas/DocumentForm.php` | Remove `FileUpload`, mantém apenas metadados (edit) |
| `app/Filament/Admin/Resources/Documents/Tables/DocumentsTable.php` | Adiciona coluna de progresso + ações Retry/Priorizar/Ver Erro |
| `app/Filament/Admin/Resources/Documents/Pages/ListDocuments.php` | Adiciona polling |
| `app/Models/Document.php` | Adiciona novos fillable fields |
| `app/Providers/AppServiceProvider.php` | Bind TUS Server singleton + configuração |
| `routes/web.php` | Adiciona rota `/tus/upload` |
| `config/horizon.php` | Publicado e configurado com supervisores |

---

## Tarefa 1: Branch + Dependências

**Arquivos:** `composer.json`, `package.json`, `config/horizon.php`

- [ ] **Criar branch de feature**

```bash
git checkout -b feature/upload-queue-refactor
```

- [ ] **Instalar dependências PHP**

```bash
cd /home/nandodev/projects/nandorag
composer require ankitpokhrel/tus-php laravel/horizon
```

- [ ] **Instalar dependência JS**

```bash
npm install tus-js-client
```

- [ ] **Publicar Horizon**

```bash
php artisan horizon:install
```

Isso cria `config/horizon.php` e `resources/views/vendor/horizon/`.

- [ ] **Verificar que Horizon está instalado**

```bash
php artisan horizon --help
```

Deve exibir o help do comando horizon sem erros.

- [ ] **Commit inicial**

```bash
git add composer.json composer.lock package.json package-lock.json config/horizon.php resources/views/vendor/
git commit -m "feat: install laravel/horizon, tus-php, tus-js-client"
```

---

## Tarefa 2: Bug Fix — Chat Streaming (TextDelta)

**Arquivos:** `app/Filament/Admin/Pages/Chats.php`

- [ ] **Localizar a linha do bug**

```bash
grep -n "chunk->text" app/Filament/Admin/Pages/Chats.php
```

Deve retornar: `256:                $fullResponse .= $chunk->text;`

- [ ] **Corrigir o bug em `Chats.php`**

Substituir o bloco `foreach ($stream as $chunk)` (linhas 250-263):

```php
foreach ($stream as $chunk) {
    // Remove loading state on first chunk
    if ($this->isStreaming) {
        $this->isStreaming = false;
    }

    if ($chunk instanceof \Laravel\Ai\Streaming\Events\TextDelta) {
        $fullResponse .= $chunk->delta;
    }

    $this->stream(
        to: "chat-message-{$assistantMessage->id}",
        content: str($fullResponse)->markdown()->toHtml(),
        replace: true
    );
}
```

- [ ] **Verificar sintaxe**

```bash
php -l app/Filament/Admin/Pages/Chats.php
```

Esperado: `No syntax errors detected`

- [ ] **Testar manualmente no browser**: acessar o chat, enviar uma pergunta e verificar que o texto da resposta é exibido (não apenas as fontes).

- [ ] **Commit**

```bash
git add app/Filament/Admin/Pages/Chats.php
git commit -m "fix: capture TextDelta->delta for chat streaming response"
```

---

## Tarefa 3: Migration — Campos de Progresso no Document

**Arquivos:** nova migration

- [ ] **Criar migration**

```bash
php artisan make:migration add_progress_fields_to_documents_table
```

- [ ] **Editar a migration criada em `database/migrations/XXXX_add_progress_fields_to_documents_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedTinyInteger('progress')->default(0)->after('tags');
            $table->unsignedInteger('processed_chunks')->default(0)->after('progress');
            $table->unsignedInteger('total_chunks')->default(0)->after('processed_chunks');
            $table->text('error_log')->nullable()->after('total_chunks');
            $table->timestamp('queued_at')->nullable()->after('error_log');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['progress', 'processed_chunks', 'total_chunks', 'error_log', 'queued_at']);
        });
    }
};
```

- [ ] **Rodar migration**

```bash
php artisan migrate
```

Esperado: `Running migrations... DONE`

- [ ] **Verificar colunas no banco**

```bash
php artisan tinker --execute="echo implode(', ', array_keys((array) DB::selectOne('SELECT progress, processed_chunks, total_chunks, error_log, queued_at FROM documents LIMIT 1 OFFSET 999') ?? ['progress'=>null,'processed_chunks'=>null,'total_chunks'=>null,'error_log'=>null,'queued_at'=>null]));"
```

Esperado: `progress, processed_chunks, total_chunks, error_log, queued_at`

- [ ] **Commit**

```bash
git add database/migrations/
git commit -m "feat: add progress tracking fields to documents table"
```

---

## Tarefa 4: Document Model + Factory

**Arquivos:** `app/Models/Document.php`, `database/factories/DocumentFactory.php`

- [ ] **Atualizar `$fillable` e `$casts` em `app/Models/Document.php`**

```php
<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_filename',
        'mime_type',
        'content',
        'status',
        'file_path',
        'tags',
        'progress',
        'processed_chunks',
        'total_chunks',
        'error_log',
        'queued_at',
    ];

    protected function casts(): array
    {
        return [
            'status'     => DocumentStatus::class,
            'tags'       => 'array',
            'queued_at'  => 'datetime',
        ];
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(DocumentChunk::class);
    }
}
```

- [ ] **Criar `database/factories/DocumentFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'filename'          => $this->faker->word() . '.txt',
            'original_filename' => $this->faker->word() . '.txt',
            'mime_type'         => 'text/plain',
            'content'           => null,
            'status'            => DocumentStatus::Pending,
            'file_path'         => null,
            'tags'              => [],
            'progress'          => 0,
            'processed_chunks'  => 0,
            'total_chunks'      => 0,
            'error_log'         => null,
            'queued_at'         => null,
        ];
    }

    public function processing(): static
    {
        return $this->state(['status' => DocumentStatus::Processing]);
    }

    public function completed(): static
    {
        return $this->state([
            'status'           => DocumentStatus::Completed,
            'progress'         => 100,
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'status'    => DocumentStatus::Failed,
            'error_log' => 'RuntimeException: Something went wrong',
        ]);
    }
}
```

- [ ] **Verificar sintaxe**

```bash
php -l app/Models/Document.php
php -l database/factories/DocumentFactory.php
```

- [ ] **Commit**

```bash
git add app/Models/Document.php database/factories/DocumentFactory.php
git commit -m "feat: update Document model fillable and create DocumentFactory"
```

---

## Tarefa 5: ProcessDocumentJob

**Arquivos:** `app/Jobs/ProcessDocumentJob.php`, `tests/Feature/Jobs/ProcessDocumentJobTest.php`

- [ ] **Escrever o teste em `tests/Feature/Jobs/ProcessDocumentJobTest.php`**

```php
<?php

use App\Enums\DocumentStatus;
use App\Jobs\GenerateEmbeddingsJob;
use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use App\Models\DocumentChunk;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

test('sets document to processing and dispatches GenerateEmbeddingsJob on success', function () {
    Bus::fake([GenerateEmbeddingsJob::class]);
    Storage::fake('local');

    $document = Document::factory()->create([
        'file_path' => 'documents/test.txt',
        'mime_type'  => 'text/plain',
    ]);

    Storage::disk('local')->put('documents/test.txt', str_repeat('Word ', 200));

    (new ProcessDocumentJob($document->id))->handle(
        app(\App\Services\DocumentImportService::class),
        app(\App\Services\ChunkingService::class),
    );

    $document->refresh();

    expect($document->status)->toBe(DocumentStatus::Processing);
    expect($document->content)->not->toBeEmpty();
    expect($document->total_chunks)->toBeGreaterThan(0);
    expect(DocumentChunk::where('document_id', $document->id)->whereNull('embedding')->count())
        ->toBe($document->total_chunks);

    Bus::assertDispatched(GenerateEmbeddingsJob::class, fn ($job) => $job->documentId === $document->id);
});

test('sets document to failed and saves error_log on exception', function () {
    Storage::fake('local');

    $document = Document::factory()->create([
        'file_path' => 'documents/missing.txt',
        'mime_type'  => 'text/plain',
    ]);

    // File does not exist — will throw

    expect(fn () => (new ProcessDocumentJob($document->id))->handle(
        app(\App\Services\DocumentImportService::class),
        app(\App\Services\ChunkingService::class),
    ))->toThrow(\Throwable::class);

    $document->refresh();

    expect($document->status)->toBe(DocumentStatus::Failed);
    expect($document->error_log)->not->toBeNull();
});
```

- [ ] **Rodar o teste para confirmar que falha (RED)**

```bash
php artisan test tests/Feature/Jobs/ProcessDocumentJobTest.php --no-coverage
```

Esperado: FAIL — `ProcessDocumentJob not found`

- [ ] **Criar `app/Jobs/ProcessDocumentJob.php`**

```php
<?php

namespace App\Jobs;

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentChunk;
use App\Services\ChunkingService;
use App\Services\DocumentImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessDocumentJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $timeout = 600;
    public int $tries = 3;
    public array $backoff = [60, 300, 600];

    public function __construct(public readonly int $documentId) {}

    public function handle(DocumentImportService $importService, ChunkingService $chunkingService): void
    {
        $document = Document::findOrFail($this->documentId);

        $document->update([
            'status'    => DocumentStatus::Processing,
            'queued_at' => now(),
        ]);

        try {
            $filePath = Storage::disk('local')->path($document->file_path);

            $result = $importService->extract($filePath, $document->mime_type);

            $chunks = $chunkingService->chunk(
                $result['text'],
                $document->original_filename,
                $result['pages'],
            );

            // Delete any previous chunks (retry scenario)
            $document->chunks()->delete();

            foreach ($chunks as $chunkData) {
                DocumentChunk::create([
                    'document_id'     => $document->id,
                    'content'         => $chunkData['content'],
                    'chunk_index'     => $chunkData['chunk_index'],
                    'token_count'     => $chunkData['token_count'],
                    'source_file'     => $chunkData['source_file'],
                    'source_location' => $chunkData['source_location'],
                    'embedding'       => null,
                ]);
            }

            $document->update([
                'content'          => $result['text'],
                'total_chunks'     => count($chunks),
                'processed_chunks' => 0,
                'progress'         => 0,
                'error_log'        => null,
            ]);

            GenerateEmbeddingsJob::dispatch($document->id)->onQueue(
                $this->queue === 'priority' ? 'priority' : 'embeddings'
            );

        } catch (\Throwable $e) {
            Log::error("ProcessDocumentJob failed for #{$document->id}: {$e->getMessage()}");

            $document->update([
                'status'    => DocumentStatus::Failed,
                'error_log' => $e->getMessage() . "\n" . $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
```

- [ ] **Rodar o teste (GREEN)**

```bash
php artisan test tests/Feature/Jobs/ProcessDocumentJobTest.php --no-coverage
```

Esperado: `2 passed`

- [ ] **Commit**

```bash
git add app/Jobs/ProcessDocumentJob.php tests/Feature/Jobs/ProcessDocumentJobTest.php
git commit -m "feat: add ProcessDocumentJob with retry and error tracking"
```

---

## Tarefa 6: GenerateEmbeddingsJob

**Arquivos:** `app/Jobs/GenerateEmbeddingsJob.php`, `tests/Feature/Jobs/GenerateEmbeddingsJobTest.php`

- [ ] **Escrever o teste em `tests/Feature/Jobs/GenerateEmbeddingsJobTest.php`**

```php
<?php

use App\Enums\DocumentStatus;
use App\Jobs\GenerateEmbeddingsJob;
use App\Models\Document;
use App\Models\DocumentChunk;
use Laravel\Ai\Embeddings;

test('generates embeddings for all chunks and completes document', function () {
    Embeddings::fake(fn () => [array_fill(0, 768, 0.1)]);

    $document = Document::factory()->processing()->create([
        'total_chunks'     => 3,
        'processed_chunks' => 0,
        'progress'         => 0,
    ]);

    DocumentChunk::factory()->count(3)->create([
        'document_id' => $document->id,
        'embedding'   => null,
    ]);

    (new GenerateEmbeddingsJob($document->id))->handle(
        app(\App\Services\EmbeddingService::class),
    );

    $document->refresh();

    expect($document->status)->toBe(DocumentStatus::Completed);
    expect($document->progress)->toBe(100);
    expect($document->processed_chunks)->toBe(3);
    expect(DocumentChunk::where('document_id', $document->id)->whereNull('embedding')->count())->toBe(0);
});

test('sets document to failed and saves error_log when embedding fails', function () {
    Embeddings::fake(fn () => throw new RuntimeException('Ollama unavailable'));

    $document = Document::factory()->processing()->create([
        'total_chunks' => 1,
    ]);

    DocumentChunk::factory()->create([
        'document_id' => $document->id,
        'embedding'   => null,
    ]);

    expect(fn () => (new GenerateEmbeddingsJob($document->id))->handle(
        app(\App\Services\EmbeddingService::class),
    ))->toThrow(\Throwable::class);

    $document->refresh();

    expect($document->status)->toBe(DocumentStatus::Failed);
    expect($document->error_log)->not->toBeNull();
});
```

- [ ] **Criar factory para DocumentChunk** — adicione ao final de `database/factories/DocumentChunkFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentChunkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'document_id'     => Document::factory(),
            'content'         => $this->faker->paragraph(),
            'chunk_index'     => $this->faker->numberBetween(0, 100),
            'token_count'     => $this->faker->numberBetween(50, 512),
            'source_file'     => 'test.txt',
            'source_location' => 'p1',
            'embedding'       => null,
        ];
    }
}
```

- [ ] **Adicionar `use HasFactory` ao modelo `DocumentChunk`** em `app/Models/DocumentChunk.php`:

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentChunk extends Model
{
    use HasFactory;
    // ... resto existente
```

- [ ] **Rodar os testes para confirmar que falham (RED)**

```bash
php artisan test tests/Feature/Jobs/GenerateEmbeddingsJobTest.php --no-coverage
```

Esperado: FAIL — `GenerateEmbeddingsJob not found`

- [ ] **Criar `app/Jobs/GenerateEmbeddingsJob.php`**

```php
<?php

namespace App\Jobs;

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentChunk;
use App\Services\EmbeddingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateEmbeddingsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $timeout = 300;
    public int $tries = 3;
    public array $backoff = [30, 120, 300];

    private const BATCH_SIZE = 10;

    public function __construct(public readonly int $documentId) {}

    public function handle(EmbeddingService $embeddingService): void
    {
        $document = Document::findOrFail($this->documentId);

        try {
            $chunks = DocumentChunk::where('document_id', $document->id)
                ->whereNull('embedding')
                ->orderBy('chunk_index')
                ->get();

            $total = $document->total_chunks ?: $chunks->count();
            $processed = $document->processed_chunks;

            foreach ($chunks->chunk(self::BATCH_SIZE) as $batch) {
                foreach ($batch as $chunk) {
                    $chunkText = mb_convert_encoding($chunk->content, 'UTF-8', 'UTF-8');
                    $chunkText = preg_replace('/[\x00-\x1F\x7F]/u', '', $chunkText);

                    if (strlen($chunkText) > 2000) {
                        $chunkText = substr($chunkText, 0, 2000);
                    }

                    $embedding = $embeddingService->generateEmbedding($chunkText);

                    $chunk->update(['embedding' => $embedding]);
                    $processed++;
                }

                $progress = $total > 0 ? (int) round($processed / $total * 100) : 100;

                $document->update([
                    'processed_chunks' => $processed,
                    'progress'         => $progress,
                ]);
            }

            $document->update([
                'status'   => DocumentStatus::Completed,
                'progress' => 100,
            ]);

        } catch (\Throwable $e) {
            Log::error("GenerateEmbeddingsJob failed for #{$document->id}: {$e->getMessage()}");

            $document->update([
                'status'    => DocumentStatus::Failed,
                'error_log' => $e->getMessage() . "\n" . $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
```

- [ ] **Rodar os testes (GREEN)**

```bash
php artisan test tests/Feature/Jobs/GenerateEmbeddingsJobTest.php --no-coverage
```

Esperado: `2 passed`

- [ ] **Rodar todos os testes existentes para garantir nada quebrou**

```bash
php artisan test --no-coverage
```

Esperado: todos passando.

- [ ] **Commit**

```bash
git add app/Jobs/GenerateEmbeddingsJob.php \
        app/Models/DocumentChunk.php \
        database/factories/DocumentChunkFactory.php \
        database/factories/DocumentFactory.php \
        tests/Feature/Jobs/GenerateEmbeddingsJobTest.php
git commit -m "feat: add GenerateEmbeddingsJob with batch processing and progress tracking"
```

---

## Tarefa 7: Configurar Horizon

**Arquivos:** `config/horizon.php`

- [ ] **Editar `config/horizon.php`** — substituir a seção `environments`:

```php
'environments' => [
    'production' => [
        'documents-supervisor' => [
            'connection' => 'redis',
            'queue'      => ['priority', 'documents'],
            'balance'    => 'simple',
            'processes'  => 1,
            'timeout'    => 600,
            'tries'      => 3,
            'minProcesses' => 1,
            'maxProcesses' => 1,
        ],
        'embeddings-supervisor' => [
            'connection' => 'redis',
            'queue'      => ['priority', 'embeddings'],
            'balance'    => 'auto',
            'processes'  => 2,
            'timeout'    => 300,
            'tries'      => 3,
            'minProcesses' => 1,
            'maxProcesses' => 4,
        ],
    ],

    'local' => [
        'local-supervisor' => [
            'connection' => 'redis',
            'queue'      => ['priority', 'documents', 'embeddings'],
            'balance'    => 'simple',
            'processes'  => 1,
            'timeout'    => 600,
            'tries'      => 3,
        ],
    ],
],
```

- [ ] **Verificar que `QUEUE_CONNECTION=redis` está no `.env`**

```bash
grep QUEUE_CONNECTION .env
```

Se retornar `database`, trocar para `redis`:

```bash
sed -i 's/QUEUE_CONNECTION=database/QUEUE_CONNECTION=redis/' .env
```

- [ ] **Testar que o Horizon inicia sem erros**

```bash
php artisan horizon:status
```

Esperado: `Horizon is inactive.` (ainda não rodando — isso é correto)

- [ ] **Criar arquivo de configuração do Supervisor para a VPS** em `docs/supervisor-horizon.conf`:

```ini
[program:horizon]
process_name=%(program_name)s
command=php /var/www/nandorag/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/nandorag/storage/logs/horizon.log
stopwaitsecs=3600
```

> **Instruções para a VPS:** copiar para `/etc/supervisor/conf.d/horizon.conf`, depois:
> ```bash
> supervisorctl reread && supervisorctl update && supervisorctl start horizon
> ```

- [ ] **Commit**

```bash
git add config/horizon.php docs/supervisor-horizon.conf
git commit -m "feat: configure Horizon with documents and embeddings supervisors"
```

---

## Tarefa 8: TUS Server — Controller e Rota

**Arquivos:** `app/Http/Controllers/TusController.php`, `app/Providers/AppServiceProvider.php`, `routes/web.php`, `tests/Feature/TusControllerTest.php`

- [ ] **Escrever o teste em `tests/Feature/TusControllerTest.php`**

```php
<?php

use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

test('GET /tus/upload returns 204 (TUS discovery)', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/tus/upload', ['Tus-Resumable' => '1.0.0'])
        ->assertStatus(204);
});

test('POST /tus/upload returns 201 with Location header', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/tus/upload', [], [
        'Tus-Resumable'  => '1.0.0',
        'Upload-Length'  => '1024',
        'Upload-Metadata' => 'filename dGVzdC50eHQ=,filetype dGV4dC9wbGFpbg==',
    ]);

    $response->assertStatus(201);
    $response->assertHeader('Location');
});
```

> **Nota:** O teste de upload completo (PATCH) é complexo de simular sem um cliente TUS real. Esses dois testes cobrem o handshake inicial. Teste completo do fluxo de criação de Document é feito manualmente.

- [ ] **Rodar testes para confirmar que falham (RED)**

```bash
php artisan test tests/Feature/TusControllerTest.php --no-coverage
```

Esperado: FAIL — rota não existe

- [ ] **Registrar o TUS Server no `AppServiceProvider`** — adicionar em `register()`:

```php
public function register(): void
{
    $this->app->singleton(\TusPhp\Tus\Server::class, function () {
        $server = new \TusPhp\Tus\Server('redis');

        $server->setApiPath('/tus/upload');
        $server->setUploadDir(storage_path('app/private/tus-temp'));
        $server->setMaxUploadSize(500 * 1024 * 1024); // 500MB

        return $server;
    });
}
```

- [ ] **Criar `app/Http/Controllers/TusController.php`**

```php
<?php

namespace App\Http\Controllers;

use App\Enums\DocumentStatus;
use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use TusPhp\Events\TusEvent;
use TusPhp\Tus\Server;

class TusController extends Controller
{
    public function handle(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $server = app(Server::class);

        $server->event()->addListener(
            TusEvent::UPLOAD_COMPLETE,
            function (TusEvent $event) use ($request) {
                $this->onUploadComplete($event, $request);
            }
        );

        return $server->serve();
    }

    private function onUploadComplete(TusEvent $event, Request $request): void
    {
        $file = $event->getFile();
        $originalName = $file->getName();
        $extension    = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $uuid         = (string) Str::uuid();
        $storedPath   = "documents/{$uuid}.{$extension}";

        try {
            // Mover do diretório TUS temporário para local permanente
            // getKey() retorna o upload key do TUS; o arquivo fica em tus-temp/{key}
            $tusRelativePath = 'private/tus-temp/' . $file->getKey();

            if (!Storage::disk('local')->exists($tusRelativePath)) {
                // Fallback: tus-php pode nomear o arquivo de outra forma
                // Verificar: storage_path('app/' . $tusRelativePath)
                throw new \RuntimeException("TUS temp file not found at: {$tusRelativePath}");
            }

            Storage::disk('local')->move($tusRelativePath, $storedPath);
        } catch (\Throwable $e) {
            Log::error("TUS: failed to move file {$originalName}: {$e->getMessage()}");
            return;
        }

        $mimeType = match ($extension) {
            'pdf'            => 'application/pdf',
            'md', 'markdown' => 'text/markdown',
            default          => 'text/plain',
        };

        $document = Document::create([
            'filename'          => $originalName,
            'original_filename' => $originalName,
            'mime_type'         => $mimeType,
            'file_path'         => $storedPath,
            'tags'              => [$originalName],
            'status'            => DocumentStatus::Pending,
            'queued_at'         => now(),
        ]);

        ProcessDocumentJob::dispatch($document->id)->onQueue('documents');

        Log::info("TUS: Document #{$document->id} created and queued for processing.");
    }
}
```

- [ ] **Adicionar rota TUS em `routes/web.php`**

Adicionar antes do fechamento do arquivo:

```php
use App\Http\Controllers\TusController;

// TUS upload endpoint (suporta GET, HEAD, POST, PATCH, DELETE)
Route::middleware(['web', 'auth'])->group(function () {
    Route::any('/tus/upload{path?}', [TusController::class, 'handle'])
        ->where('path', '.*')
        ->name('tus.upload');
});
```

- [ ] **Criar diretório temporário TUS**

```bash
mkdir -p storage/app/private/tus-temp
touch storage/app/private/tus-temp/.gitkeep
```

- [ ] **Rodar os testes (GREEN)**

```bash
php artisan test tests/Feature/TusControllerTest.php --no-coverage
```

Esperado: `2 passed`

- [ ] **Commit**

```bash
git add app/Http/Controllers/TusController.php \
        app/Providers/AppServiceProvider.php \
        routes/web.php \
        storage/app/private/tus-temp/.gitkeep \
        tests/Feature/TusControllerTest.php
git commit -m "feat: add TUS upload server with Document creation on complete"
```

---

## Tarefa 9: Componente Livewire TUS — Frontend

**Arquivos:** `app/Livewire/TusUpload.php`, `resources/views/livewire/tus-upload.blade.php`

- [ ] **Criar `app/Livewire/TusUpload.php`**

```php
<?php

namespace App\Livewire;

use App\Filament\Admin\Resources\Documents\Pages\ListDocuments;
use Livewire\Component;

class TusUpload extends Component
{
    public bool $uploadComplete = false;
    public string $uploadedFilename = '';

    public function uploadCompleted(string $filename): void
    {
        $this->uploadComplete = true;
        $this->uploadedFilename = $filename;

        // Redireciona para a lista após 1.5s (o JS controla o delay)
        $this->redirectRoute('filament.admin.resources.documents.index');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.tus-upload');
    }
}
```

- [ ] **Criar `resources/views/livewire/tus-upload.blade.php`**

```blade
<div
    x-data="tusUploader(@js(route('tus.upload')))"
    x-init="init()"
    class="space-y-4"
>
    {{-- Seletor de arquivo --}}
    <div x-show="!uploading && !done">
        <label class="block text-sm font-heading font-semibold text-gray-700 mb-2">
            Selecionar Arquivo (PDF, TXT, MD — até 500MB)
        </label>
        <input
            type="file"
            accept=".pdf,.txt,.md,.markdown"
            @change="selectFile($event)"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border file:border-black file:rounded file:bg-yellow-400 file:font-heading file:font-semibold file:cursor-pointer"
        />
    </div>

    {{-- Nome do arquivo selecionado + tamanho --}}
    <div x-show="file && !uploading && !done" class="text-sm text-gray-600">
        <span x-text="file ? file.name + ' (' + formatSize(file.size) + ')' : ''"></span>
    </div>

    {{-- Botão de upload --}}
    <button
        x-show="file && !uploading && !done"
        @click="startUpload()"
        type="button"
        class="px-4 py-2 bg-black text-white font-heading font-semibold border border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all"
    >
        Enviar Arquivo
    </button>

    {{-- Barra de progresso --}}
    <div x-show="uploading" class="space-y-2">
        <div class="flex justify-between text-sm font-heading">
            <span x-text="'Enviando: ' + filename"></span>
            <span x-text="progress + '%'"></span>
        </div>
        <div class="w-full bg-gray-200 border border-black h-4">
            <div
                class="bg-yellow-400 h-full transition-all duration-300"
                :style="'width: ' + progress + '%'"
            ></div>
        </div>
        <div class="text-xs text-gray-500" x-text="formatSize(uploadedBytes) + ' de ' + formatSize(totalBytes)"></div>

        <button
            @click="pauseResume()"
            type="button"
            class="text-xs underline text-gray-600 hover:text-black"
            x-text="paused ? 'Retomar' : 'Pausar'"
        ></button>
    </div>

    {{-- Sucesso --}}
    <div x-show="done" class="flex items-center gap-2 text-green-700 font-heading font-semibold">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>Upload concluído! Redirecionando...</span>
    </div>

    {{-- Erro --}}
    <div x-show="error" class="text-red-600 text-sm font-heading" x-text="error"></div>
</div>

@script
<script>
function tusUploader(endpoint) {
    return {
        file: null,
        filename: '',
        progress: 0,
        uploading: false,
        paused: false,
        done: false,
        error: null,
        uploadedBytes: 0,
        totalBytes: 0,
        tusUpload: null,

        init() {
            // tus-js-client é carregado via npm/vite
        },

        selectFile(event) {
            this.file = event.target.files[0] ?? null;
            this.filename = this.file?.name ?? '';
            this.error = null;
        },

        startUpload() {
            if (!this.file) return;

            this.uploading = true;
            this.error = null;

            this.tusUpload = new tus.Upload(this.file, {
                endpoint: endpoint,
                retryDelays: [0, 3000, 5000, 10000, 20000],
                chunkSize: 5 * 1024 * 1024,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                metadata: {
                    filename: this.file.name,
                    filetype: this.file.type,
                },
                onError: (err) => {
                    this.error = 'Erro no upload: ' + err.message;
                    this.uploading = false;
                },
                onProgress: (bytesUploaded, bytesTotal) => {
                    this.uploadedBytes = bytesUploaded;
                    this.totalBytes = bytesTotal;
                    this.progress = Math.round(bytesUploaded / bytesTotal * 100);
                },
                onSuccess: () => {
                    this.progress = 100;
                    this.uploading = false;
                    this.done = true;
                    $wire.uploadCompleted(this.filename);
                },
            });

            this.tusUpload.start();
        },

        pauseResume() {
            if (this.paused) {
                this.tusUpload.start();
                this.paused = false;
            } else {
                this.tusUpload.abort();
                this.paused = true;
            }
        },

        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        },
    }
}
</script>
@endscript
```

- [ ] **Registrar o componente Livewire** em `app/Providers/AppServiceProvider.php`, no método `boot()`:

```php
use Livewire\Livewire;

public function boot(): void
{
    Livewire::component('tus-upload', \App\Livewire\TusUpload::class);
}
```

- [ ] **Importar tus-js-client no `resources/js/app.js`** (ou equivalente):

```js
import * as tus from 'tus-js-client';
window.tus = tus;
```

- [ ] **Buildar assets**

```bash
npm run build
```

Esperado: sem erros.

- [ ] **Commit**

```bash
git add app/Livewire/TusUpload.php \
        resources/views/livewire/tus-upload.blade.php \
        app/Providers/AppServiceProvider.php \
        resources/js/app.js
git commit -m "feat: add TUS Livewire component with Alpine.js progress UI"
```

---

## Tarefa 10: Atualizar CreateDocument — Remover Processamento Síncrono

**Arquivos:** `app/Filament/Admin/Resources/Documents/Pages/CreateDocument.php`, `app/Filament/Admin/Resources/Documents/Schemas/DocumentForm.php`

- [ ] **Substituir `app/Filament/Admin/Resources/Documents/Pages/CreateDocument.php`**

```php
<?php

namespace App\Filament\Admin\Resources\Documents\Pages;

use App\Filament\Admin\Resources\Documents\DocumentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CreateDocument extends Page
{
    protected static string $resource = DocumentResource::class;

    protected static string $view = 'filament.resources.documents.pages.create-document';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Voltar')
                ->color('purple')
                ->icon(Heroicon::ChevronLeft)
                ->url(url()->previous())
                ->extraAttributes(['class' => 'neo-border-sm shadow-neo-xs font-heading']),
        ];
    }
}
```

- [ ] **Criar o template da página** em `resources/views/filament/resources/documents/pages/create-document.blade.php`:

```blade
<x-filament-panels::page>
    <div class="max-w-2xl">
        <x-filament::section>
            <x-slot name="heading">Upload de Documento</x-slot>
            <x-slot name="description">
                Selecione um arquivo PDF, TXT ou Markdown. Após o upload, o processamento ocorre em segundo plano.
            </x-slot>

            <livewire:tus-upload />
        </x-filament::section>
    </div>
</x-filament-panels::page>
```

- [ ] **Remover o `FileUpload` do formulário de criação em `DocumentForm.php`** — o `Section 'Upload'` só deve aparecer no edit (para mostrar o nome do arquivo). Substituir a Section Upload:

```php
Section::make('Upload')
    ->label('Arquivo')
    ->columnSpan(['default' => 1, 'md' => 2])
    ->schema([
        TextInput::make('filename')
            ->label('Nome do Arquivo')
            ->required()
            ->disabled()
            ->columnSpanFull(),
    ])
    ->visibleOn('edit'),
```

- [ ] **Verificar sintaxe**

```bash
php -l app/Filament/Admin/Resources/Documents/Pages/CreateDocument.php
php -l app/Filament/Admin/Resources/Documents/Schemas/DocumentForm.php
```

- [ ] **Verificar que o DocumentResource ainda registra CreateDocument corretamente** — em `app/Filament/Admin/Resources/Documents/DocumentResource.php`, confirmar que `getPages()` contém:

```php
'create' => Pages\CreateDocument::route('/create'),
```

Se não estiver, adicionar. A substituição de `CreateRecord` por `Page` mantém o mesmo nome de classe, mas o Filament precisa que a rota esteja registrada.

- [ ] **Verificar visualmente no browser**: acessar `/admin/documents/create` — deve exibir o componente TUS, não o FileUpload nativo.

- [ ] **Commit**

```bash
git add app/Filament/Admin/Resources/Documents/Pages/CreateDocument.php \
        app/Filament/Admin/Resources/Documents/Schemas/DocumentForm.php \
        resources/views/filament/resources/documents/pages/create-document.blade.php
git commit -m "feat: replace sync FileUpload with TUS component in CreateDocument"
```

---

## Tarefa 11: Atualizar DocumentsTable — Progresso + Ações

**Arquivos:** `app/Filament/Admin/Resources/Documents/Tables/DocumentsTable.php`

- [ ] **Substituir `DocumentsTable.php`** com as novas colunas e ações:

```php
<?php

namespace App\Filament\Admin\Resources\Documents\Tables;

use App\Enums\DocumentStatus;
use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('extension')
                    ->state(fn ($record) => strtoupper(pathinfo($record->filename, PATHINFO_EXTENSION) ?: 'DOC'))
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'PDF' => 'danger',
                        'MD'  => 'info',
                        'TXT' => 'warning',
                        default => 'gray',
                    })
                    ->label('EXT'),

                TextColumn::make('filename')
                    ->label('Nome do Arquivo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->extraAttributes(['class' => 'font-heading uppercase tracking-wider']),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                // Coluna de progresso — visível apenas em processing
                TextColumn::make('progress')
                    ->label('Progresso')
                    ->state(fn (Document $record): string => $record->status === DocumentStatus::Processing
                        ? "{$record->processed_chunks}/{$record->total_chunks} chunks ({$record->progress}%)"
                        : ($record->status === DocumentStatus::Completed ? '100%' : '—')
                    )
                    ->color(fn (Document $record) => match ($record->status) {
                        DocumentStatus::Processing => 'warning',
                        DocumentStatus::Completed  => 'success',
                        default                    => 'gray',
                    })
                    ->size('xs'),

                TextColumn::make('chunks_count')
                    ->counts('chunks')
                    ->label('Chunks')
                    ->badge()
                    ->color('gray')
                    ->size('xs')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Data de Upload')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->size('xs')
                    ->color('gray')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filtrar por Status')
                    ->options(DocumentStatus::class),
            ])
            ->actions([
                EditAction::make()
                    ->label('Editar')
                    ->button()
                    ->color('warning')
                    ->size('xs')
                    ->extraAttributes(['class' => 'neo-border-sm shadow-neo-xs font-heading']),

                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->size('xs')
                    ->button()
                    ->visible(fn (Document $record) => $record->status === DocumentStatus::Failed)
                    ->requiresConfirmation()
                    ->modalHeading('Re-processar documento?')
                    ->modalDescription('Os chunks existentes serão deletados e o documento será re-processado do zero.')
                    ->action(function (Document $record) {
                        $record->update([
                            'status'           => DocumentStatus::Pending,
                            'progress'         => 0,
                            'processed_chunks' => 0,
                            'error_log'        => null,
                            'queued_at'        => now(),
                        ]);
                        ProcessDocumentJob::dispatch($record->id)->onQueue('documents');
                        Notification::make()->title('Re-processamento agendado')->success()->send();
                    })
                    ->extraAttributes(['class' => 'neo-border-sm shadow-neo-xs font-heading']),

                Action::make('prioritize')
                    ->label('Priorizar')
                    ->icon('heroicon-o-bolt')
                    ->color('warning')
                    ->size('xs')
                    ->button()
                    ->visible(fn (Document $record) => in_array($record->status, [
                        DocumentStatus::Pending,
                        DocumentStatus::Failed,
                    ]))
                    ->action(function (Document $record) {
                        $record->update([
                            'status'    => DocumentStatus::Pending,
                            'error_log' => null,
                            'queued_at' => now(),
                        ]);
                        ProcessDocumentJob::dispatch($record->id)->onQueue('priority');
                        Notification::make()->title('Documento priorizado')->success()->send();
                    })
                    ->extraAttributes(['class' => 'neo-border-sm shadow-neo-xs font-heading']),

                Action::make('view_error')
                    ->label('Ver Erro')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->size('xs')
                    ->button()
                    ->visible(fn (Document $record) => $record->status === DocumentStatus::Failed && $record->error_log)
                    ->modalHeading(fn (Document $record) => "Erro — {$record->filename}")
                    ->modalContent(fn (Document $record) => view('filament.modals.document-error', ['document' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->extraAttributes(['class' => 'neo-border-sm shadow-neo-xs font-heading']),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                ])->label('Ações em Massa'),
            ]);
    }
}
```

- [ ] **Criar o template do modal de erro** em `resources/views/filament/modals/document-error.blade.php`:

```blade
<div class="space-y-3 p-4">
    @if($document->queued_at)
        <p class="text-xs text-gray-500 font-heading">
            Falhou em: {{ $document->updated_at->format('d/m/Y H:i:s') }}
        </p>
    @endif
    <div class="bg-red-50 border border-red-300 rounded p-3 overflow-auto max-h-96">
        <pre class="text-xs text-red-800 whitespace-pre-wrap font-mono">{{ $document->error_log }}</pre>
    </div>
</div>
```

- [ ] **Verificar sintaxe**

```bash
php -l app/Filament/Admin/Resources/Documents/Tables/DocumentsTable.php
```

- [ ] **Commit**

```bash
git add app/Filament/Admin/Resources/Documents/Tables/DocumentsTable.php \
        resources/views/filament/modals/document-error.blade.php
git commit -m "feat: add progress column and retry/prioritize/error actions to DocumentsTable"
```

---

## Tarefa 12: Polling na ListDocuments

**Arquivos:** `app/Filament/Admin/Resources/Documents/Pages/ListDocuments.php`

- [ ] **Atualizar `ListDocuments.php`** para adicionar polling automático enquanto há documentos em processamento:

```php
<?php

namespace App\Filament\Admin\Resources\Documents\Pages;

use App\Enums\DocumentStatus;
use App\Filament\Admin\Resources\Documents\DocumentResource;
use App\Models\Document;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    // Polling a cada 3 segundos quando há documentos processando
    public function getPollingInterval(): ?string
    {
        $hasProcessing = Document::whereIn('status', [
            DocumentStatus::Processing->value,
            DocumentStatus::Pending->value,
        ])->exists();

        return $hasProcessing ? '3s' : null;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Novo Documento'),
        ];
    }
}
```

- [ ] **Verificar sintaxe**

```bash
php -l app/Filament/Admin/Resources/Documents/Pages/ListDocuments.php
```

- [ ] **Commit**

```bash
git add app/Filament/Admin/Resources/Documents/Pages/ListDocuments.php
git commit -m "feat: add polling to ListDocuments during document processing"
```

---

## Tarefa 13: Configuração do Servidor (VPS)

**Esta tarefa é executada diretamente na VPS, não no código.**

- [ ] **Configurar Nginx** — adicionar dentro do bloco `server {}` da aplicação:

```nginx
client_max_body_size 0;

location /tus/upload {
    proxy_pass http://127.0.0.1:80;
    proxy_read_timeout 3600;
    proxy_send_timeout 3600;
    proxy_request_buffering off;
}
```

- [ ] **Configurar PHP** — editar `/etc/php/8.x/fpm/php.ini`:

```ini
upload_max_filesize = 500M
post_max_size = 0
max_execution_time = 0
max_input_time = -1
```

Reiniciar PHP-FPM:

```bash
sudo systemctl restart php8.x-fpm
sudo systemctl reload nginx
```

- [ ] **Instalar e configurar Supervisor para o Horizon**

```bash
sudo apt install supervisor -y
sudo cp docs/supervisor-horizon.conf /etc/supervisor/conf.d/horizon.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start horizon
```

- [ ] **Verificar Horizon rodando**

```bash
php artisan horizon:status
```

Esperado: `Horizon is running.`

- [ ] **Acessar o dashboard do Horizon** em `/horizon` — deve exibir os supervisores `documents-supervisor` e `embeddings-supervisor` ativos.

---

## Tarefa 14: Validação Final + Teste de Ponta a Ponta

- [ ] **Rodar toda a suite de testes**

```bash
php artisan test --no-coverage
```

Esperado: todos passando.

- [ ] **Testar fluxo completo manualmente:**
  1. Acessar `/admin/documents/create`
  2. Selecionar um PDF grande (>10MB)
  3. Verificar que a barra de progresso TUS avança
  4. Após upload: verificar que o Document aparece na lista com `status = pending`
  5. Verificar no Horizon dashboard que o job foi enfileirado
  6. Aguardar processamento e observar a barra de progresso atualizar via polling
  7. Verificar que `status = completed` ao final

- [ ] **Testar o bug fix do chat:**
  1. Acessar o chat
  2. Enviar uma pergunta sobre um documento processado
  3. Verificar que o texto da resposta é exibido (não apenas as fontes)

- [ ] **Commit final**

```bash
git add -A
git commit -m "chore: final cleanup and validation for upload queue refactor"
```

- [ ] **Abrir PR da branch `feature/upload-queue-refactor` para `main`**

```bash
gh pr create \
  --title "feat: TUS upload >100MB, Redis+Horizon pipeline, chat bug fix" \
  --body "Closes: upload processing refactor and chat streaming bug"
```

---

## Resumo de Comandos Úteis

```bash
# Subir Horizon localmente (desenvolvimento)
php artisan horizon

# Monitorar filas
php artisan horizon:status

# Pausar/retomar Horizon
php artisan horizon:pause
php artisan horizon:continue

# Limpar filas Redis
php artisan horizon:clear

# Rodar testes específicos
php artisan test tests/Feature/Jobs/ --no-coverage
```
