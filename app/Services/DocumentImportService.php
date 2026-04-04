<?php

namespace App\Services;

use App\Contracts\DocumentParser;
use App\Services\Parsers\TextDocumentParser;
use Exception;

class DocumentImportService
{
    /**
     * @param  array<int, DocumentParser>  $parsers
     */
    public function __construct(
        private array $parsers
    ) {}

    /**
     * Extract text from a file by delegating to the appropriate parser.
     *
     * @return array{text: string, pages: array<int, array{page: int|null, text: string, start_line: int, end_line: int}>}
     *
     * @throws Exception
     */
    public function extract(string $filePath, string $mimeType): array
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($mimeType)) {
                return $parser->parse($filePath, $mimeType);
            }
        }

        // Fallback to text parser if no specific match is found
        foreach ($this->parsers as $parser) {
            if ($parser instanceof TextDocumentParser) {
                return $parser->parse($filePath, $mimeType);
            }
        }

        throw new Exception("No document parser found for mime type: {$mimeType}");
    }
}
