<?php

use App\Services\DocumentImportService;

test('extracts text from a text file', function () {
    $service = new DocumentImportService();

    $tmpFile = tempnam(sys_get_temp_dir(), 'test_');
    file_put_contents($tmpFile, "Line one\nLine two\nLine three");

    $result = $service->extract($tmpFile, 'text/plain');

    expect($result['text'])->toBe("Line one\nLine two\nLine three");
    expect($result['pages'])->toHaveCount(1);
    expect($result['pages'][0]['page'])->toBeNull();
    expect($result['pages'][0]['start_line'])->toBe(1);
    expect($result['pages'][0]['end_line'])->toBe(3);

    unlink($tmpFile);
});

test('extracts text from a markdown file', function () {
    $service = new DocumentImportService();

    $tmpFile = tempnam(sys_get_temp_dir(), 'test_') . '.md';
    file_put_contents($tmpFile, "# Title\n\nSome content here.");

    $result = $service->extract($tmpFile, 'text/markdown');

    expect($result['text'])->toContain('# Title');
    expect($result['text'])->toContain('Some content here.');

    unlink($tmpFile);
});

test('extracts text from a PDF file with page tracking', function () {
    $testPdfDir = base_path('pdf_test');
    $pdfFiles = glob("{$testPdfDir}/*.pdf");

    if (empty($pdfFiles)) {
        $this->markTestSkipped('No test PDF files found in pdf_test/');
    }

    $service = new DocumentImportService();
    $result = $service->extract($pdfFiles[0], 'application/pdf');

    expect($result['text'])->not->toBeEmpty();
    expect($result['pages'])->not->toBeEmpty();
    expect($result['pages'][0]['page'])->toBe(1);
    expect($result['pages'][0]['start_line'])->toBe(1);
});
