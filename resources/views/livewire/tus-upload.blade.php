<div
    wire:ignore
    x-data="{
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
            console.log('TusUpload component initialized with local Alpine state');
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

            if (typeof tus === 'undefined') {
                this.error = 'Biblioteca TUS não detectada. Tentando carregar...';
                this.loadTusScript(() => this.startUpload());
                return;
            }

            this.tusUpload = new tus.Upload(this.file, {
                endpoint: '/tus/upload',
                retryDelays: [0, 3000, 5000, 10000, 20000],
                chunkSize: 5 * 1024 * 1024,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content,
                },
                metadata: {
                    filename: this.file.name,
                    filetype: this.file.type,
                },
                onError: (err) => {
                    console.error('TUS Error:', err);
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
                    this.$wire.call('uploadCompleted', this.filename);
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

        loadTusScript(callback) {
            if (typeof tus !== 'undefined') return callback();
            let script = document.createElement('script');
            script.src = '/js/tus.min.js';
            script.onload = callback;
            document.head.appendChild(script);
        }
    }"
    class="max-w-5xl mx-auto space-y-10 my-10"
>
    <!-- Gante que a LIB TUS está no topo -->
    <script src="/js/tus.min.js"></script>

    <!-- UI do Seletor -->
    <div x-show="!uploading && !done" class="transition-all duration-300">
        <label class="block text-[10px] font-heading font-black text-black mb-2 uppercase tracking-[0.3em] flex items-center gap-2">
            <span class="w-2 h-2 bg-cyan-400 border border-black inline-block"></span>
            Interface de Ingestão de Dados
        </label>
        
        <div class="group relative p-8 border-[4px] border-black bg-white shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[4px] hover:translate-y-[4px] transition-all flex flex-col items-center justify-center min-h-[240px]">
            <input
                type="file"
                id="file-input-tus"
                accept=".pdf,.txt,.md,.markdown"
                @change="selectFile($event)"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
            />
            
            <div class="flex flex-col items-center pointer-events-none">
                <div class="mb-4 p-4 bg-yellow-400 border-[3px] border-black rotate-[-2deg] group-hover:rotate-[2deg] transition-transform">
                    <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>
                <div class="text-xl font-heading font-black text-black text-center uppercase tracking-tight" x-text="file ? filename : 'Selecione o arquivo para o RAG'"></div>
                <div class="text-[10px] font-heading font-bold text-gray-500 mt-2 uppercase tracking-widest bg-gray-100 px-2 py-1 italic border border-gray-200">PDF, MD ou TXT</div>
            </div>
            
            <button
                type="button"
                x-show="file"
                @click.stop="startUpload()"
                class="mt-8 px-10 py-4 bg-cyan-400 border-[4px] border-black font-heading font-black text-black z-20 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:shadow-none transition-all uppercase italic tracking-tighter text-lg"
            >
                INJETAR AGORA
            </button>
        </div>
    </div>

    <!-- Interface de Progresso -->
    <div x-show="uploading && !done" class="p-6 border-[6px] border-black bg-white shadow-[10px_10px_0px_0px_rgba(34,211,238,1)]" x-cloak>
        <div class="flex justify-between items-end mb-6">
            <div class="max-w-[70%]">
                <div class="inline-block px-2 py-1 bg-black text-white text-[10px] font-heading font-black uppercase mb-2 tracking-tighter">Módulo de Transmissão Ativo</div>
                <div class="text-2xl font-heading font-black truncate italic tracking-tight" x-text="filename"></div>
            </div>
            <div class="text-right">
                <div class="text-5xl font-heading font-black leading-none tracking-tighter" x-text="progress + '%'"></div>
                <div class="text-[10px] font-mono font-black mt-2 uppercase bg-yellow-400 inline-block px-2 border-2 border-black" x-text="formatSize(uploadedBytes) + ' / ' + formatSize(totalBytes)"></div>
            </div>
        </div>
        
        <div class="w-full bg-black border-[4px] border-black h-12 relative overflow-hidden">
            <div 
                class="bg-cyan-400 h-full transition-all duration-300 pattern-diagonal"
                :style="'width: ' + progress + '%'"
            ></div>
            <div class="absolute inset-0 flex items-center justify-center mix-blend-difference font-heading font-black text-white tracking-widest italic text-sm" x-text="(progress < 15 ? '' : 'TRANSMITINDO DADOS...')"></div>
        </div>

        <div class="mt-6 flex gap-3">
            <button
                type="button"
                @click="pauseResume()"
                class="flex-1 py-3 border-[3px] border-black bg-white font-heading font-black hover:bg-gray-100 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[2px] active:translate-y-[2px] transition-all uppercase italic text-sm tracking-tight"
                x-text="paused ? 'Retomar Injeção' : 'Pausar Módulo'"
            ></button>
        </div>
    </div>

    <!-- Feedback de Sucesso -->
    <div x-show="done" class="p-8 border-[6px] border-black bg-green-400 shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] text-center" x-cloak>
        <div class="inline-block p-4 bg-white border-[4px] border-black mb-6 rotate-[-4deg]">
            <svg class="w-12 h-12 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="5" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div class="text-4xl font-heading font-black mb-3 uppercase italic leading-none tracking-tighter">DADOS ABSORVIDOS</div>
        <p class="text-sm font-heading font-bold mb-8 max-w-md mx-auto leading-tight italic opacity-90">
            O arquivo foi recebido pelo sistema NandoRAG. <br>
            A vetorização e indexação iniciaram imediatamente.
        </p>
        
        <a href="/admin/documents" class="inline-block px-10 py-4 bg-black text-white font-heading font-black hover:bg-gray-900 transition-transform hover:scale-105 shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] uppercase italic tracking-[0.2em] text-lg">
            CONCLUIR OPERAÇÃO
        </a>
    </div>

    <!-- Alertas de Erro -->
    <div x-show="error" class="p-6 border-[6px] border-black bg-red-600 text-white shadow-[10px_10px_0px_0px_rgba(0,0,0,1)]" x-cloak>
        <div class="flex items-center gap-4 mb-4">
            <div class="p-2 bg-white border-[3px] border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
            </div>
            <div class="font-heading font-black uppercase text-2xl italic leading-none tracking-tighter">ERRO DE SEGMENTO</div>
        </div>
        <div class="text-sm font-heading font-bold ml-16 bg-black/20 p-3 border-l-4 border-white/50 break-words" x-text="error"></div>
        <div class="ml-16 mt-6">
            <button 
                @click="error = null; uploading = false" 
                class="px-8 py-3 bg-white text-black border-[3px] border-black font-heading font-black uppercase text-xs hover:bg-gray-100 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[2px] active:translate-y-[2px] transition-all italic tracking-widest"
            >
                REINICIALIZAR MÓDULO
            </button>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
.pattern-diagonal {
    background-image: repeating-linear-gradient(45deg, transparent, transparent 20px, rgba(0,0,0,0.1) 20px, rgba(0,0,0,0.1) 40px);
}
</style>
