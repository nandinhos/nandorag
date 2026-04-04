<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('ai.providers.ollama.url', 'http://localhost:11434');
    }

    /**
     * Get list of available models from Ollama
     *
     * @return array<int, array{name: string, size: int, modified_at: string}>
     */
    public function getModels(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/tags");

            if ($response->successful()) {
                $data = $response->json();

                return $data['models'] ?? [];
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to fetch Ollama models: {$e->getMessage()}");
        }

        return [];
    }

    /**
     * Get model info
     */
    public function getModelInfo(string $modelName): ?array
    {
        try {
            $response = Http::timeout(5)
                ->post("{$this->baseUrl}/api/show", ['name' => $modelName]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to fetch model info: {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Check if Ollama is running
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(3)->get("{$this->baseUrl}/api/tags");

            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Format model size for display
     */
    public static function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }
}
