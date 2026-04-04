<?php

use App\Services\ChunkingService;

beforeEach(function () {
    config([
        'rag.chunk_size' => 100,
        'rag.chunk_overlap' => 20,
    ]);
});

test('chunks text into fixed-size segments', function () {
    $service = new ChunkingService();

    $text = implode(' ', array_map(fn ($i) => "word{$i}", range(1, 200)));
    $pages = [['page' => null, 'text' => $text, 'start_line' => 1, 'end_line' => 1]];

    $chunks = $service->chunk($text, 'test.txt', $pages);

    expect($chunks)->not->toBeEmpty();
    expect($chunks[0])->toHaveKeys(['content', 'chunk_index', 'token_count', 'source_file', 'source_location']);
    expect($chunks[0]['chunk_index'])->toBe(0);
    expect($chunks[0]['source_file'])->toBe('test.txt');
});

test('chunks have overlap between consecutive chunks', function () {
    $service = new ChunkingService();

    $text = implode(' ', array_map(fn ($i) => "word{$i}", range(1, 300)));
    $pages = [['page' => null, 'text' => $text, 'start_line' => 1, 'end_line' => 1]];

    $chunks = $service->chunk($text, 'test.txt', $pages);

    expect(count($chunks))->toBeGreaterThan(1);

    $words1 = explode(' ', $chunks[0]['content']);
    $words2 = explode(' ', $chunks[1]['content']);
    $overlap = array_intersect($words1, $words2);

    expect(count($overlap))->toBeGreaterThan(0);
});

test('handles empty text', function () {
    $service = new ChunkingService();

    $chunks = $service->chunk('', 'empty.txt', []);

    expect($chunks)->toBeEmpty();
});

test('handles text smaller than chunk size', function () {
    $service = new ChunkingService();

    $text = 'This is a short text.';
    $pages = [['page' => null, 'text' => $text, 'start_line' => 1, 'end_line' => 1]];

    $chunks = $service->chunk($text, 'short.txt', $pages);

    expect($chunks)->toHaveCount(1);
    expect($chunks[0]['content'])->toBe($text);
});

test('resolves page range for PDF source location', function () {
    $service = new ChunkingService();

    $page1 = "Page one content with several words to fill up space.\n";
    $page2 = "Page two content with different words for testing.\n";
    $text = $page1 . $page2;

    $pages = [
        ['page' => 1, 'text' => trim($page1), 'start_line' => 1, 'end_line' => 1],
        ['page' => 2, 'text' => trim($page2), 'start_line' => 2, 'end_line' => 2],
    ];

    $chunks = $service->chunk($text, 'doc.pdf', $pages);

    expect($chunks[0]['source_location'])->toStartWith('p.');
});

test('resolves line range for text source location', function () {
    $service = new ChunkingService();

    $text = "Line one\nLine two\nLine three\nLine four\nLine five";
    $pages = [['page' => null, 'text' => $text, 'start_line' => 1, 'end_line' => 5]];

    $chunks = $service->chunk($text, 'doc.md', $pages);

    expect($chunks[0]['source_location'])->toMatch('/^L\d+/');
});
