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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [
                'vow_expiration',
                'birthday',
                'health_check',
                'formation_milestone',
                'anniversary',
                'other',
            ]);
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('reminder_date');
            $table->foreignId('member_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('community_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            // Indexes
            $table->index('type');
            $table->index('reminder_date');
            $table->index('is_sent');
            $table->index('member_id');
            $table->index('community_id');
            $table->index(['reminder_date', 'is_sent']); // Composite for pending reminders
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
