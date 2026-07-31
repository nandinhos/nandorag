<x-filament-panels::page>
    <div class="max-w-2xl">
        <x-filament::section>
            <x-slot name="heading">Upload de Documento</x-slot>
            <x-slot name="description">
                Selecione um arquivo PDF, TXT ou Markdown. Após o upload, o processamento ocorre em segundo plano.
            </x-slot>

            <livewire:tus-upload />
        </x-filament::section>
    </div>
</x-filament-panels::page>
