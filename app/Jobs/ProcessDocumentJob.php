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
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    public int $tries = 3;

    public array $backoff = [60, 300, 600];

    public function __construct(public readonly int $documentId) {}

    public function handle(DocumentImportService $importService, ChunkingService $chunkingService): void
    {
        $document = Document::findOrFail($this->documentId);

        $document->update([
            'status' => DocumentStatus::Processing,
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
                    'document_id' => $document->id,
                    'content' => $chunkData['content'],
                    'chunk_index' => $chunkData['chunk_index'],
                    'token_count' => $chunkData['token_count'],
                    'source_file' => $chunkData['source_file'],
                    'source_location' => $chunkData['source_location'],
                    'embedding' => null,
                ]);
            }

            $document->update([
                'content' => $result['text'],
                'total_chunks' => count($chunks),
                'processed_chunks' => 0,
                'progress' => 0,
                'error_log' => null,
            ]);

            GenerateEmbeddingsJob::dispatch($document->id)->onQueue(
                $this->queue === 'priority' ? 'priority' : 'embeddings'
            );

        } catch (\Throwable $e) {
            Log::error("ProcessDocumentJob failed for #{$document->id}: {$e->getMessage()}");

            $document->update([
                'status' => DocumentStatus::Failed,
                'error_log' => $e->getMessage()."\n".$e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
