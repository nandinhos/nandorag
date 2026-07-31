<div class="space-y-3 p-4">
    @if($document->queued_at)
        <p class="text-xs text-gray-500 font-heading">
            Falhou em: {{ $document->updated_at->format('d/m/Y H:i:s') }}
        </p>
    @endif
    <div class="bg-red-50 border border-red-300 rounded p-3 overflow-auto max-h-96">
        <pre class="text-xs text-red-800 whitespace-pre-wrap font-mono">{{ $document->error_log }}</pre>
    </div>
</div>
