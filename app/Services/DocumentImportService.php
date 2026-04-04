<?php

namespace App\Services;

use Spatie\PdfToText\Pdf;

class DocumentImportService
{
    /**
     * Extract text from a file with source location metadata.
     *
     * @return array{text: string, pages: array<int, array{page: int, text: string, start_line: int, end_line: int}>}
     */
    public function extract(string $filePath, string $mimeType): array
    {
        return match ($mimeType) {
            'application/pdf' => $this->extractFromPdf($filePath),
            default => $this->extractFromText($filePath),
        };
    }

    private function extractFromPdf(string $filePath): array
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

            $allText .= $pageText . "\n";
            $currentLine += $lineCount;
        }

        return [
            'text' => trim($allText),
            'pages' => $pages,
        ];
    }

    private function extractFromText(string $filePath): array
    {
        $text = file_get_contents($filePath);
        $lines = explode("\n", $text);

        return [
            'text' => $text,
            'pages' => [
                [
                    'page' => null,
                    'text' => $text,
                    'start_line' => 1,
                    'end_line' => count($lines),
                ],
            ],
        ];
    }

    private function getPdfPageCount(string $filePath): int
    {
        $fullText = Pdf::getText($filePath);
        // pdftotext inserts form feed characters between pages
        $pages = preg_split('/\f/', $fullText);

        return max(1, count(array_filter($pages, fn ($p) => trim($p) !== '')));
    }
}
