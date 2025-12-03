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
        // Fix formation_documents foreign key for uploaded_by
        Schema::table('formation_documents', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->foreign('uploaded_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict'); // Don't delete documents if user is deleted
        });

        // Add missing indexes for performance
        Schema::table('members', function (Blueprint $table) {
            $table->index('status');
            $table->index('entry_date');
            $table->index(['community_id', 'status']); // Composite index for common queries
        });

        Schema::table('formation_events', function (Blueprint $table) {
            $table->index('stage');
            $table->index('started_at');
            $table->index(['member_id', 'stage']); // Composite index
        });

        // Only add indexes if assignments table exists
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->index('start_date');
                $table->index('end_date');
                $table->index(['member_id', 'start_date']); // Composite index
                $table->index(['community_id', 'start_date']); // Composite index
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert formation_documents foreign key
        Schema::table('formation_documents', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->foreign('uploaded_by')->references('id')->on('users');
        });

        // Drop added indexes
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['entry_date']);
            $table->dropIndex(['community_id', 'status']);
        });

        Schema::table('formation_events', function (Blueprint $table) {
            $table->dropIndex(['stage']);
            $table->dropIndex(['started_at']);
            $table->dropIndex(['member_id', 'stage']);
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
            $table->dropIndex(['member_id', 'start_date']);
            $table->dropIndex(['community_id', 'start_date']);
        });
    }
};
