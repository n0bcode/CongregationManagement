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
            // Check and add columns only if they don't exist
            if (!Schema::hasColumn('members', 'surname')) {
                $table->string('surname', 100)->nullable()->after('religious_name');
            }
            if (!Schema::hasColumn('members', 'given_name')) {
                $table->string('given_name', 100)->nullable()->after('surname');
            }
            if (!Schema::hasColumn('members', 'first_profession_date')) {
                $table->date('first_profession_date')->nullable()->after('entry_date');
            }
            if (!Schema::hasColumn('members', 'ordination_date')) {
                $table->date('ordination_date')->nullable()->after('first_profession_date');
            }
            if (!Schema::hasColumn('members', 'date_of_death')) {
                $table->date('date_of_death')->nullable()->after('ordination_date');
            }
            if (!Schema::hasColumn('members', 'is_deceased')) {
                $table->boolean('is_deceased')->default(false)->after('date_of_death');
            }
            if (!Schema::hasColumn('members', 'phone')) {
                $table->string('phone', 50)->nullable()->after('email');
            }
        });
        
        // Add indexes separately to avoid errors (use try-catch for Laravel 11 compatibility)
        try {
            Schema::table('members', function (Blueprint $table) {
                if (!Schema::hasColumn('members', 'surname')) {
                    return; // Skip if column doesn't exist
                }
                $table->index(['surname', 'given_name'], 'members_surname_given_name_idx');
            });
        } catch (\Exception $e) {
            // Index already exists, skip
        }
        
        try {
            Schema::table('members', function (Blueprint $table) {
                if (Schema::hasColumn('members', 'is_deceased')) {
                    $table->index('is_deceased');
                }
            });
        } catch (\Exception $e) {
            // Index already exists, skip
        }
        
        try {
            Schema::table('members', function (Blueprint $table) {
                if (Schema::hasColumn('members', 'first_profession_date')) {
                    $table->index('first_profession_date');
                }
            });
        } catch (\Exception $e) {
            // Index already exists, skip
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['surname', 'given_name']);
            $table->dropIndex(['is_deceased']);
            $table->dropIndex(['first_profession_date']);
            
            // Drop columns
            $table->dropColumn([
                'surname',
                'given_name',
                'first_profession_date',
                'ordination_date',
                'date_of_death',
                'is_deceased',
                'phone',
            ]);
        });
    }
};
