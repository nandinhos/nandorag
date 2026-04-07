<?php

namespace App\Filament\Admin\Resources\Documents\Tables;

use App\Enums\DocumentStatus;
use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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

                TextColumn::make('filename')
                    ->label('Nome do Arquivo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->extraAttributes(['class' => 'font-heading uppercase tracking-wider']),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                // Coluna de progresso — visível apenas em processing
                TextColumn::make('progress')
                    ->label('Progresso')
                    ->state(fn (Document $record): string => $record->status === DocumentStatus::Processing
                        ? "{$record->processed_chunks}/{$record->total_chunks} chunks ({$record->progress}%)"
                        : ($record->status === DocumentStatus::Completed ? '100%' : '—')
                    )
                    ->color(fn (Document $record) => match ($record->status) {
                        DocumentStatus::Processing => 'warning',
                        DocumentStatus::Completed => 'success',
                        default => 'gray',
                    })
                    ->size('xs'),

                TextColumn::make('chunks_count')
                    ->counts('chunks')
                    ->label('Chunks')
                    ->badge()
                    ->color('gray')
                    ->size('xs')
                    ->alignCenter(),

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

                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->size('xs')
                    ->button()
                    ->visible(fn (Document $record) => $record->status === DocumentStatus::Failed)
                    ->requiresConfirmation()
                    ->modalHeading('Re-processar documento?')
                    ->modalDescription('Os chunks existentes serão deletados e o documento será re-processado do zero.')
                    ->action(function (Document $record) {
                        $record->update([
                            'status' => DocumentStatus::Pending,
                            'progress' => 0,
                            'processed_chunks' => 0,
                            'error_log' => null,
                            'queued_at' => now(),
                        ]);
                        ProcessDocumentJob::dispatch($record->id)->onQueue('documents');
                        Notification::make()->title('Re-processamento agendado')->success()->send();
                    })
                    ->extraAttributes(['class' => 'neo-border-sm shadow-neo-xs font-heading']),

                Action::make('prioritize')
                    ->label('Priorizar')
                    ->icon('heroicon-o-bolt')
                    ->color('warning')
                    ->size('xs')
                    ->button()
                    ->visible(fn (Document $record) => in_array($record->status, [
                        DocumentStatus::Pending,
                        DocumentStatus::Failed,
                    ]))
                    ->action(function (Document $record) {
                        $record->update([
                            'status' => DocumentStatus::Pending,
                            'error_log' => null,
                            'queued_at' => now(),
                        ]);
                        ProcessDocumentJob::dispatch($record->id)->onQueue('priority');
                        Notification::make()->title('Documento priorizado')->success()->send();
                    })
                    ->extraAttributes(['class' => 'neo-border-sm shadow-neo-xs font-heading']),

                Action::make('view_error')
                    ->label('Ver Erro')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->size('xs')
                    ->button()
                    ->visible(fn (Document $record) => $record->status === DocumentStatus::Failed && $record->error_log)
                    ->modalHeading(fn (Document $record) => "Erro — {$record->filename}")
                    ->modalContent(fn (Document $record) => view('filament.modals.document-error', ['document' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
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
