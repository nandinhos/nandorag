<?php

namespace App\Filament\Admin\Resources\Documents\Pages;

use App\Filament\Admin\Resources\Documents\DocumentResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditDocument extends EditRecord
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
            DeleteAction::make()
                ->label('Excluir'),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Salvar Alterações')
            ->extraAttributes(['class' => 'neo-border-sm shadow-neo-xs font-heading']);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar')
            ->color('danger')
            ->extraAttributes(['class' => 'neo-border-sm shadow-neo-xs font-heading']);
    }
}
