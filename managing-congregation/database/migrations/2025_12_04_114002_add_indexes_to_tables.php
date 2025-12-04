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
        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasIndex('members', 'members_community_id_status_index')) {
                $table->index(['community_id', 'status']);
            }
            if (! Schema::hasIndex('members', 'members_dob_index')) {
                $table->index('dob');
            }
            if (! Schema::hasIndex('members', 'members_last_name_first_name_index')) {
                $table->index(['last_name', 'first_name']);
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasIndex('expenses', 'expenses_community_id_date_index')) {
                $table->index(['community_id', 'date']);
            }
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            if (! Schema::hasIndex('audit_logs', 'audit_logs_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at']);
            }
            if (! Schema::hasIndex('audit_logs', 'audit_logs_auditable_type_auditable_id_index')) {
                $table->index(['auditable_type', 'auditable_id']);
            }
            if (! Schema::hasIndex('audit_logs', 'audit_logs_event_index')) {
                $table->index('action', 'audit_logs_event_index');
            }
        });

        Schema::table('documents', function (Blueprint $table) {
            if (! Schema::hasIndex('documents', 'documents_community_id_category_index')) {
                $table->index(['community_id', 'category']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['community_id', 'status']);
            $table->dropIndex(['dob']);
            $table->dropIndex(['last_name', 'first_name']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['community_id', 'expense_date']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['auditable_type', 'auditable_id']);
            $table->dropIndex(['event']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['community_id', 'category']);
        });
    }
};
