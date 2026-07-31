<?php

namespace Database\Factories;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'filename' => $this->faker->word().'.txt',
            'original_filename' => $this->faker->word().'.txt',
            'mime_type' => 'text/plain',
            'content' => null,
            'status' => DocumentStatus::Pending,
            'file_path' => null,
            'tags' => [],
            'progress' => 0,
            'processed_chunks' => 0,
            'total_chunks' => 0,
            'error_log' => null,
            'queued_at' => null,
        ];
    }

    public function processing(): static
    {
        return $this->state(['status' => DocumentStatus::Processing]);
    }

    public function completed(): static
    {
        return $this->state([
            'status' => DocumentStatus::Completed,
            'progress' => 100,
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'status' => DocumentStatus::Failed,
            'error_log' => 'RuntimeException: Something went wrong',
        ]);
    }
}
