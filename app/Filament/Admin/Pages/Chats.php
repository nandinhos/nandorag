<?php

namespace App\Filament\Admin\Pages;

use App\Agents\ChatAgent;
use App\Enums\ChatMessageRole;
use App\Models\Chat;
use App\Models\Document;
use App\Services\RagRetrievalService;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Enums\Lab;
use Livewire\Attributes\Computed;

class Chats extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.admin.pages.chats';

    public ?int $activeChatId = null;
    public string $messageInput = '';
    public string $newChatTitle = 'New Chat';
    public ?int $newChatDocumentId = null;
    public bool $showNewChatModal = false;
    public bool $isStreaming = false;
    public ?string $editingTitle = null;
    public ?int $editingChatId = null;

    public function mount(): void
    {
        $chat = auth()->user()->chats()->latest()->first();
        $this->activeChatId = $chat?->id;
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
            $systemPrompt = "You are a helpful assistant that answers questions based on provided document excerpts. "
                . "Always base your answers on the provided context. If the context doesn't contain relevant information, say so.\n\n"
                . $context;

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

            $response = $agent->prompt(
                $message,
                provider: Lab::Ollama,
                model: config('rag.chat_model', 'llama3.2:3b'),
            );

            $this->activeChat->messages()->create([
                'role' => ChatMessageRole::Assistant,
                'content' => $response->text,
                'sources' => $sources,
            ]);
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
