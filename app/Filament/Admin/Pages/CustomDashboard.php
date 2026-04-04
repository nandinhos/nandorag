<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\DashboardStats;
use App\Models\Chat;
use App\Models\Document;
use App\Models\DocumentChunk;
use Filament\Pages\Dashboard;

class CustomDashboard extends Dashboard
{
    public int $documentsCount = 0;

    public int $chatsCount = 0;

    public int $chunksCount = 0;

    public function mount(): void
    {
        $this->documentsCount = Document::count();
        $this->chatsCount = Chat::count();
        $this->chunksCount = DocumentChunk::count();
    }

    public function getWidgets(): array
    {
        return [
            DashboardStats::class,
        ];
    }
}
