<x-filament-panels::page>
    <style>
        .prose table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 1rem;
            margin-bottom: 1rem;
            border: 2px solid #000;
        }
        .prose th, .prose td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .prose th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
        .prose tr:nth-child(even) {
            background-color: #f9fafb;
        }
    </style>
    <div class="flex h-[calc(100vh-12rem)] flex-col md:flex-row gap-4">
        {{-- Sidebar: Chat tabs (Hidden on mobile by default) --}}
        <div 
            x-data="{ open: false }" 
            class="w-full md:w-72 flex-shrink-0 rounded-none border-4 border-black bg-white p-4 shadow-neo overflow-y-auto md:block" 
            :class="open ? 'block' : 'hidden md:block'"
            style="box-shadow: 4px 4px 0px 0px #000000;"
        >
            <div class="flex items-center justify-between md:block">
                <h3 class="font-heading font-bold uppercase tracking-wider text-xs md:hidden mb-0">Selecionar Chat</h3>
                <button @click="open = !open" class="md:hidden border-2 border-black bg-neo-yellow px-2 py-1 text-xs font-bold uppercase tracking-wider">
                    <span x-show="!open">Abrir Chats</span>
                    <span x-show="open">Fechar Lista</span>
                </button>
            </div>

            <button 
                wire:click="openNewChatModal" 
                class="my-4 flex w-full items-center justify-center gap-2 border-2 border-black bg-neo-green px-4 py-3 text-sm font-heading font-bold uppercase tracking-wider hover:bg-neo-yellow transition-all hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-neo"
                style="box-shadow: 3px 3px 0px 0px #000000;"
            >
                <x-heroicon-m-plus class="h-4 w-4" />
                Novo Chat
            </button>

            <div class="space-y-2">
                @foreach($this->chats as $chat)
                    <div
                        wire:key="chat-{{ $chat->id }}"
                        class="group flex items-center gap-2 border-2 border-black px-3 py-2.5 text-sm cursor-pointer font-heading uppercase tracking-wider {{ $activeChatId === $chat->id ? 'bg-neo-teal font-bold' : 'bg-white hover:bg-neo-yellow' }}"
                        style="{{ $activeChatId === $chat->id ? 'box-shadow: 3px 3px 0px 0px #000000;' : 'box-shadow: 2px 2px 0px 0px #000000;' }}"
                    >
                        @if($editingChatId === $chat->id)
                            <input
                                type="text"
                                wire:model="editingTitle"
                                wire:keydown.enter="saveTitle"
                                wire:keydown.escape="$set('editingChatId', null)"
                                wire:blur="saveTitle"
                                class="w-full border-2 border-black px-2 py-1 text-sm font-mono"
                                autofocus
                            />
                        @else
                            <button wire:click="selectChat({{ $chat->id }})" class="flex-1 truncate text-left">
                                {{ $chat->title }}
                            </button>
                            <div class="hidden gap-1 group-hover:flex">
                                <button wire:click="startEditTitle({{ $chat->id }}, '{{ addslashes($chat->title) }}')" class="border-2 border-black bg-white p-1 hover:bg-neo-yellow">
                                    <x-heroicon-m-pencil class="h-3.5 w-3.5" />
                                </button>
                                <button 
                                    wire:click="mountAction('deleteChat', { chat: {{ $chat->id }} })" 
                                    class="border-2 border-black bg-white p-1 hover:bg-neo-salmon"
                                >
                                    <x-heroicon-m-x-mark class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Main chat area --}}
        <div class="flex flex-1 flex-col rounded-none border-4 border-black bg-white shadow-neo" style="box-shadow: 4px 4px 0px 0px #000000;">
            @if($this->activeChat)
                {{-- Chat header --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b-4 border-black px-5 py-4 bg-neo-bg gap-3">
                    <div class="w-full sm:w-auto">
                        <h3 class="text-sm font-heading font-bold uppercase tracking-wider text-black truncate">{{ $this->activeChat->title }}</h3>
                        @if($this->activeChat->document)
                            <p class="text-[10px] font-mono text-gray-600 mt-1">Escopo: {{ $this->activeChat->document->filename }}</p>
                        @endif
                    </div>
                    
                    {{-- Model Selector --}}
                    <div class="flex w-full sm:w-auto items-center gap-3">
                        @if($this->ollamaAvailable())
                            <label class="hidden sm:block text-xs font-heading uppercase tracking-wider text-black">Model:</label>
                            <select
                                wire:model="selectedModel"
                                class="w-full sm:w-auto border-2 border-black bg-white px-3 py-2 text-[10px] sm:text-xs font-mono focus:border-neo-magenta focus:ring-0"
                                style="box-shadow: 2px 2px 0px 0px #000000;"
                            >
                                @forelse($this->availableModels as $model)
                                    <option value="{{ $model['name'] }}">
                                        {{ $model['name'] }}
                                    </option>
                                @empty
                                    <option value="{{ $defaultModel }}">{{ $defaultModel }}</option>
                                @endforelse
                            </select>
                        @else
                            <span class="text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 text-neo-salmon border-2 border-black px-2 py-1 bg-white">
                                <x-heroicon-m-exclamation-triangle class="h-3 w-3" />
                                Offline
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Messages --}}
                <div class="flex-1 overflow-y-auto p-5 space-y-4" id="chat-messages" x-data x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)">
                    @forelse($this->activeChat->messages as $msg)
                        <div class="flex {{ $msg->role === \App\Enums\ChatMessageRole::User ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] rounded-none border-2 border-black px-4 py-3 {{ $msg->role === \App\Enums\ChatMessageRole::User ? 'bg-neo-teal text-black font-bold' : 'bg-white text-black' }}" style="{{ $msg->role === \App\Enums\ChatMessageRole::User ? 'box-shadow: 3px 3px 0px 0px #000000;' : 'box-shadow: 2px 2px 0px 0px #000000;' }}">
                                <div class="prose prose-sm max-w-none font-mono text-sm" id="chat-message-{{ $msg->id }}" wire:stream="chat-message-{{ $msg->id }}">
                                    {!! str($msg->content)->markdown() !!}
                                </div>

                                {{-- Sources --}}
                                @if($msg->sources && count($msg->sources) > 0)
                                    <details class="mt-3 border-t-2 border-black pt-2">
                                        <summary class="cursor-pointer text-xs font-heading uppercase tracking-wider font-bold hover:text-neo-magenta">
                                            {{ count($msg->sources) }} fonte(s)
                                        </summary>
                                        <div class="mt-2 space-y-2">
                                            @foreach($msg->sources as $source)
                                                <div class="border-2 border-black bg-neo-bg px-3 py-2 text-xs font-mono">
                                                    <span class="font-bold">{{ $source['source_file'] }}</span>
                                                    <span class="text-gray-600">({{ $source['source_location'] }})</span>
                                                    <p class="mt-1 text-gray-700">{{ $source['excerpt'] }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex h-full items-center justify-center text-gray-400 font-heading uppercase tracking-wider">
                            <p>Envie uma mensagem para iniciar o Chat.</p>
                        </div>
                    @endforelse

                    @if($isStreaming)
                        <div class="flex justify-start">
                            <div class="rounded-none border-2 border-black bg-white px-4 py-3" style="box-shadow: 2px 2px 0px 0px #000000;">
                                <div class="flex items-center gap-2 text-sm font-mono text-gray-600">
                                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Pensando...
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Input --}}
                <div class="border-t-4 border-black p-4 bg-neo-bg">
                    <form wire:submit="sendMessage" class="flex gap-3">
                        <input
                            type="text"
                            wire:model="messageInput"
                            placeholder="Digite sua mensagem..."
                            class="flex-1 border-2 border-black bg-white px-4 py-3 text-sm font-mono focus:border-neo-magenta focus:ring-0"
                            style="box-shadow: 2px 2px 0px 0px #000000;"
                            @if($isStreaming) disabled @endif
                            autofocus
                        />
                        <button
                            type="submit"
                            class="border-2 border-black bg-neo-green px-6 py-3 text-sm font-heading font-bold uppercase tracking-wider hover:bg-neo-yellow transition-all hover:translate-x-[-2px] hover:translate-y-[-2px] disabled:opacity-50"
                            style="box-shadow: 3px 3px 0px 0px #000000;"
                            @if($isStreaming) disabled @endif
                        >
                            Enviar
                        </button>
                    </form>
                </div>
            @else
                <div class="flex h-full items-center justify-center bg-neo-bg">
                    <div class="text-center">
                        <x-heroicon-o-chat-bubble-left-right class="mx-auto h-16 w-16 mb-4 text-black" />
                        <p class="font-heading uppercase tracking-wider text-black">Crie um novo Chat para começar.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- New Chat Modal --}}
    @if($showNewChatModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="$set('showNewChatModal', false)">
            <div class="w-full max-w-md rounded-none border-4 border-black bg-white p-6 shadow-neo-xl" style="box-shadow: 8px 8px 0px 0px #000000;">
                <h3 class="text-lg font-heading font-bold uppercase tracking-wider text-black mb-4 border-b-2 border-black pb-2">Novo Chat</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-heading uppercase tracking-wider text-black mb-2">Título</label>
                        <input
                            type="text"
                            wire:model="newChatTitle"
                            class="w-full border-2 border-black bg-white px-3 py-2 text-sm font-mono focus:border-neo-magenta focus:ring-0"
                            style="box-shadow: 2px 2px 0px 0px #000000;"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-heading uppercase tracking-wider text-black mb-2">Escopo do Documento</label>
                        <select
                            wire:model="newChatDocumentId"
                            class="w-full border-2 border-black bg-white px-3 py-2 text-sm font-mono focus:border-neo-magenta focus:ring-0"
                            style="box-shadow: 2px 2px 0px 0px #000000;"
                        >
                            <option value="">Todos os Documentos</option>
                            @foreach($this->documents as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->filename }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        wire:click="$set('showNewChatModal', false)"
                        class="border-2 border-black bg-white px-4 py-2 text-sm font-heading uppercase tracking-wider hover:bg-neo-yellow transition-all"
                        style="box-shadow: 2px 2px 0px 0px #000000;"
                    >
                        Cancelar
                    </button>
                    <button
                        wire:click="createChat"
                        class="border-2 border-black bg-neo-green px-4 py-2 text-sm font-heading uppercase tracking-wider hover:bg-neo-yellow transition-all hover:translate-x-[-2px] hover:translate-y-[-2px]"
                        style="box-shadow: 3px 3px 0px 0px #000000;"
                    >
                        Criar
                    </button>
                </div>
            </div>
        </div>
        @endif

        <x-filament-actions::modals />
        </x-filament-panels::page>