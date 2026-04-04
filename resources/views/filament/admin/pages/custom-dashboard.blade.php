<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="border-4 border-black bg-white p-6 shadow-neo" style="box-shadow: 4px 4px 0 #000;">
        <h1 class="font-heading text-3xl font-bold uppercase tracking-wider">Dashboard</h1>
        <p class="font-mono text-sm text-gray-600 mt-1">Visão geral do seu assistente de IA</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="border-4 border-black bg-neo-teal p-6 shadow-neo" style="box-shadow: 4px 4px 0 #000;">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="font-heading text-sm uppercase tracking-wider">Documents</span>
            </div>
            <div class="text-4xl font-heading font-bold">{{ $this->documentsCount }}</div>
            <div class="font-mono text-xs text-gray-700 mt-1">Arquivos carregados</div>
        </div>

        <div class="border-4 border-black bg-neo-yellow p-6 shadow-neo" style="box-shadow: 4px 4px 0 #000;">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="font-heading text-sm uppercase tracking-wider">Chats</span>
            </div>
            <div class="text-4xl font-heading font-bold">{{ $this->chatsCount }}</div>
            <div class="font-mono text-xs text-gray-700 mt-1">Conversas ativas</div>
        </div>

        <div class="border-4 border-black bg-neo-magenta p-6 shadow-neo" style="box-shadow: 4px 4px 0 #000;">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                <span class="font-heading text-sm uppercase tracking-wider">Chunks</span>
            </div>
            <div class="text-4xl font-heading font-bold">{{ $this->chunksCount }}</div>
            <div class="font-mono text-xs text-gray-700 mt-1">Fragmentos indexados</div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="border-4 border-black bg-white p-6 shadow-neo" style="box-shadow: 4px 4px 0 #000;">
        <h2 class="font-heading text-xl font-bold uppercase tracking-wider mb-4 border-b-2 border-black pb-2">
            Ações Rápidas
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="/admin/documents/create" class="border-3 border-black bg-neo-teal p-4 text-center hover:bg-neo-yellow transition-all hover:-translate-y-1 shadow-neo" style="box-shadow: 3px 3px 0 #000;">
                <svg class="w-8 h-8 mx-auto mb-2 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="font-heading text-xs uppercase">Novo Documento</span>
            </a>
            <a href="/admin/chats" class="border-3 border-black bg-neo-yellow p-4 text-center hover:bg-neo-teal transition-all hover:-translate-y-1 shadow-neo" style="box-shadow: 3px 3px 0 #000;">
                <svg class="w-8 h-8 mx-auto mb-2 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="font-heading text-xs uppercase">Novo Chat</span>
            </a>
            <a href="/admin/documents" class="border-3 border-black bg-neo-magenta p-4 text-center hover:bg-neo-yellow transition-all hover:-translate-y-1 shadow-neo" style="box-shadow: 3px 3px 0 #000;">
                <svg class="w-8 h-8 mx-auto mb-2 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                <span class="font-heading text-xs uppercase">Ver Docs</span>
            </a>
            <a href="/admin/help" class="border-3 border-black bg-white p-4 text-center hover:bg-neo-yellow transition-all hover:-translate-y-1 shadow-neo" style="box-shadow: 3px 3px 0 #000;">
                <svg class="w-8 h-8 mx-auto mb-2 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-heading text-xs uppercase">Ajuda</span>
            </a>
        </div>
    </div>
</div>