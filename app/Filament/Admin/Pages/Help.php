<?php

namespace App\Filament\Admin\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Http;

class Help extends Page
{
    protected static ?string $title = 'Ajuda & Status';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?int $navigationSort = 3;

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

    protected string $view = 'filament.admin.pages.help';

    public bool $ollamaConnected = false;

    public bool $embeddingModelAvailable = false;

    public bool $chatModelAvailable = false;

    public string $ollamaError = '';

    public array $availableModels = [];

    public function mount(): void
    {
        $this->checkStatus();
    }

    public function checkStatus(): void
    {
        $this->ollamaConnected = false;
        $this->embeddingModelAvailable = false;
        $this->chatModelAvailable = false;
        $this->ollamaError = '';
        $this->availableModels = [];

        $baseUrl = config('ai.providers.ollama.url', 'http://localhost:11434');

        try {
            $response = Http::timeout(5)->get($baseUrl);
            $this->ollamaConnected = $response->successful();
        } catch (\Throwable $e) {
            $this->ollamaError = $e->getMessage();

            return;
        }

        if (! $this->ollamaConnected) {
            return;
        }

        try {
            $response = Http::timeout(5)->get("{$baseUrl}/api/tags");

            if ($response->successful()) {
                $models = collect($response->json('models', []))
                    ->pluck('name')
                    ->toArray();

                $this->availableModels = $models;

                $embeddingModel = config('rag.embedding_model', 'nomic-embed-text');
                $chatModel = config('rag.chat_model', 'llama3.2:3b');

                $this->embeddingModelAvailable = collect($models)->contains(
                    fn ($m) => str($m)->startsWith($embeddingModel)
                );
                $this->chatModelAvailable = collect($models)->contains(
                    fn ($m) => str($m)->startsWith($chatModel)
                );
            }
        } catch (\Throwable $e) {
            $this->ollamaError = $e->getMessage();
        }
    }
}
