<?php

namespace App\Filament\Admin\Resources\Documents\Pages;

use App\Filament\Admin\Resources\Documents\DocumentResource;
use App\Services\EmbeddingService;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Voltar')
                ->color('purple')
                ->icon(Heroicon::ChevronLeft)
                ->url(url()->previous())
                ->extraAttributes(['class' => 'neo-border-sm shadow-neo-xs font-heading']),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Criar Documento')
            ->extraAttributes(['class' => 'neo-border-sm shadow-neo-xs font-heading']);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar')
            ->color('danger')
            ->extraAttributes(['class' => 'neo-border-sm shadow-neo-xs font-heading']);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $originalFilename = $data['original_filename'] ?? $data['filename'];

        $data['mime_type'] = $this->detectMimeType($originalFilename);

        if (empty($data['tags'])) {
            $data['tags'] = [$originalFilename];
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        app(EmbeddingService::class)->processDocument($this->record);
    }

    private function detectMimeType(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => 'application/pdf',
            'md', 'markdown' => 'text/markdown',
            default => 'text/plain',
        };
    }
}
