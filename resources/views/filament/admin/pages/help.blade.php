<x-filament-panels::page>

    {{-- ══ STATUS DO SISTEMA ══════════════════════════════════════════════════ --}}
    <x-filament::section>
        <x-slot name="heading">Status do Sistema</x-slot>
        <x-slot name="description">Verifique a conectividade com o Ollama e os modelos necessários</x-slot>
        <x-slot name="headerEnd">
            <x-filament::button
                wire:click="checkStatus"
                wire:loading.attr="disabled"
                color="gray"
                size="sm"
                icon="heroicon-o-arrow-path"
            >
                <span wire:loading.remove wire:target="checkStatus">Verificar</span>
                <span wire:loading wire:target="checkStatus">Verificando...</span>
            </x-filament::button>
        </x-slot>

        <div class="grid gap-3 sm:grid-cols-3">

            <div class="neo-border shadow-neo-sm p-4 {{ $ollamaConnected ? 'bg-neo-green' : 'bg-neo-salmon' }}">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2.5 h-2.5 border-2 border-neo-black {{ $ollamaConnected ? 'bg-neo-black' : 'bg-neo-white' }}"></span>
                    <span class="font-heading text-xs font-bold uppercase tracking-wider">Ollama Server</span>
                </div>
                <p class="font-mono text-xs font-bold">{{ $ollamaConnected ? '✓ Conectado' : '✕ Desconectado' }}</p>
                <p class="font-mono text-xs opacity-60 mt-0.5">{{ config('ai.providers.ollama.url', 'http://localhost:11434') }}</p>
            </div>

            <div class="neo-border shadow-neo-sm p-4 {{ $embeddingModelAvailable ? 'bg-neo-green' : 'bg-neo-yellow' }}">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2.5 h-2.5 border-2 border-neo-black {{ $embeddingModelAvailable ? 'bg-neo-black' : 'bg-neo-white' }}"></span>
                    <span class="font-heading text-xs font-bold uppercase tracking-wider">Embedding</span>
                </div>
                <p class="font-mono text-xs font-bold">{{ $embeddingModelAvailable ? '✓ Disponível' : '✕ Não encontrado' }}</p>
                <p class="font-mono text-xs opacity-60 mt-0.5">{{ config('rag.embedding_model', 'nomic-embed-text') }}</p>
            </div>

            <div class="neo-border shadow-neo-sm p-4 {{ $chatModelAvailable ? 'bg-neo-green' : 'bg-neo-yellow' }}">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2.5 h-2.5 border-2 border-neo-black {{ $chatModelAvailable ? 'bg-neo-black' : 'bg-neo-white' }}"></span>
                    <span class="font-heading text-xs font-bold uppercase tracking-wider">Chat Model</span>
                </div>
                <p class="font-mono text-xs font-bold">{{ $chatModelAvailable ? '✓ Disponível' : '✕ Não encontrado' }}</p>
                <p class="font-mono text-xs opacity-60 mt-0.5">{{ config('rag.chat_model', 'llama3.2:3b') }}</p>
            </div>
        </div>

        @if($ollamaError)
            <div class="mt-3 neo-border-sm bg-neo-magenta p-3 font-mono text-xs font-bold">✕ {{ $ollamaError }}</div>
        @endif

        @if(count($availableModels) > 0)
            <div class="mt-4 flex flex-wrap gap-2 items-center">
                <span class="font-heading text-xs uppercase tracking-wider opacity-60">Modelos instalados:</span>
                @foreach($availableModels as $model)
                    <x-filament::badge color="primary">{{ $model }}</x-filament::badge>
                @endforeach
            </div>
        @endif
    </x-filament::section>

    {{-- ══ CONFIGURAÇÃO DO AMBIENTE ════════════════════════════════════════════ --}}
    <x-filament::section collapsible collapsed>
        <x-slot name="heading">Configuração do Ambiente</x-slot>
        <x-slot name="description">Variáveis .env e parâmetros RAG</x-slot>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <p class="font-heading text-xs uppercase tracking-widest mb-3 opacity-60">Conexão</p>
                <x-neo.code-window lang="ini" title=".env">
