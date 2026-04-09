<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class TusUpload extends Component
{
    public bool $uploadComplete = false;

    public string $uploadedFilename = '';

    public function uploadCompleted(string $filename): void
    {
        $this->uploadComplete = true;
        $this->uploadedFilename = $filename;

        // Definir caminhos relativos ao root do disco 'local'
        $tempPath = 'tus-temp/' . $filename;
        $finalPath = 'documents/' . $filename;

        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($tempPath)) {
            // Garantir que o diretório de destino existe
            if (!\Illuminate\Support\Facades\Storage::disk('local')->exists('documents')) {
                \Illuminate\Support\Facades\Storage::disk('local')->makeDirectory('documents');
            }

            // Mover para pasta definitiva
            \Illuminate\Support\Facades\Storage::disk('local')->move($tempPath, $finalPath);
            
            // Criar o registro no banco
            $document = \App\Models\Document::create([
                'filename' => $filename,
                'original_filename' => $filename,
                'mime_type' => \Illuminate\Support\Facades\Storage::disk('local')->mimeType($finalPath) ?? 'application/octet-stream',
                'file_path' => $finalPath,
                'status' => \App\Enums\DocumentStatus::Pending,
                'progress' => 0,
                'processed_chunks' => 0,
                'total_chunks' => 0,
                'tags' => [],
            ]);

            // Disparar o pipeline de processamento e embeddings
            \App\Jobs\ProcessDocumentJob::dispatch($document->id);
        }

        $this->redirectRoute('filament.admin.resources.documents.index');
    }

    public function render(): View
    {
        return view('livewire.tus-upload');
    }
}
