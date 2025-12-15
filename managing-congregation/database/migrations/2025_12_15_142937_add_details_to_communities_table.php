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
        Schema::table('communities', function (Blueprint $table) {
            $table->string('patron_saint')->nullable()->after('location');
            $table->date('founded_at')->nullable()->after('patron_saint');
            $table->date('feast_day')->nullable()->after('founded_at'); // Specifically for the patron saint's feast day
            $table->string('email')->nullable()->after('feast_day');
            $table->string('phone')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->dropColumn(['patron_saint', 'founded_at', 'feast_day', 'email', 'phone']);
        });
    }
};
