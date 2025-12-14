<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize 'Active' -> 'active'
        DB::table('members')
            ->where('status', 'Active')
            ->update(['status' => 'active']);

        // Normalize 'Deceased' -> 'deceased'
        DB::table('members')
            ->where('status', 'Deceased')
            ->update(['status' => 'deceased']);

        // Normalize 'Exited' -> 'exited'
        DB::table('members')
            ->where('status', 'Exited')
            ->update(['status' => 'exited']);

        // Normalize 'Transferred' -> 'transferred'
        DB::table('members')
            ->where('status', 'Transferred')
            ->update(['status' => 'transferred']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We cannot reliably revert this as we don't know which ones were Uppercase originally.
        // It's safer to leave them lowercase as that is the correct source of truth.
    }
};
