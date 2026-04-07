<?php

namespace App\Filament\Admin\Resources\Documents\Pages;

use App\Enums\DocumentStatus;
use App\Filament\Admin\Resources\Documents\DocumentResource;
use App\Models\Document;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    // Polling a cada 3 segundos quando há documentos processando
    public function getPollingInterval(): ?string
    {
        $hasProcessing = Document::whereIn('status', [
            DocumentStatus::Processing->value,
            DocumentStatus::Pending->value,
        ])->exists();

        return $hasProcessing ? '3s' : null;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Novo Documento'),
        ];
    }
}
