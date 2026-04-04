<?php

namespace App\Services\Parsers;

use App\Contracts\DocumentParser;

class TextDocumentParser implements DocumentParser
{
    /**
     * Extract text from a text file (.txt, .md, etc).
     *
     * @return array{text: string, pages: array<int, array{page: int|null, text: string, start_line: int, end_line: int}>}
     */
    public function parse(string $filePath, string $mimeType): array
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

    /**
     * Check if this parser supports text mime types.
     */
    public function supports(string $mimeType): bool
    {
        return in_array($mimeType, [
            'text/plain',
            'text/markdown',
            'application/octet-stream', // Some .md files might be detected as this
        ]);
    }
}
