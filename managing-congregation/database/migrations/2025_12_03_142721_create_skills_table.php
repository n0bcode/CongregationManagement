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
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->enum('category', ['pastoral', 'practical', 'special']);
            $table->string('name');
            $table->enum('proficiency', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('member_id');
            $table->index('category');
            $table->index(['member_id', 'category']); // Composite index for common queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
