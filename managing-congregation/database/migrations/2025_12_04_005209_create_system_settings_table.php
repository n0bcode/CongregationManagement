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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'integer', 'boolean', 'json', 'date'])->default('string');
            $table->text('description')->nullable();
            $table->string('group')->default('general'); // For organizing settings
            $table->timestamps();

            // Indexes
            $table->index('key');
            $table->index('group');
        });

        // Insert default settings
        DB::table('system_settings')->insert([
            [
                'key' => 'service_year_start_month',
                'value' => '7', // July
                'type' => 'integer',
                'description' => 'Month when the service year starts (1-12)',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'vow_reminder_days',
                'value' => '90', // 90 days before
                'type' => 'integer',
                'description' => 'Number of days before vow expiration to send reminder',
                'group' => 'reminders',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'birthday_reminder_days',
                'value' => '7', // 7 days before
                'type' => 'integer',
                'description' => 'Number of days before birthday to send reminder',
                'group' => 'reminders',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'health_check_reminder_months',
                'value' => '12', // Annual
                'type' => 'integer',
                'description' => 'Number of months between health check reminders',
                'group' => 'reminders',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'congregation_name',
                'value' => 'Congregation Management System',
                'type' => 'string',
                'description' => 'Name of the congregation',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'congregation_logo_path',
                'value' => null,
                'type' => 'string',
                'description' => 'Path to congregation logo file',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
