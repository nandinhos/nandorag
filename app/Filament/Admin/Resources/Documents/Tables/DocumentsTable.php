<?php

namespace App\Filament\Admin\Resources\Documents\Tables;

use App\Enums\DocumentStatus;
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
                // Extension Badge
                TextColumn::make('extension')
                    ->state(fn ($record) => strtoupper(pathinfo($record->filename, PATHINFO_EXTENSION) ?: 'DOC'))
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'PDF' => 'danger',
                        'MD' => 'info',
                        'TXT' => 'warning',
                        default => 'gray',
                    })
                    ->label('EXT'),

                // Filename
                TextColumn::make('filename')
                    ->label('Nome do Arquivo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->extraAttributes(['class' => 'font-heading uppercase tracking-wider']),
                
                // Mime Type (Hidden on mobile)
                TextColumn::make('mime_type')
                    ->label('Tipo (MIME)')
                    ->size('xs')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Status
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                
                // Chunks
                TextColumn::make('chunks_count')
                    ->counts('chunks')
                    ->label('Chunks')
                    ->badge()
                    ->color('gray')
                    ->size('xs')
                    ->alignCenter(),

                // Date
                TextColumn::make('created_at')
                    ->label('Data de Upload')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->size('xs')
                    ->color('gray')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filtrar por Status')
                    ->options(DocumentStatus::class),
            ])
            ->actions([
                EditAction::make()
                    ->label('Editar')
                    ->button()
                    ->color('warning')
                    ->size('xs')
                    ->extraAttributes(['class' => 'neo-border-sm shadow-neo-xs font-heading']),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                ])->label('Ações em Massa'),
            ]);
    }
}
