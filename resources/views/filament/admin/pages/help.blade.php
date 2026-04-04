<x-filament-panels::page>
    {{-- Status Panel --}}
    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Connection Status</h2>
            <button wire:click="checkStatus" class="rounded-lg bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                Check Status
            </button>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            {{-- Ollama Server --}}
            <div class="rounded-lg border p-4 {{ $ollamaConnected ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950' }}">
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full {{ $ollamaConnected ? 'bg-green-500' : 'bg-red-500' }}"></div>
                    <span class="text-sm font-medium {{ $ollamaConnected ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                        Ollama Server
                    </span>
                </div>
                <p class="mt-1 text-xs {{ $ollamaConnected ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ $ollamaConnected ? 'Connected' : 'Disconnected' }}
                </p>
            </div>

            {{-- Embedding Model --}}
            <div class="rounded-lg border p-4 {{ $embeddingModelAvailable ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950' }}">
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full {{ $embeddingModelAvailable ? 'bg-green-500' : 'bg-red-500' }}"></div>
                    <span class="text-sm font-medium {{ $embeddingModelAvailable ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                        Embedding Model
                    </span>
                </div>
                <p class="mt-1 text-xs {{ $embeddingModelAvailable ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ config('rag.embedding_model') }}: {{ $embeddingModelAvailable ? 'Available' : 'Not Found' }}
                </p>
            </div>

            {{-- Chat Model --}}
            <div class="rounded-lg border p-4 {{ $chatModelAvailable ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950' }}">
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full {{ $chatModelAvailable ? 'bg-green-500' : 'bg-red-500' }}"></div>
                    <span class="text-sm font-medium {{ $chatModelAvailable ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                        Chat Model
                    </span>
                </div>
                <p class="mt-1 text-xs {{ $chatModelAvailable ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ config('rag.chat_model') }}: {{ $chatModelAvailable ? 'Available' : 'Not Found' }}
                </p>
            </div>
        </div>

        @if($ollamaError)
            <div class="mt-3 rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                {{ $ollamaError }}
            </div>
        @endif

        @if(count($availableModels) > 0)
            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                Available models: {{ implode(', ', $availableModels) }}
            </div>
        @endif
    </div>

    {{-- Setup Guide --}}
    <div class="mt-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Setup Guide</h2>

        <div class="prose prose-sm dark:prose-invert max-w-none">
            <h3>1. Install Ollama</h3>
            <p>Download and install Ollama from <a href="https://ollama.com" target="_blank" rel="noopener">ollama.com</a>.</p>
            <pre><code># Linux
curl -fsSL https://ollama.com/install.sh | sh

# Start the server
ollama serve</code></pre>

            <h3>2. Pull Required Models</h3>
            <p>You need two models: one for generating embeddings and one for chat conversations.</p>
            <pre><code># Embedding model (768 dimensions, ~275MB)
ollama pull {{ config('rag.embedding_model', 'nomic-embed-text') }}

# Chat model (3B parameters, ~2GB)
ollama pull {{ config('rag.chat_model', 'llama3.2:3b') }}</code></pre>

            <h3>3. Environment Configuration</h3>
            <p>Add or update these variables in your <code>.env</code> file:</p>
            <pre><code># Ollama server URL (default: http://localhost:11434)
OLLAMA_BASE_URL=http://localhost:11434

# RAG settings (optional, these are the defaults)
RAG_CHUNK_SIZE=512
RAG_CHUNK_OVERLAP=50
RAG_SIMILARITY_THRESHOLD=0.5
RAG_TOP_K=10</code></pre>

            <h3>4. Laravel/AI Configuration</h3>
            <p>The <code>config/ai.php</code> file is already configured to use Ollama as the default provider for both text and embeddings. Key settings:</p>
            <pre><code>// config/ai.php
'default' => 'ollama',
'default_for_embeddings' => 'ollama',

'providers' => [
    'ollama' => [
        'driver' => 'ollama',
        'url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
    ],
],</code></pre>

            <h3>5. Troubleshooting</h3>
            <table>
                <thead>
                    <tr>
                        <th>Issue</th>
                        <th>Solution</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Connection refused</td>
                        <td>Ensure Ollama is running: <code>ollama serve</code></td>
                    </tr>
                    <tr>
                        <td>Model not found</td>
                        <td>Pull the model: <code>ollama pull model-name</code></td>
                    </tr>
                    <tr>
                        <td>Slow responses</td>
                        <td>llama3.2:3b needs ~2-3GB RAM. Check available memory.</td>
                    </tr>
                    <tr>
                        <td>Embedding errors</td>
                        <td>nomic-embed-text needs ~300MB RAM. Verify with <code>ollama list</code>.</td>
                    </tr>
                    <tr>
                        <td>Wrong OLLAMA_BASE_URL</td>
                        <td>Default is <code>http://localhost:11434</code>. If Ollama runs on a different host/port, update <code>.env</code>.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
