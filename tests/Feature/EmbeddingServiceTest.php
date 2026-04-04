<?php

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Services\EmbeddingService;
use Laravel\Ai\Embeddings;

test('processes a document and creates chunks with embeddings', function () {
    Embeddings::fake(fn () => [array_fill(0, 768, 0.1)]);

    // Create a test text file
    $storagePath = storage_path('app/documents');
    if (! is_dir($storagePath)) {
        mkdir($storagePath, 0755, true);
    }
    file_put_contents("{$storagePath}/test.txt", str_repeat("This is test content for chunking. ", 50));

    $document = Document::create([
        'filename' => 'test.txt',
        'original_filename' => 'test.txt',
        'mime_type' => 'text/plain',
        'file_path' => 'test.txt',
        'tags' => ['test'],
    ]);

    $service = app(EmbeddingService::class);
    $service->processDocument($document);

    $document->refresh();

    expect($document->status)->toBe(DocumentStatus::Completed);
    expect($document->content)->not->toBeEmpty();
    expect($document->chunks)->not->toBeEmpty();
    expect($document->chunks->first()->embedding)->toBeArray();
    expect($document->chunks->first()->embedding)->toHaveCount(768);
    expect($document->chunks->first()->source_file)->toBe('test.txt');

    // Cleanup
    @unlink("{$storagePath}/test.txt");
});

test('sets document status to failed on error', function () {
    Embeddings::fake(fn () => throw new \RuntimeException('Ollama connection failed'));

    $storagePath = storage_path('app/documents');
    if (! is_dir($storagePath)) {
        mkdir($storagePath, 0755, true);
    }
    file_put_contents("{$storagePath}/fail-test.txt", "Some content here.");

    $document = Document::create([
        'filename' => 'fail-test.txt',
        'original_filename' => 'fail-test.txt',
        'mime_type' => 'text/plain',
        'file_path' => 'fail-test.txt',
        'tags' => [],
    ]);

    $service = app(EmbeddingService::class);

    try {
        $service->processDocument($document);
    } catch (\RuntimeException) {
        // Expected
    }

    $document->refresh();
    expect($document->status)->toBe(DocumentStatus::Failed);

    @unlink("{$storagePath}/fail-test.txt");
});