OLLAMA_BASE_URL={{ config('ai.providers.ollama.url', 'http://localhost:11434') }}
                </x-neo.code-window>
            </div>
            <div>
                <p class="font-heading text-xs uppercase tracking-widest mb-3 opacity-60">Parâmetros RAG</p>
                <x-neo.code-window lang="ini" title=".env">
RAG_CHUNK_SIZE={{ config('rag.chunk_size', 512) }}
RAG_CHUNK_OVERLAP={{ config('rag.chunk_overlap', 50) }}
RAG_SIMILARITY_THRESHOLD={{ config('rag.similarity_threshold', 0.5) }}
RAG_TOP_K={{ config('rag.top_k', 10) }}
RAG_EMBEDDING_MODEL={{ config('rag.embedding_model', 'nomic-embed-text') }}
RAG_CHAT_MODEL={{ config('rag.chat_model', 'llama3.2:3b') }}
                </x-neo.code-window>
            </div>
        </div>
    </x-filament::section>

    {{-- ══ GUIA DE SETUP ══════════════════════════════════════════════════════ --}}
    <x-filament::section collapsible collapsed>
        <x-slot name="heading">Guia de Instalação</x-slot>
        <x-slot name="description">Passo a passo para configurar o Ollama e os modelos</x-slot>

        <div class="space-y-5">

            {{-- Step 1 --}}
            <div class="neo-border shadow-neo overflow-hidden">
                <div class="bg-neo-teal border-b-4 border-neo-black px-5 py-3 flex items-center gap-3">
                    <span class="bg-neo-black text-neo-white font-heading text-xs font-bold px-2 py-0.5">01</span>
                    <h3 class="font-heading text-sm uppercase tracking-wider">Instalar o Ollama</h3>
                </div>
                <div class="p-5 bg-neo-white space-y-3">
                    <p class="font-mono text-xs text-gray-600 leading-relaxed">
                        Faça o download em <strong>ollama.com</strong> e inicie o servidor:
                    </p>
                    <x-neo.code-window lang="bash" title="terminal">
# Linux / macOS
curl -fsSL https://ollama.com/install.sh | sh

# Iniciar o servidor
ollama serve
                    </x-neo.code-window>
                </div>
            </div>

            {{-- Step 2 --}}
            <div class="neo-border shadow-neo overflow-hidden">
                <div class="bg-neo-yellow border-b-4 border-neo-black px-5 py-3 flex items-center gap-3">
                    <span class="bg-neo-black text-neo-white font-heading text-xs font-bold px-2 py-0.5">02</span>
                    <h3 class="font-heading text-sm uppercase tracking-wider">Baixar os Modelos</h3>
                </div>
                <div class="p-5 bg-neo-white space-y-3">
                    <p class="font-mono text-xs text-gray-600 leading-relaxed">
                        São necessários dois modelos: embeddings e chat.
                    </p>
                    <x-neo.code-window lang="bash" title="terminal">
# Embedding (~275 MB — 768 dims)
ollama pull {{ config('rag.embedding_model', 'nomic-embed-text') }}

# Chat (~2 GB — 3B params)
ollama pull {{ config('rag.chat_model', 'llama3.2:3b') }}
                    </x-neo.code-window>
                    <div class="grid sm:grid-cols-2 gap-3 mt-1">
                        <div class="neo-border-sm bg-neo-bg p-3 shadow-neo-xs">
                            <p class="font-heading text-xs uppercase tracking-wider mb-1">nomic-embed-text</p>
                            <p class="font-mono text-xs opacity-60">768 dims · ~275 MB · semântico</p>
                        </div>
                        <div class="neo-border-sm bg-neo-bg p-3 shadow-neo-xs">
                            <p class="font-heading text-xs uppercase tracking-wider mb-1">llama3.2:3b</p>
                            <p class="font-mono text-xs opacity-60">3B params · ~2 GB · conversacional</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3 --}}
            <div class="neo-border shadow-neo overflow-hidden">
                <div class="bg-neo-magenta border-b-4 border-neo-black px-5 py-3 flex items-center gap-3">
                    <span class="bg-neo-black text-neo-white font-heading text-xs font-bold px-2 py-0.5">03</span>
                    <h3 class="font-heading text-sm uppercase tracking-wider">Verificar Instalação</h3>
                </div>
                <div class="p-5 bg-neo-white space-y-3">
                    <p class="font-mono text-xs text-gray-600 leading-relaxed">
                        Confirme que os modelos estão disponíveis:
                    </p>
                    <x-neo.code-window lang="bash" title="terminal">
