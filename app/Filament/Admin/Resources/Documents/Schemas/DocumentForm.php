<?php

namespace App\Filament\Admin\Resources\Documents\Schemas;

use App\Enums\DocumentStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(null)
            ->schema([
                Section::make('Upload')
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('Document File')
                            ->disk('local')
                            ->directory('documents')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'text/plain',
                                'text/markdown',
                                'text/x-markdown',
                            ])
                            ->maxSize(10240)
                            ->required()
                            ->hiddenOn('edit')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $filename = is_array($state)
                                        ? (array_values($state)[0] ?? null)?->getClientOriginalName()
                                        : $state->getClientOriginalName();

                                    if ($filename) {
                                        $set('filename', $filename);
                                        $set('original_filename', $filename);
                                        $set('tags', [$filename]);
                                    }
                                }
                            }),

                        TextInput::make('filename')
                            ->required(),

                        TextInput::make('original_filename')
                            ->required()
                            ->hiddenOn('edit'),
                    ]),

                Section::make('Metadata')
                    ->schema([
                        TagsInput::make('tags')
                            ->placeholder('Add tags...'),

                        Placeholder::make('status_display')
                            ->label('Status')
                            ->content(fn ($record) => $record?->status?->getLabel() ?? 'New')
                            ->visibleOn('edit'),

                        Placeholder::make('chunk_count')
                            ->label('Chunks')
                            ->content(fn ($record) => $record?->chunks()->count() ?? 0)
                            ->visibleOn('edit'),
                    ]),

                Section::make('Content')
                    ->schema([
                        Textarea::make('content')
                            ->rows(10)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->visibleOn('edit'),
            ]);
    }
}
