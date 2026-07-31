<?php

namespace App\Providers;

use App\Contracts\EmbeddingEngine;
use App\Livewire\TusUpload;
use App\Services\Adapters\OllamaEmbeddingAdapter;
use App\Services\DocumentImportService;
use App\Services\Parsers\PdfDocumentParser;
use App\Services\Parsers\TextDocumentParser;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use TusPhp\Tus\Server;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Server::class, function () {
            $server = new Server('redis');

            $server->setApiPath('/tus/upload');
            $server->setUploadDir(storage_path('app/private/tus-temp'));
            $server->setMaxUploadSize(500 * 1024 * 1024); // 500MB

            return $server;
        });

        $this->app->bind(EmbeddingEngine::class, OllamaEmbeddingAdapter::class);

        $this->app->when(DocumentImportService::class)
            ->needs('$parsers')
            ->give(function ($app) {
                return [
                    $app->make(PdfDocumentParser::class),
                    $app->make(TextDocumentParser::class),
                ];
            });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('tus-upload', TusUpload::class);
    }
}
