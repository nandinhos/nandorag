<?php

namespace App\Http\Controllers;

use App\Enums\DocumentStatus;
use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use TusPhp\Events\UploadComplete;
use TusPhp\Tus\Server;

class TusController extends Controller
{
    public function handle(Request $request): Response
    {
        $server = app(Server::class);

        $server->event()->addListener(
            'tus-server.upload.complete',
            function (UploadComplete $event) use ($request) {
                $this->onUploadComplete($event, $request);
            }
        );

        return $server->serve();
    }

    private function onUploadComplete(UploadComplete $event, Request $request): void
    {
        $file = $event->getFile();
        $originalName = $file->getName();
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $uuid = (string) Str::uuid();
        $storedPath = "documents/{$uuid}.{$extension}";

        try {
            // Mover do diretório TUS temporário para local permanente
            // getKey() retorna o upload key do TUS; o arquivo fica em tus-temp/{key}
            $tusRelativePath = 'private/tus-temp/'.$file->getKey();

            if (! Storage::disk('local')->exists($tusRelativePath)) {
                // Fallback: tus-php pode nomear o arquivo de outra forma
                // Verificar: storage_path('app/' . $tusRelativePath)
                throw new \RuntimeException("TUS temp file not found at: {$tusRelativePath}");
            }

            Storage::disk('local')->move($tusRelativePath, $storedPath);
        } catch (\Throwable $e) {
            Log::error("TUS: failed to move file {$originalName}: {$e->getMessage()}");

            return;
        }

        $mimeType = match ($extension) {
            'pdf' => 'application/pdf',
            'md', 'markdown' => 'text/markdown',
            default => 'text/plain',
        };

        $document = Document::create([
            'filename' => $originalName,
            'original_filename' => $originalName,
            'mime_type' => $mimeType,
            'file_path' => $storedPath,
            'tags' => [$originalName],
            'status' => DocumentStatus::Pending,
            'queued_at' => now(),
        ]);

        ProcessDocumentJob::dispatch($document->id)->onQueue('documents');

        Log::info("TUS: Document #{$document->id} created and queued for processing.");
    }
}
