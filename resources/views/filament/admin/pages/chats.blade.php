<x-filament-panels::page>
    <div class="flex h-[calc(100vh-12rem)] gap-4">
        {{-- Sidebar: Chat tabs --}}
        <div class="w-64 flex-shrink-0 rounded-xl bg-white p-3 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-y-auto">
            <button wire:click="openNewChatModal" class="mb-3 flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 px-3 py-2 text-sm font-medium text-white hover:bg-primary-500">
                <x-heroicon-m-plus class="h-4 w-4" />
                New Chat
            </button>

            <div class="space-y-1">
                @foreach($this->chats as $chat)
                    <div
                        wire:key="chat-{{ $chat->id }}"
                        class="group flex items-center gap-2 rounded-lg px-3 py-2 text-sm cursor-pointer {{ $activeChatId === $chat->id ? 'bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5' }}"
                    >
                        @if($editingChatId === $chat->id)
                            <input
                                type="text"
                                wire:model="editingTitle"
                                wire:keydown.enter="saveTitle"
                                wire:keydown.escape="$set('editingChatId', null)"
                                wire:blur="saveTitle"
                                class="w-full rounded border-gray-300 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-800"
                                autofocus
                            />
                        @else
                            <button wire:click="selectChat({{ $chat->id }})" class="flex-1 truncate text-left">
                                {{ $chat->title }}
                            </button>
                            <div class="hidden gap-1 group-hover:flex">
                                <button wire:click="startEditTitle({{ $chat->id }}, '{{ addslashes($chat->title) }}')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <x-heroicon-m-pencil class="h-3.5 w-3.5" />
                                </button>
                                <button wire:click="deleteChat({{ $chat->id }})" wire:confirm="Delete this chat?" class="text-gray-400 hover:text-red-500">
                                    <x-heroicon-m-x-mark class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Main chat area --}}
        <div class="flex flex-1 flex-col rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            @if($this->activeChat)
                {{-- Chat header --}}
                <div class="flex items-center justify-between border-b px-4 py-3 dark:border-gray-700">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $this->activeChat->title }}</h3>
                        @if($this->activeChat->document)
                            <p class="text-xs text-gray-500">Scoped to: {{ $this->activeChat->document->filename }}</p>
                        @else
                            <p class="text-xs text-gray-500">All documents</p>
                        @endif
                    </div>
                </div>

                {{-- Messages --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chat-messages" x-data x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)" wire:poll.visible.5s>
                    @forelse($this->activeChat->messages as $msg)
                        <div class="flex {{ $msg->role === \App\Enums\ChatMessageRole::User ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] rounded-2xl px-4 py-2.5 {{ $msg->role === \App\Enums\ChatMessageRole::User ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100' }}">
                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                    {!! str($msg->content)->markdown() !!}
                                </div>

                                {{-- Sources --}}
                                @if($msg->sources && count($msg->sources) > 0)
                                    <details class="mt-2 border-t {{ $msg->role === \App\Enums\ChatMessageRole::User ? 'border-primary-500' : 'border-gray-200 dark:border-gray-700' }} pt-2">
                                        <summary class="cursor-pointer text-xs opacity-70">
                                            {{ count($msg->sources) }} source(s)
                                        </summary>
                                        <div class="mt-1 space-y-1">
                                            @foreach($msg->sources as $source)
                                                <div class="rounded bg-black/5 px-2 py-1 text-xs dark:bg-white/5">
                                                    <span class="font-medium">{{ $source['source_file'] }}</span>
                                                    <span class="opacity-70">({{ $source['source_location'] }})</span>
                                                    <p class="mt-0.5 opacity-60">{{ $source['excerpt'] }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex h-full items-center justify-center text-gray-400">
                            <p>Send a message to start chatting.</p>
                        </div>
                    @endforelse

                    @if($isStreaming)
                        <div class="flex justify-start">
                            <div class="rounded-2xl bg-gray-100 px-4 py-2.5 dark:bg-gray-800">
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Thinking...
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Input --}}
                <div class="border-t p-4 dark:border-gray-700">
                    <form wire:submit="sendMessage" class="flex gap-2">
                        <input
                            type="text"
                            wire:model="messageInput"
                            placeholder="Type your message..."
                            class="flex-1 rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            @if($isStreaming) disabled @endif
                            autofocus
                        />
                        <button
                            type="submit"
                            class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-500 disabled:opacity-50"
                            @if($isStreaming) disabled @endif
                        >
                            Send
                        </button>
                    </form>
                </div>
            @else
                <div class="flex h-full items-center justify-center text-gray-400">
                    <div class="text-center">
                        <x-heroicon-o-chat-bubble-left-right class="mx-auto h-12 w-12 mb-3" />
                        <p>Create a new chat to get started.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- New Chat Modal --}}
    @if($showNewChatModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="$set('showNewChatModal', false)">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">New Chat</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <input
                            type="text"
                            wire:model="newChatTitle"
                            class="w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Document Scope</label>
                        <select
                            wire:model="newChatDocumentId"
                            class="w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                            <option value="">All Documents</option>
                            @foreach($this->documents as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->filename }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button
                        wire:click="$set('showNewChatModal', false)"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Cancel
                    </button>
                    <button
                        wire:click="createChat"
                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-500"
                    >
                        Create
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
