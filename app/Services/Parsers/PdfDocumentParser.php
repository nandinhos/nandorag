<?php

namespace App\Services\Parsers;

use App\Contracts\DocumentParser;
use Spatie\PdfToText\Pdf;

class PdfDocumentParser implements DocumentParser
{
    /**
     * Extract text and page metadata from a PDF file.
     *
     * @return array{text: string, pages: array<int, array{page: int|null, text: string, start_line: int, end_line: int}>}
     */
    public function parse(string $filePath, string $mimeType): array
    {
        $pageCount = $this->getPdfPageCount($filePath);
        $pages = [];
        $allText = '';
        $currentLine = 1;

        for ($page = 1; $page <= $pageCount; $page++) {
            $pageText = Pdf::getText($filePath, options: [
                "-f {$page}",
                "-l {$page}",
                '-layout',
            ]);

            $lineCount = substr_count($pageText, "\n") + 1;

            $pages[] = [
                'page' => $page,
                'text' => $pageText,
                'start_line' => $currentLine,
                'end_line' => $currentLine + $lineCount - 1,
            ];

            $allText .= $pageText."\n";
            $currentLine += $lineCount;
        }

        return [
            'text' => trim($allText),
            'pages' => $pages,
        ];
    }

    /**
     * Check if this parser supports PDF mime types.
     */
    public function supports(string $mimeType): bool
    {
        return $mimeType === 'application/pdf';
    }

    private function getPdfPageCount(string $filePath): int
    {
        $fullText = Pdf::getText($filePath);
        // pdftotext inserts form feed characters between pages
        $pages = preg_split('/\f/', $fullText);

        return max(1, count(array_filter($pages, fn ($p) => trim($p) !== '')));
    }
}
