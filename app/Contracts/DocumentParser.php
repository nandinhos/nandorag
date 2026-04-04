<?php

namespace App\Contracts;

interface DocumentParser
{
    /**
     * Extract text and page metadata from a file.
     *
     * @return array{text: string, pages: array<int, array{page: int|null, text: string, start_line: int, end_line: int}>}
     */
    public function parse(string $filePath, string $mimeType): array;

    /**
     * Check if this parser supports the given mime type.
     */
    public function supports(string $mimeType): bool;
}
