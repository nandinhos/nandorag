<?php

namespace App\Filament\Admin\Pages;

use App\Agents\ChatAgent;
use App\Enums\ChatMessageRole;
use App\Models\Chat;
use App\Models\Document;
use App\Services\OllamaService;
use App\Services\RagRetrievalService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Enums\Lab;
use Livewire\Attributes\Computed;

class Chats extends Page implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?int $navigationSort = 2;

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

    protected string $view = 'filament.admin.pages.chats';

    public ?int $activeChatId = null;

    public string $messageInput = '';

    public string $newChatTitle = 'New Chat';

    public ?int $newChatDocumentId = null;

    public bool $showNewChatModal = false;

    public bool $isStreaming = false;

    public ?string $editingTitle = null;

    public ?int $editingChatId = null;

    public string $selectedModel = '';

    public function mount(): void
    {
        $chat = auth()->user()->chats()->latest()->first();
        $this->activeChatId = $chat?->id;
        $this->selectedModel = config('rag.chat_model', 'llama3.2:3b');
    }

    #[Computed]
    public function chats()
    {
        return auth()->user()->chats()->latest()->get();
    }

    #[Computed]
    public function activeChat()
    {
        return $this->activeChatId
            ? Chat::with('messages', 'document')->find($this->activeChatId)
            : null;
    }

    #[Computed]
    public function documents()
    {
        return Document::where('status', 'completed')->orderBy('filename')->get();
    }

    #[Computed]
    public function availableModels(): array
    {
        $ollamaService = app(OllamaService::class);

        return $ollamaService->getModels();
    }

    #[Computed]
    public function ollamaAvailable(): bool
    {
        return app(OllamaService::class)->isAvailable();
    }

    #[Computed]
    public function defaultModel(): string
    {
        return config('rag.chat_model', 'llama3.2:3b');
    }

    protected function getActions(): array
    {
        return [
            $this->deleteChatAction(),
        ];
    }

    public function deleteChatAction(): Action
    {
        return Action::make('deleteChat')
            ->requiresConfirmation()
            ->modalHeading('Excluir Conversa')
            ->modalDescription('Tem certeza que deseja apagar esta conversa? Esta ação não pode ser desfeita.')
            ->modalSubmitActionLabel('Confirmar Exclusão')
            ->modalIcon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->action(fn (array $arguments) => $this->deleteChat($arguments['chat']));
    }

    public function selectChat(int $chatId): void
    {
        $this->activeChatId = $chatId;
        unset($this->activeChat);
    }

    public function openNewChatModal(): void
    {
        $this->newChatTitle = 'New Chat';
        $this->newChatDocumentId = null;
        $this->showNewChatModal = true;
    }

    public function createChat(): void
    {
        $chat = auth()->user()->chats()->create([
            'title' => $this->newChatTitle,
            'document_id' => $this->newChatDocumentId,
        ]);

        $this->activeChatId = $chat->id;
        $this->showNewChatModal = false;
        unset($this->chats, $this->activeChat);
    }

    public function deleteChat(int $chatId): void
    {
        Chat::where('id', $chatId)
            ->where('user_id', auth()->id())
            ->delete();

        if ($this->activeChatId === $chatId) {
            $this->activeChatId = auth()->user()->chats()->latest()->first()?->id;
        }

        unset($this->chats, $this->activeChat);
    }

    public function startEditTitle(int $chatId, string $title): void
    {
        $this->editingChatId = $chatId;
        $this->editingTitle = $title;
    }

    public function saveTitle(): void
    {
        if ($this->editingChatId && $this->editingTitle) {
            Chat::where('id', $this->editingChatId)
                ->where('user_id', auth()->id())
                ->update(['title' => $this->editingTitle]);
        }

        $this->editingChatId = null;
        $this->editingTitle = null;
        unset($this->chats);
    }

    public function sendMessage(): void
    {
        $message = trim($this->messageInput);
        if (empty($message) || ! $this->activeChat) {
            return;
        }

        $this->messageInput = '';
        $this->isStreaming = true;

        // Save user message
        $this->activeChat->messages()->create([
            'role' => ChatMessageRole::User,
            'content' => $message,
        ]);

        // RAG retrieval
        $ragService = app(RagRetrievalService::class);
        $chunks = $ragService->retrieve($message, $this->activeChat->document_id);
        $context = $ragService->buildContext($chunks);
        $sources = $ragService->formatSources($chunks);

        try {
            $systemPrompt = 'You are a helpful assistant that answers questions based on provided document excerpts. '
                ."Always base your answers on the provided context. If the context doesn't contain relevant information, say so.\n\n"
                .$context;

            // Build conversation history for context
            $history = $this->activeChat->messages()
                ->latest()
                ->take(20)
                ->get()
                ->reverse()
                ->map(fn ($msg) => [
                    'role' => $msg->role->value,
                    'content' => $msg->content,
                ])
                ->values()
                ->toArray();

            // Remove the last user message since we pass it as the prompt
            array_pop($history);

            $agent = new ChatAgent($systemPrompt);
            $agent->withMessages($history);

            $model = $this->selectedModel ?: config('rag.chat_model', 'minimax-m2.5:cloud');

            // Create placeholder for assistant response
            $assistantMessage = $this->activeChat->messages()->create([
                'role' => ChatMessageRole::Assistant,
                'content' => '',
                'sources' => $sources,
            ]);

            $fullResponse = '';

            // Handle streaming response
            $stream = $agent->stream(
                $message,
                provider: Lab::Ollama,
                model: $model,
            );

            foreach ($stream as $chunk) {
                // Remove loading state on first chunk
                if ($this->isStreaming) {
                    $this->isStreaming = false;
                }

                $fullResponse .= $chunk->text;
                
                $this->stream(
                    to: "chat-message-{$assistantMessage->id}",
                    content: str($fullResponse)->markdown()->toHtml(),
                    replace: true
                );
            }

            // Save final response
            $assistantMessage->update(['content' => $fullResponse]);

        } catch (\Throwable $e) {
            Log::error("Chat response failed: {$e->getMessage()}");

            $this->activeChat->messages()->create([
                'role' => ChatMessageRole::Assistant,
                'content' => 'Sorry, I encountered an error generating a response. Please check that Ollama is running.',
            ]);
        }

        $this->isStreaming = false;
        unset($this->activeChat);
    }
}
