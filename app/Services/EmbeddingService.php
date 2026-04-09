<?php

namespace App\Services;

use App\Contracts\EmbeddingEngine;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentChunk;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmbeddingService
{
    public function __construct(
        private DocumentImportService $importService,
        private ChunkingService $chunkingService,
        private EmbeddingEngine $embeddingEngine,
    ) {}

    public function processDocument(Document $document): void
    {
        $document->update(['status' => DocumentStatus::Processing]);

        try {
            $result = $this->importService->extract(
                Storage::disk('local')->path($document->file_path),
                $document->mime_type,
            );

            $document->update(['content' => $result['text']]);

            $chunks = $this->chunkingService->chunk(
                $result['text'],
                $document->original_filename,
                $result['pages'],
            );

            foreach ($chunks as $chunkData) {
                $chunkText = $chunkData['content'];

                if (strlen($chunkText) > 2000) {
                    $chunkText = substr($chunkText, 0, 2000);
                }

                $chunkText = mb_convert_encoding($chunkText, 'UTF-8', 'UTF-8');
                $chunkText = preg_replace('/[\x00-\x1F\x7F]/u', '', $chunkText);

                $embedding = $this->generateEmbedding($chunkText);

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
     * @param  array<string>  $texts
     * @return array<array<float>>
     */
    public function generateBatch(array $texts): array
    {
        return $this->embeddingEngine->generateBatch($texts);
    }

    /**
     * @return array<float>
     */
    public function generateEmbedding(string $text): array
    {
        return $this->embeddingEngine->generate($text);
    }
}
