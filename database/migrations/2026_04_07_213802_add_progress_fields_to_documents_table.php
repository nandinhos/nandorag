<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedTinyInteger('progress')->default(0)->after('tags');
            $table->unsignedInteger('processed_chunks')->default(0)->after('progress');
            $table->unsignedInteger('total_chunks')->default(0)->after('processed_chunks');
            $table->text('error_log')->nullable()->after('total_chunks');
            $table->timestamp('queued_at')->nullable()->after('error_log');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['progress', 'processed_chunks', 'total_chunks', 'error_log', 'queued_at']);
        });
    }
};
