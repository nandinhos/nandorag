<?php

use App\Enums\DocumentStatus;
use App\Jobs\GenerateEmbeddingsJob;
use App\Models\Document;
use App\Models\DocumentChunk;
use App\Services\EmbeddingService;
use Laravel\Ai\Embeddings;

test('generates embeddings for all chunks and completes document', function () {
    Embeddings::fake(fn () => [array_fill(0, 768, 0.1)]);

    $document = Document::factory()->processing()->create([
        'total_chunks' => 3,
        'processed_chunks' => 0,
        'progress' => 0,
    ]);

    DocumentChunk::factory()->count(3)->create([
        'document_id' => $document->id,
        'embedding' => null,
    ]);

    (new GenerateEmbeddingsJob($document->id))->handle(
        app(EmbeddingService::class),
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
        'embedding' => null,
    ]);

    expect(fn () => (new GenerateEmbeddingsJob($document->id))->handle(
        app(EmbeddingService::class),
    ))->toThrow(Exception::class);

    $document->refresh();

    expect($document->status)->toBe(DocumentStatus::Failed);
    expect($document->error_log)->not->toBeNull();
});
