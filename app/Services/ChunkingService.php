<?php

namespace App\Services;

class ChunkingService
{
    private int $chunkSize;
    private int $chunkOverlap;

    public function __construct()
    {
        $this->chunkSize = config('rag.chunk_size', 512);
        $this->chunkOverlap = config('rag.chunk_overlap', 50);
    }

    /**
     * Chunk text into overlapping segments with source location tracking.
     *
     * @param  array<int, array{page: int|null, text: string, start_line: int, end_line: int}>  $pages
     * @return array<int, array{content: string, chunk_index: int, token_count: int, source_file: string, source_location: string}>
     */
    public function chunk(string $text, string $sourceFile, array $pages): array
    {
        // Split words while tracking their byte offsets in the original text
        preg_match_all('/\S+/', $text, $matches, PREG_OFFSET_CAPTURE);
        $wordEntries = $matches[0]; // Each is [word, offset]
        $totalWords = count($wordEntries);

        if ($totalWords === 0) {
            return [];
        }

        // Pre-compute cumulative newline counts at each byte offset
        $newlineCounts = $this->buildNewlineIndex($text);

        $chunkSizeWords = $this->tokensToWords($this->chunkSize);
        $overlapWords = $this->tokensToWords($this->chunkOverlap);

        $chunks = [];
        $chunkIndex = 0;
        $position = 0;

        while ($position < $totalWords) {
            $end = min($position + $chunkSizeWords, $totalWords);
            $chunkWordEntries = array_slice($wordEntries, $position, $end - $position);

            $chunkContent = implode(' ', array_column($chunkWordEntries, 0));
            $tokenCount = $this->estimateTokens(count($chunkWordEntries));

            $startOffset = $chunkWordEntries[0][1];
            $lastEntry = end($chunkWordEntries);
            $endOffset = $lastEntry[1] + strlen($lastEntry[0]);

            $startLine = ($newlineCounts[$startOffset] ?? 0) + 1;
            $endLine = ($newlineCounts[$endOffset] ?? $newlineCounts[$startOffset] ?? 0) + 1;

            $sourceLocation = $this->formatSourceLocation($startLine, $endLine, $pages);

            $chunks[] = [
                'content' => $chunkContent,
                'chunk_index' => $chunkIndex,
                'token_count' => $tokenCount,
                'source_file' => $sourceFile,
                'source_location' => $sourceLocation,
            ];

            $chunkIndex++;
            $position = $end - $overlapWords;

            if ($position >= $totalWords || $end === $totalWords) {
                break;
            }
        }

        return $chunks;
    }

    private function tokensToWords(int $tokens): int
    {
        return (int) ceil($tokens / 1.3);
    }

    private function estimateTokens(int $wordCount): int
    {
        return (int) ceil($wordCount * 1.3);
    }

    /**
     * Build an index mapping byte offsets to cumulative newline count.
     * Returns an array where key=offset gives the number of newlines before that offset.
     */
    private function buildNewlineIndex(string $text): array
    {
        $index = [];
        $count = 0;
        $len = strlen($text);

        for ($i = 0; $i < $len; $i++) {
            $index[$i] = $count;
            if ($text[$i] === "\n") {
                $count++;
            }
        }
        $index[$len] = $count;

        return $index;
    }

    private function formatSourceLocation(int $startLine, int $endLine, array $pages): string
    {
        if ($this->hasPdfPages($pages)) {
            return $this->resolvePageRange($startLine, $endLine, $pages);
        }

        return $startLine === $endLine
            ? "L{$startLine}"
            : "L{$startLine}-L{$endLine}";
    }

    private function hasPdfPages(array $pages): bool
    {
        return isset($pages[0]['page']) && $pages[0]['page'] !== null;
    }

    private function resolvePageRange(int $startLine, int $endLine, array $pages): string
    {
        $startPage = null;
        $endPage = null;

        foreach ($pages as $page) {
            if ($startPage === null && $startLine >= $page['start_line'] && $startLine <= $page['end_line']) {
                $startPage = $page['page'];
            }
            if ($endLine >= $page['start_line'] && $endLine <= $page['end_line']) {
                $endPage = $page['page'];
            }
        }

        $startPage ??= $pages[0]['page'] ?? 1;
        $endPage ??= $startPage;

        return $startPage === $endPage
            ? "p.{$startPage}"
            : "p.{$startPage}-{$endPage}";
    }
}
