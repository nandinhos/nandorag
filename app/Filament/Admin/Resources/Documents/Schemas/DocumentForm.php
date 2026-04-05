<?php

namespace App\Filament\Admin\Resources\Documents\Schemas;

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
            ->columns([
                'default' => 1,
                'md' => 2,
            ])
            ->schema([
                Section::make('Upload')
                    ->label('Upload de Arquivo')
                    ->columnSpan([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('Arquivo do Documento')
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
                            ->columnSpanFull()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    return;
                                }

                                $file = is_array($state)
                                    ? (array_values($state)[0] ?? null)
                                    : $state;

                                if (! is_object($file) || ! method_exists($file, 'getClientOriginalName')) {
                                    return;
                                }

                                $filename = $file->getClientOriginalName();

                                if ($filename) {
                                    $set('filename', $filename);
                                    $set('original_filename', $filename);
                                    $set('tags', [$filename]);
                                }
                            }),

                        TextInput::make('filename')
                            ->label('Nome do Arquivo')
                            ->required(),

                        TextInput::make('original_filename')
                            ->label('Nome Original')
                            ->required()
                            ->hiddenOn('edit'),
                    ])->columns([
                        'default' => 1,
                        'sm' => 2,
                    ]),

                Section::make('Metadata')
                    ->label('Metadados')
                    ->columnSpan([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TagsInput::make('tags')
                            ->label('Tags')
                            ->placeholder('Adicionar tags...')
                            ->columnSpanFull(),

                        Placeholder::make('status_display')
                            ->label('Status')
                            ->content(fn ($record) => $record?->status?->getLabel() ?? 'Novo')
                            ->visibleOn('edit'),

                        Placeholder::make('chunk_count')
                            ->label('Chunks')
                            ->content(fn ($record) => $record?->chunks()->count() ?? 0)
                            ->visibleOn('edit'),
                    ])->columns([
                        'default' => 1,
                        'sm' => 2,
                    ]),

                Section::make('Content')
                    ->label('Conteúdo')
                    ->columnSpan([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        Textarea::make('content')
                            ->label('Texto Extraído')
                            ->rows(10)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->visibleOn('edit'),
            ]);
    }
}
