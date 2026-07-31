<?php

namespace App\Filament\Admin\Resources\Documents\Schemas;

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
                    ->label('Arquivo')
                    ->columnSpan(['default' => 1, 'md' => 2])
                    ->schema([
                        TextInput::make('filename')
                            ->label('Nome do Arquivo')
                            ->required()
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->visibleOn('edit'),

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
