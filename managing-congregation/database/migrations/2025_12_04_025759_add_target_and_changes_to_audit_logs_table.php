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
        Schema::table('audit_logs', function (Blueprint $table) {
            // Add target_type and target_id for RBAC audit logging
            $table->string('target_type')->nullable()->after('auditable_id');
            $table->string('target_id')->nullable()->after('target_type');

            // Add changes column for RBAC permission/role changes
            $table->json('changes')->nullable()->after('new_values');

            // Add indexes for performance
            $table->index(['target_type', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['target_type', 'target_id']);
            $table->dropColumn(['target_type', 'target_id', 'changes']);
        });
    }
};
