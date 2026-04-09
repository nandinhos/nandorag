<?php

use App\Enums\DocumentStatus;
use App\Jobs\GenerateEmbeddingsJob;
use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use App\Models\DocumentChunk;
use App\Services\ChunkingService;
use App\Services\DocumentImportService;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

test('sets document to processing and dispatches GenerateEmbeddingsJob on success', function () {
    Bus::fake([GenerateEmbeddingsJob::class]);
    Storage::fake('local');

    $document = Document::factory()->create([
        'file_path' => 'documents/test.txt',
        'mime_type' => 'text/plain',
    ]);

    Storage::disk('local')->put('documents/test.txt', str_repeat('Word ', 200));

    (new ProcessDocumentJob($document->id))->handle(
        app(DocumentImportService::class),
        app(ChunkingService::class),
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
        'mime_type' => 'text/plain',
    ]);

    // File does not exist — will throw

    expect(fn () => (new ProcessDocumentJob($document->id))->handle(
        app(DocumentImportService::class),
        app(ChunkingService::class),
    ))->toThrow(Exception::class);

    $document->refresh();

    expect($document->status)->toBe(DocumentStatus::Failed);
    expect($document->error_log)->not->toBeNull();
});
