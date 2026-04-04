<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface VectorSearchEngine
{
    /**
     * Search for similar content using a query vector.
     *
     * @param  array<float>  $vector
     * @param  array<string, mixed>  $options  (limit, threshold, filters)
     */
    public function search(array $vector, array $options = []): Collection;
}
