<?php ?>

<div class="space-y-0">

    <div class="p-6 space-y-5">

    {{-- ══ PAGE TITLE BLOCK ═════════════════════════════════════════════════ --}}
    <div class="neo-border shadow-neo bg-neo-white p-5 flex items-end justify-between gap-4 flex-wrap">
        <div>
            <h1 class="font-heading text-4xl uppercase tracking-wide leading-none">Dashboard</h1>
            <p class="font-mono text-xs text-gray-500 mt-1">Visão geral do seu assistente de IA pessoal</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="/admin/chats"
               class="btn-neo bg-neo-teal font-heading text-xs uppercase tracking-wider px-5 py-2.5 neo-border-sm shadow-neo-sm">
                Abrir Chat →
            </a>
            <a href="/admin/documents"
               class="btn-neo bg-neo-white font-heading text-xs uppercase tracking-wider px-5 py-2.5 neo-border-sm shadow-neo-sm">
                Ver Documentos
            </a>
        </div>
    </div>

    {{-- ══ STAT CARDS — 4 coloridos ═════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

        <div class="neo-border shadow-neo bg-neo-teal p-5 flex flex-col gap-2">
            <p class="font-heading text-xs uppercase tracking-widest opacity-60">Documentos</p>
            <p class="font-heading text-6xl font-bold leading-none">{{ $documentsCount }}</p>
            <div class="border-t-2 border-neo-black pt-2 mt-auto flex items-center justify-between">
                <span class="font-mono text-xs">Arquivos carregados</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        <div class="neo-border shadow-neo bg-neo-yellow p-5 flex flex-col gap-2">
            <p class="font-heading text-xs uppercase tracking-widest opacity-60">Chats</p>
            <p class="font-heading text-6xl font-bold leading-none">{{ $chatsCount }}</p>
            <div class="border-t-2 border-neo-black pt-2 mt-auto flex items-center justify-between">
                <span class="font-mono text-xs">Conversas abertas</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
        </div>

        <div class="neo-border shadow-neo bg-neo-magenta p-5 flex flex-col gap-2">
            <p class="font-heading text-xs uppercase tracking-widest opacity-60">Chunks</p>
            <p class="font-heading text-6xl font-bold leading-none">{{ $chunksCount }}</p>
            <div class="border-t-2 border-neo-black pt-2 mt-auto flex items-center justify-between">
                <span class="font-mono text-xs">Fragmentos indexados</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
            </div>
        </div>

        <div class="neo-border shadow-neo bg-neo-green p-5 flex flex-col gap-2">
            <p class="font-heading text-xs uppercase tracking-widest opacity-60">Mensagens</p>
            <p class="font-heading text-6xl font-bold leading-none">{{ $messagesCount }}</p>
            <div class="border-t-2 border-neo-black pt-2 mt-auto flex items-center justify-between">
                <span class="font-mono text-xs">Trocadas com a IA</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- ══ MIDDLE ROW: status + chats recentes ══════════════════════════════ --}}
    <div class="grid lg:grid-cols-3 gap-3">

        {{-- STATUS DOS DOCUMENTOS --}}
        <div class="neo-border shadow-neo bg-neo-white overflow-hidden">

            {{-- header --}}
            <div class="bg-neo-purple border-b-4 border-neo-black px-5 py-3 flex items-center gap-2">
                <span class="bg-neo-black text-neo-purple font-heading text-xs font-bold uppercase tracking-widest px-2 py-0.5">01</span>
                <h2 class="font-heading text-sm uppercase tracking-wider text-neo-white">Status dos Docs</h2>
            </div>

            {{-- 2x2 status grid --}}
            <div class="grid grid-cols-2">
                <div class="bg-neo-green border-r-2 border-b-2 border-neo-black p-4">
                    <p class="font-heading text-xs uppercase tracking-wider opacity-60 mb-1">Concluídos</p>
                    <p class="font-heading text-4xl font-bold leading-none">{{ $docsCompleted }}</p>
                </div>
                <div class="bg-neo-yellow border-b-2 border-neo-black p-4">
                    <p class="font-heading text-xs uppercase tracking-wider opacity-60 mb-1">Pendentes</p>
                    <p class="font-heading text-4xl font-bold leading-none">{{ $docsPending }}</p>
                </div>
                <div class="bg-neo-salmon border-r-2 border-neo-black p-4">
                    <p class="font-heading text-xs uppercase tracking-wider opacity-60 mb-1">Com Erro</p>
                    <p class="font-heading text-4xl font-bold leading-none">{{ $docsFailed }}</p>
                </div>
                <div class="bg-neo-teal p-4">
                    <p class="font-heading text-xs uppercase tracking-wider opacity-60 mb-1">Modelos IA</p>
                    <p class="font-heading text-4xl font-bold leading-none">{{ count($availableModels) ?: '–' }}</p>
                </div>
            </div>

            {{-- Ollama pill --}}
            <div class="bg-neo-bg border-t-2 border-neo-black px-5 py-2.5 flex items-center gap-2">
                <span class="w-2.5 h-2.5 border-2 border-neo-black {{ $ollamaOk ? 'bg-neo-green' : 'bg-neo-magenta' }}"></span>
                <span class="font-heading text-xs font-bold uppercase tracking-wider">
                    Ollama: {{ $ollamaOk ? 'Online' : 'Offline' }}
                </span>
                @if($ollamaOk && count($availableModels) > 0)
                    @php $modelNames = array_column($availableModels, 'name'); @endphp
                    <span class="font-mono text-xs text-gray-500 ml-auto truncate">
                        {{ implode(', ', array_slice($modelNames, 0, 2)) }}{{ count($modelNames) > 2 ? ' +' . (count($modelNames) - 2) : '' }}
                    </span>
                @endif
            </div>
        </div>

        {{-- CHATS RECENTES --}}
        <div class="lg:col-span-2 neo-border shadow-neo bg-neo-white overflow-hidden flex flex-col">

            {{-- header --}}
            <div class="bg-neo-magenta border-b-4 border-neo-black px-5 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="bg-neo-black text-neo-magenta font-heading text-xs font-bold uppercase tracking-widest px-2 py-0.5">02</span>
                    <h2 class="font-heading text-sm uppercase tracking-wider">Chats Recentes</h2>
                </div>
                <a href="/admin/chats"
                   class="btn-neo bg-neo-white font-heading text-xs uppercase tracking-wider px-3 py-1 neo-border-sm shadow-neo-xs">
                    Ver Todos →
                </a>
            </div>

            @if($recentChats->isEmpty())
                <div class="flex-1 flex flex-col items-center justify-center p-8 m-4 border-2 border-dashed border-neo-black">
                    <p class="font-heading text-sm uppercase opacity-40 mb-3">Nenhum chat ainda</p>
                    <a href="/admin/chats" class="btn-neo bg-neo-teal font-heading text-xs uppercase px-4 py-2 neo-border-sm shadow-neo-xs">
                        Iniciar Conversa
                    </a>
                </div>
            @else
                @php
                    $badgeColors = ['bg-neo-teal','bg-neo-yellow','bg-neo-magenta','bg-neo-green','bg-neo-salmon','bg-neo-purple'];
                @endphp
                <div class="divide-y-2 divide-neo-black flex-1">
                    @foreach($recentChats as $i => $chat)
                    <a href="/admin/chats"
                       class="flex items-center gap-3 px-5 py-3.5 hover:bg-neo-bg transition-colors text-neo-black no-underline">
                        <span class="{{ $badgeColors[$i % 6] }} neo-border-sm w-7 h-7 flex items-center justify-center font-heading text-xs font-bold flex-shrink-0 shadow-neo-xs">
                            {{ $i + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-heading text-sm uppercase truncate">{{ $chat->title ?: 'Sem título' }}</p>
                            <p class="font-mono text-xs text-gray-500 mt-0.5 truncate">
                                @if($chat->document) 📄 {{ $chat->document->filename }} · @endif
                                {{ $chat->updated_at->diffForHumans() }}
                            </p>
                        </div>
                        <span class="bg-neo-black text-neo-white font-heading text-xs px-2 py-0.5 flex-shrink-0">
                            {{ $chat->messages_count }} msg
                        </span>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ══ TABELA DE DOCUMENTOS RECENTES ════════════════════════════════════ --}}
    <div class="neo-border shadow-neo bg-neo-white overflow-hidden">

        {{-- header --}}
        <div class="bg-neo-yellow border-b-4 border-neo-black px-5 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="bg-neo-black text-neo-yellow font-heading text-xs font-bold uppercase tracking-widest px-2 py-0.5">03</span>
                <h2 class="font-heading text-sm uppercase tracking-wider">Documentos Recentes</h2>
            </div>
            <a href="/admin/documents"
               class="btn-neo bg-neo-white font-heading text-xs uppercase tracking-wider px-3 py-1 neo-border-sm shadow-neo-xs">
                Ver Todos →
            </a>
        </div>

        {{-- table --}}
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-neo-bg border-b-4 border-neo-black">
                        <th class="font-heading text-xs font-bold uppercase tracking-widest text-left px-5 py-3 border-r-2 border-neo-black w-10">#</th>
                        <th class="font-heading text-xs font-bold uppercase tracking-widest text-left px-5 py-3 border-r-2 border-neo-black">Arquivo</th>
                        <th class="font-heading text-xs font-bold uppercase tracking-widest text-left px-5 py-3 border-r-2 border-neo-black hidden md:table-cell">Tipo</th>
                        <th class="font-heading text-xs font-bold uppercase tracking-widest text-center px-5 py-3 border-r-2 border-neo-black">Chunks</th>
                        <th class="font-heading text-xs font-bold uppercase tracking-widest text-center px-5 py-3 border-r-2 border-neo-black">Status</th>
                        <th class="font-heading text-xs font-bold uppercase tracking-widest text-left px-5 py-3">Adicionado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentDocuments as $i => $doc)
                    @php
                        $rowBg     = $i % 2 === 0 ? 'bg-neo-white' : 'bg-neo-bg';
                        $ext       = strtoupper(pathinfo($doc->filename, PATHINFO_EXTENSION) ?: 'DOC');
                        $extClass  = match($ext) {
                            'PDF' => 'bg-neo-salmon', 'MD' => 'bg-neo-teal',
                            'TXT' => 'bg-neo-yellow', default => 'bg-neo-magenta',
                        };
                        $statusVal = $doc->status instanceof \App\Enums\DocumentStatus
                            ? $doc->status->value
                            : (string) $doc->status;
                        [$statusBg, $statusLabel] = match($statusVal) {
                            'completed'  => ['bg-neo-green',   'CONCLUÍDO'],
                            'pending'    => ['bg-neo-yellow',  'PENDENTE'],
                            'processing' => ['bg-neo-teal',    'PROCESSANDO'],
                            'failed'     => ['bg-neo-magenta', 'ERRO'],
                            default      => ['bg-neo-white',   strtoupper($statusVal)],
                        };
                    @endphp
                    <tr class="{{ $rowBg }} border-b-2 border-neo-black hover:bg-neo-yellow transition-colors">
                        <td class="font-mono text-xs px-5 py-3 border-r-2 border-neo-black opacity-50 font-bold">
                            {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-5 py-3 border-r-2 border-neo-black max-w-xs">
                            <div class="flex items-center gap-2">
                                <span class="{{ $extClass }} neo-border-sm font-heading text-xs font-bold px-1.5 py-0.5 flex-shrink-0 shadow-neo-xs">
                                    {{ $ext }}
                                </span>
                                <span class="font-mono text-xs truncate" title="{{ $doc->filename }}">
                                    {{ $doc->filename }}
                                </span>
                            </div>
                        </td>
                        <td class="font-mono text-xs px-5 py-3 border-r-2 border-neo-black opacity-60 hidden md:table-cell whitespace-nowrap">
                            {{ $doc->mime_type ?: '—' }}
                        </td>
                        <td class="px-5 py-3 border-r-2 border-neo-black text-center">
                            <span class="font-heading text-base font-bold">{{ $doc->chunks_count }}</span>
                        </td>
                        <td class="px-5 py-3 border-r-2 border-neo-black text-center">
                            <span class="{{ $statusBg }} neo-border-sm font-heading text-xs font-bold uppercase tracking-wider px-2 py-0.5 shadow-neo-xs">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="font-mono text-xs px-5 py-3 whitespace-nowrap" title="{{ $doc->created_at->format('d/m/Y H:i') }}">
                            {{ $doc->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <p class="font-heading text-sm uppercase opacity-35 mb-3">Nenhum documento carregado ainda</p>
                            <a href="/admin/documents/create"
                               class="btn-neo bg-neo-teal font-heading text-xs uppercase px-5 py-2 neo-border-sm shadow-neo-sm">
                                Adicionar Primeiro Documento
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- footer --}}
        <div class="bg-neo-bg border-t-2 border-neo-black px-5 py-2.5 flex items-center justify-between">
            <span class="font-mono text-xs opacity-50">
                Mostrando {{ $recentDocuments->count() }} de {{ $documentsCount }} documentos
            </span>
            <a href="/admin/documents" class="font-heading text-xs font-bold uppercase tracking-wider underline underline-offset-2 hover:text-neo-teal transition-colors">
                Ver Todos →
            </a>
        </div>
    </div>

    </div>{{-- /p-6 --}}
</div>
