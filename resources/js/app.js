import './bootstrap';
import * as tus from 'tus-js-client';

window.tus = tus;

window.tusUploader = function(endpoint) {
    console.log('Initializing tusUploader with endpoint:', endpoint);
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
            console.log('tusUploader instance initialized. Checking for "tus" object:', typeof tus !== 'undefined');
            if (typeof tus === 'undefined') {
                this.error = 'Biblioteca tus-js-client não carregada. Verifique o build do Vite.';
            }
        },

        selectFile(event) {
            this.file = event.target.files[0] ?? null;
            this.filename = this.file?.name ?? '';
            this.error = null;
            console.log('File selected:', this.filename);
        },

        startUpload() {
            if (!this.file) return;

            this.uploading = true;
            this.error = null;

            console.log('Starting upload for:', this.filename);

            if (typeof tus === 'undefined') {
                this.error = 'Erro: tus-js-client não disponível.';
                this.uploading = false;
                return;
            }

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
                    console.log('TUS Success:', this.filename);
                    this.progress = 100;
                    this.uploading = false;
                    this.done = true;
                    if (this.$wire) {
                        this.$wire.call('uploadCompleted', this.filename);
                    }
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
};
