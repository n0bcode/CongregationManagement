<?php

declare(strict_types=1);

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
        Schema::create('formation_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_event_id')
                ->constrained('formation_events')
                ->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path', 500);
            $table->string('document_type', 100)->nullable();
            $table->unsignedInteger('file_size');
            $table->string('mime_type', 100);
            $table->foreignId('uploaded_by')
                ->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            $table->index('formation_event_id', 'idx_formation_event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formation_documents');
    }
};
