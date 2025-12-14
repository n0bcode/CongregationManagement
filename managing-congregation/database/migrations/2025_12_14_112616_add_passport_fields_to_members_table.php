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
            $table->string('passport_number')->nullable()->after('email')->index();
            $table->date('passport_issued_at')->nullable()->after('passport_number');
            $table->date('passport_expired_at')->nullable()->after('passport_issued_at');
            $table->string('passport_place_of_issue')->nullable()->after('passport_expired_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'passport_number',
                'passport_issued_at',
                'passport_expired_at',
                'passport_place_of_issue',
            ]);
        });
    }
};
