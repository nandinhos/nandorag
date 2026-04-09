<?php

namespace App\Services;

use App\Models\DocumentChunk;
use Illuminate\Support\Collection;
use Laravel\Ai\Embeddings;

class RagRetrievalService
{
    /**
     * Retrieve relevant chunks for a query.
     *
     * @return Collection<int, DocumentChunk>
     */
    public function retrieve(string $query, ?int $documentId = null): Collection
    {
        $queryEmbedding = $this->generateQueryEmbedding($query);

        $builder = DocumentChunk::query()
            ->whereVectorSimilarTo(
                'embedding',
                $queryEmbedding,
                minSimilarity: config('rag.similarity_threshold', 0.5),
            )
            ->limit(config('rag.top_k', 10));

        if ($documentId) {
            $builder->where('document_id', $documentId);
        }

        return $builder->get();
    }

    /**
     * Build a context prompt from retrieved chunks.
     */
    public function buildContext(Collection $chunks): string
    {
        if ($chunks->isEmpty()) {
            return 'Nenhum documento relevante encontrado.';
        }

        $context = "USE OS SEGUINTES TRECHOS DE DOCUMENTOS PARA RESPONDER À PERGUNTA. CITE AS FONTES SE POSSÍVEL.\n\n";

        foreach ($chunks as $i => $chunk) {
            $num = $i + 1;
            $context .= "### FONTE #{$num} | ARQUIVO: {$chunk->source_file} | LOCAL: {$chunk->source_location} ###\n";
            $context .= "CONTEÚDO: {$chunk->content}\n\n";
        }

        return $context;
    }

    /**
     * Format chunks as source references for storage.
     */
    public function formatSources(Collection $chunks): array
    {
        return $chunks->map(fn (DocumentChunk $chunk) => [
            'source_file' => $chunk->source_file,
            'source_location' => $chunk->source_location,
            'excerpt' => str()->limit($chunk->content, 200),
        ])->toArray();
    }

    /**
     * @return array<float>
     */
    private function generateQueryEmbedding(string $query): array
    {
        $response = Embeddings::for([$query])
            ->generate();

        return $response->first();
    }
}
