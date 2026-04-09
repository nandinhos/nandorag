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
    use InteractsWithQueue, Queueable, SerializesModels;

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
                try {
                    $texts = [];
                    foreach ($batch as $chunk) {
                        $chunkText = mb_convert_encoding($chunk->content, 'UTF-8', 'UTF-8');
                        $chunkText = preg_replace('/[\x00-\x1F\x7F]/u', '', $chunkText);
                        
                        if (strlen($chunkText) > 2000) {
                            $chunkText = substr($chunkText, 0, 2000);
                        }
                        $texts[] = $chunkText;
                    }

                    $embeddings = $embeddingService->generateBatch($texts);

                    foreach ($batch as $index => $chunk) {
                        if (isset($embeddings[$index])) {
                            $chunk->update(['embedding' => $embeddings[$index]]);
                            $processed++;
                        }
                    }

                    $progress = $total > 0 ? (int) round($processed / $total * 100) : 100;

                    $document->update([
                        'processed_chunks' => $processed,
                        'progress' => $progress,
                    ]);

                } catch (\Throwable $batchError) {
                    Log::warning("Batch processing failed for document #{$document->id}, trying individual chunks fallback: " . $batchError->getMessage());
                    
                    // Fallback para processamento individual se o batch falhar (ex: um texto específico corrompido)
                    foreach ($batch as $chunk) {
                         try {
                            $chunkText = substr($chunk->content, 0, 2000);
                            $embedding = $embeddingService->generateEmbedding($chunkText);
                            $chunk->update(['embedding' => $embedding]);
                            $processed++;
                         } catch (\Throwable $singleError) {
                            Log::error("Individual chunk fallback failed: " . $singleError->getMessage());
                         }
                    }
                }
            }

            $document->update([
                'status' => DocumentStatus::Completed,
                'progress' => 100,
            ]);

        } catch (\Throwable $e) {
            Log::error("GenerateEmbeddingsJob failed for #{$document->id}: {$e->getMessage()}");

            $document->update([
                'status' => DocumentStatus::Failed,
                'error_log' => $e->getMessage()."\n".$e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
