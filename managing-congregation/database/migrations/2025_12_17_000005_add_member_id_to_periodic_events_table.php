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
        Schema::table('periodic_events', function (Blueprint $table) {
            // Link to members table for personal events (birthdays, death anniversaries)
            $table->foreignId('member_id')->nullable()->after('id')
                ->constrained('members')->onDelete('cascade');
            
            // Event type categorization
            $table->enum('type', ['birthday', 'death', 'feast', 'formation', 'other'])
                ->default('other')->after('name');
            
            // Flag for recurring events (birthdays are recurring, deaths are not)
            $table->boolean('is_recurring')->default(false)->after('recurrence');
            
            // Add indexes for performance
            $table->index('member_id');
            $table->index(['type', 'start_date']);
            $table->index('is_recurring');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periodic_events', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['member_id']);
            
            // Drop indexes
            $table->dropIndex(['member_id']);
            $table->dropIndex(['type', 'start_date']);
            $table->dropIndex(['is_recurring']);
            
            // Drop columns
            $table->dropColumn(['member_id', 'type', 'is_recurring']);
        });
    }
};
