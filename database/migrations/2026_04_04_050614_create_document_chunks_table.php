<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->unsignedInteger('chunk_index');
            $table->unsignedInteger('token_count');
            $table->string('source_file');
            $table->string('source_location')->nullable();
            $table->vector('embedding', dimensions: 768)->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_chunks');
    }
};
