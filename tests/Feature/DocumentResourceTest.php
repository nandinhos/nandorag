<?php

use App\Filament\Admin\Resources\Documents\Pages\CreateDocument;
use App\Filament\Admin\Resources\Documents\Pages\EditDocument;
use App\Filament\Admin\Resources\Documents\Pages\ListDocuments;
use App\Models\Document;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('list documents page renders', function () {
    Livewire::test(ListDocuments::class)->assertSuccessful();
});

test('create document page renders', function () {
    Livewire::test(CreateDocument::class)->assertSuccessful();
});

test('edit document page renders', function () {
    $document = Document::create([
        'filename' => 'test.txt',
        'original_filename' => 'test.txt',
        'mime_type' => 'text/plain',
        'tags' => ['test'],
    ]);

    Livewire::test(EditDocument::class, ['record' => $document->getRouteKey()])
        ->assertSuccessful();
});

test('list page shows documents', function () {
    Document::create([
        'filename' => 'my-document.pdf',
        'original_filename' => 'my-document.pdf',
        'mime_type' => 'application/pdf',
        'tags' => ['test'],
    ]);

    Livewire::test(ListDocuments::class)
        ->assertSee('my-document.pdf');
});
