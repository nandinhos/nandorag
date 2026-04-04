<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Chat;
use App\Models\Document;
use App\Models\DocumentChunk;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Documents', Document::count())
                ->description('Arquivos carregados')
                ->icon('heroicon-o-document-text'),
            Stat::make('Chats', Chat::count())
                ->description('Conversas ativas')
                ->icon('heroicon-o-chat-bubble-left-right'),
            Stat::make('Chunks', DocumentChunk::count())
                ->description('Fragmentos indexados')
                ->icon('heroicon-o-puzzle-piece'),
        ];
    }
}
