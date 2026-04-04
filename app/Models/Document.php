<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_filename',
        'mime_type',
        'content',
        'status',
        'file_path',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'status' => DocumentStatus::class,
            'tags' => 'array',
        ];
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(DocumentChunk::class);
    }
}
