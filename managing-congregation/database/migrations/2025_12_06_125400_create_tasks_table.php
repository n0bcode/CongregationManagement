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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('task'); // epic, story, task, bug
            $table->string('status')->default('todo'); // todo, in_progress, review, done
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->foreignId('assignee_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('reporter_id')->constrained('members');
            $table->foreignId('parent_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
