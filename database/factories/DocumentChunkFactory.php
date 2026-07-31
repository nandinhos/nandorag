<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentChunkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'content' => $this->faker->paragraph(),
            'chunk_index' => $this->faker->numberBetween(0, 100),
            'token_count' => $this->faker->numberBetween(50, 512),
            'source_file' => 'test.txt',
            'source_location' => 'p1',
            'embedding' => null,
        ];
    }
}
