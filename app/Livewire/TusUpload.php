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

        // Redireciona para a lista após 1.5s (o JS controla o delay)
        $this->redirectRoute('filament.admin.resources.documents.index');
    }

    public function render(): View
    {
        return view('livewire.tus-upload');
    }
}
