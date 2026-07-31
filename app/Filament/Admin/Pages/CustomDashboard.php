<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\DashboardStats;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Document;
use App\Models\DocumentChunk;
use App\Services\OllamaService;
use Filament\Pages\Dashboard;
use Illuminate\Support\Collection;

class CustomDashboard extends Dashboard
{
    protected string $view = 'filament.admin.pages.dashboard';

    public int $documentsCount = 0;

    public int $chatsCount = 0;

    public int $chunksCount = 0;

    public int $messagesCount = 0;

    // doc status breakdown
    public int $docsCompleted = 0;

    public int $docsPending = 0;

    public int $docsFailed = 0;

    public Collection $recentDocuments;

    public Collection $recentChats;

    public array $availableModels = [];

    public bool $ollamaOk = false;

    public function mount(): void
    {
        $this->documentsCount = Document::count();
        $this->chatsCount = Chat::count();
        $this->chunksCount = DocumentChunk::count();

        $this->docsCompleted = Document::where('status', 'completed')->count();
        $this->docsPending = Document::whereIn('status', ['pending', 'processing'])->count();
        $this->docsFailed = Document::where('status', 'failed')->count();

        $this->recentDocuments = Document::withCount('chunks')
            ->latest()
            ->limit(8)
            ->get();

        $this->recentChats = Chat::withCount('messages')
            ->with(['document:id,filename'])
            ->latest()
            ->limit(6)
            ->get();

        $this->messagesCount = ChatMessage::count();

        try {
            $ollama = app(OllamaService::class);
            $this->availableModels = $ollama->getModels();
            $this->ollamaOk = true;
        } catch (\Throwable) {
            $this->availableModels = [];
            $this->ollamaOk = false;
        }
    }

    public function getWidgets(): array
    {
        return [
            DashboardStats::class,
        ];
    }
}
