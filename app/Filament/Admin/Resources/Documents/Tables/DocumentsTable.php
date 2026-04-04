<?php

namespace App\Filament\Admin\Resources\Documents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('filename')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mime_type')
                    ->label('Type')
                    ->sortable(),
                TextColumn::make('tags')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('chunks_count')
                    ->counts('chunks')
                    ->label('Chunks')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
                SelectFilter::make('mime_type')
                    ->options([
                        'application/pdf' => 'PDF',
                        'text/plain' => 'Text',
                        'text/markdown' => 'Markdown',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
