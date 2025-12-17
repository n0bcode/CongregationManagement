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
        Schema::table('assignments', function (Blueprint $table) {
            // Link to roles table (P, L, S, D, n)
            $table->foreignId('role_id')->nullable()->after('community_id')
                ->constrained('roles')->onDelete('set null');
            
            // Specific position at the community (Rector, VR, Admin, PP, Principal, etc.)
            $table->string('position', 100)->nullable()->after('role_id');
            
            // Flag to indicate if this is the current assignment
            $table->boolean('is_current')->default(true)->after('end_date');
            
            // Add indexes
            $table->index('role_id');
            $table->index('is_current');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['role_id']);
            
            // Drop indexes
            $table->dropIndex(['role_id']);
            $table->dropIndex(['is_current']);
            
            // Drop columns
            $table->dropColumn(['role_id', 'position', 'is_current']);
        });
    }
};