# Listar modelos instalados
ollama list

# Testar servidor
curl http://localhost:11434
# → Ollama is running
                    </x-neo.code-window>
                </div>
            </div>

            {{-- Step 4 — Laravel/AI config --}}
            <div class="neo-border shadow-neo overflow-hidden">
                <div class="bg-neo-green border-b-4 border-neo-black px-5 py-3 flex items-center gap-3">
                    <span class="bg-neo-black text-neo-white font-heading text-xs font-bold px-2 py-0.5">04</span>
                    <h3 class="font-heading text-sm uppercase tracking-wider">Laravel/AI Configuration</h3>
                </div>
                <div class="p-5 bg-neo-white space-y-3">
                    <p class="font-mono text-xs text-gray-600 leading-relaxed">
                        O arquivo <code class="bg-neo-bg neo-border-sm px-1 font-mono text-xs">config/ai.php</code> já está configurado para Ollama:
                    </p>
                    <x-neo.code-window lang="php" title="config/ai.php">
'default'               => 'ollama',
'default_for_embeddings' => 'ollama',

'providers' => [
    'ollama' => [
        'driver' => 'ollama',
        'url'    => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
    ],
],
                    </x-neo.code-window>
                </div>
            </div>

        </div>
    </x-filament::section>

    {{-- ══ TROUBLESHOOTING ════════════════════════════════════════════════════ --}}
    <x-filament::section collapsible collapsed>
        <x-slot name="heading">Troubleshooting</x-slot>
        <x-slot name="description">Problemas comuns e como resolvê-los</x-slot>

        <div class="neo-border overflow-hidden shadow-neo">
            <div class="grid grid-cols-2 bg-neo-bg border-b-4 border-neo-black">
                <div class="font-heading text-xs uppercase tracking-widest px-5 py-3 border-r-2 border-neo-black">Problema</div>
                <div class="font-heading text-xs uppercase tracking-widest px-5 py-3">Solução</div>
            </div>
            @php
            $issues = [
                ['Connection refused',     'Inicie o Ollama: <code class="bg-neo-teal neo-border-sm px-1 font-mono text-xs">ollama serve</code>'],
                ['Model not found',        'Baixe: <code class="bg-neo-teal neo-border-sm px-1 font-mono text-xs">ollama pull &lt;model&gt;</code>'],
                ['Slow responses',         'llama3.2:3b precisa ~2-3 GB de RAM. Verifique memória disponível.'],
                ['Embedding errors',       'nomic-embed-text: ~300 MB. Confirme com <code class="bg-neo-teal neo-border-sm px-1 font-mono text-xs">ollama list</code>'],
                ['Wrong OLLAMA_BASE_URL',  'Padrão: <code class="bg-neo-teal neo-border-sm px-1 font-mono text-xs">http://localhost:11434</code>. Ajuste no <code class="bg-neo-yellow neo-border-sm px-1 font-mono text-xs">.env</code>'],
                ['pgvector não encontrado','Execute: <code class="bg-neo-teal neo-border-sm px-1 font-mono text-xs">CREATE EXTENSION vector;</code> no PostgreSQL'],
            ];
            @endphp
            @foreach($issues as $i => [$problem, $solution])
            <div class="grid grid-cols-2 border-b-2 border-neo-black last:border-b-0 {{ $i % 2 === 0 ? 'bg-neo-white' : 'bg-neo-bg' }} hover:bg-neo-yellow transition-colors">
                <div class="px-5 py-3 border-r-2 border-neo-black font-mono text-xs font-bold">{{ $problem }}</div>
                <div class="px-5 py-3 font-mono text-xs">{!! $solution !!}</div>
            </div>
            @endforeach
        </div>
    </x-filament::section>

</x-filament-panels::page>
