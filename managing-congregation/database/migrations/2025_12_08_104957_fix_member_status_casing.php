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
        // Update all statuses to lowercase
        DB::table('members')->update([
            'status' => DB::raw('LOWER(status)')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation needed as lowercase is the canonical form
    }
};
