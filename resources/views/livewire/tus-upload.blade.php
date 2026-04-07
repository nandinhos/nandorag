<div
    x-data="tusUploader(@js(route('tus.upload')))"
    x-init="init()"
    class="space-y-4"
>
    {{-- Seletor de arquivo --}}
    <div x-show="!uploading && !done">
        <label class="block text-sm font-heading font-semibold text-gray-700 mb-2">
            Selecionar Arquivo (PDF, TXT, MD — até 500MB)
        </label>
        <input
            type="file"
            accept=".pdf,.txt,.md,.markdown"
            @change="selectFile($event)"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border file:border-black file:rounded file:bg-yellow-400 file:font-heading file:font-semibold file:cursor-pointer"
        />
    </div>

    {{-- Nome do arquivo selecionado + tamanho --}}
    <div x-show="file && !uploading && !done" class="text-sm text-gray-600">
        <span x-text="file ? file.name + ' (' + formatSize(file.size) + ')' : ''"></span>
    </div>

    {{-- Botão de upload --}}
    <button
        x-show="file && !uploading && !done"
        @click="startUpload()"
        type="button"
        class="px-4 py-2 bg-black text-white font-heading font-semibold border border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all"
    >
        Enviar Arquivo
    </button>

    {{-- Barra de progresso --}}
    <div x-show="uploading" class="space-y-2">
        <div class="flex justify-between text-sm font-heading">
            <span x-text="'Enviando: ' + filename"></span>
            <span x-text="progress + '%'"></span>
        </div>
        <div class="w-full bg-gray-200 border border-black h-4">
            <div
                class="bg-yellow-400 h-full transition-all duration-300"
                :style="'width: ' + progress + '%'"
            ></div>
        </div>
        <div class="text-xs text-gray-500" x-text="formatSize(uploadedBytes) + ' de ' + formatSize(totalBytes)"></div>

        <button
            @click="pauseResume()"
            type="button"
            class="text-xs underline text-gray-600 hover:text-black"
            x-text="paused ? 'Retomar' : 'Pausar'"
        ></button>
    </div>

    {{-- Sucesso --}}
    <div x-show="done" class="flex items-center gap-2 text-green-700 font-heading font-semibold">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>Upload concluído! Redirecionando...</span>
    </div>

    {{-- Erro --}}
    <div x-show="error" class="text-red-600 text-sm font-heading" x-text="error"></div>
</div>

@script
<script>
function tusUploader(endpoint) {
    return {
        file: null,
        filename: '',
        progress: 0,
        uploading: false,
        paused: false,
        done: false,
        error: null,
        uploadedBytes: 0,
        totalBytes: 0,
        tusUpload: null,

        init() {
            // tus-js-client é carregado via npm/vite
        },

        selectFile(event) {
            this.file = event.target.files[0] ?? null;
            this.filename = this.file?.name ?? '';
            this.error = null;
        },

        startUpload() {
            if (!this.file) return;

            this.uploading = true;
            this.error = null;

            this.tusUpload = new tus.Upload(this.file, {
                endpoint: endpoint,
                retryDelays: [0, 3000, 5000, 10000, 20000],
                chunkSize: 5 * 1024 * 1024,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                metadata: {
                    filename: this.file.name,
                    filetype: this.file.type,
                },
                onError: (err) => {
                    this.error = 'Erro no upload: ' + err.message;
                    this.uploading = false;
                },
                onProgress: (bytesUploaded, bytesTotal) => {
                    this.uploadedBytes = bytesUploaded;
                    this.totalBytes = bytesTotal;
                    this.progress = Math.round(bytesUploaded / bytesTotal * 100);
                },
                onSuccess: () => {
                    this.progress = 100;
                    this.uploading = false;
                    this.done = true;
                    $wire.uploadCompleted(this.filename);
                },
            });

            this.tusUpload.start();
        },

        pauseResume() {
            if (this.paused) {
                this.tusUpload.start();
                this.paused = false;
            } else {
                this.tusUpload.abort();
                this.paused = true;
            }
        },

        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        },
    }
}
</script>
@endscript
