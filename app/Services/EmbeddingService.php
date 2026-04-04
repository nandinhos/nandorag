<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentChunk;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Embeddings;

class EmbeddingService
{
    public function __construct(
        private DocumentImportService $importService,
        private ChunkingService $chunkingService,
    ) {}

    public function processDocument(Document $document): void
    {
        $document->update(['status' => DocumentStatus::Processing]);

        try {
            $result = $this->importService->extract(
                storage_path("app/documents/{$document->file_path}"),
                $document->mime_type,
            );

            $document->update(['content' => $result['text']]);

            $chunks = $this->chunkingService->chunk(
                $result['text'],
                $document->original_filename,
                $result['pages'],
            );

            foreach ($chunks as $chunkData) {
                $embedding = $this->generateEmbedding($chunkData['content']);

                DocumentChunk::create([
                    'document_id' => $document->id,
                    'content' => $chunkData['content'],
                    'chunk_index' => $chunkData['chunk_index'],
                    'token_count' => $chunkData['token_count'],
                    'source_file' => $chunkData['source_file'],
                    'source_location' => $chunkData['source_location'],
                    'embedding' => $embedding,
                ]);
            }

            $document->update(['status' => DocumentStatus::Completed]);
        } catch (\Throwable $e) {
            Log::error("Document processing failed for #{$document->id}: {$e->getMessage()}");
            $document->update(['status' => DocumentStatus::Failed]);

            throw $e;
        }
    }

    /**
     * @return array<float>
     */
    public function generateEmbedding(string $text): array
    {
        $response = Embeddings::for([$text])
            ->dimensions(config('rag.embedding_dimensions', 768))
            ->generate();

        return $response->first();
    }
}
