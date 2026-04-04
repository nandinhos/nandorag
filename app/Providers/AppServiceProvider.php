<?php

namespace App\Providers;

use App\Contracts\EmbeddingEngine;
use App\Services\Adapters\OllamaEmbeddingAdapter;
use App\Services\DocumentImportService;
use App\Services\Parsers\PdfDocumentParser;
use App\Services\Parsers\TextDocumentParser;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
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
        //
    }
}
