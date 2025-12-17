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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            
            // Role code from directory (P, L, S, D, n)
            $table->string('code', 10)->unique();
            
            // Full title (Presbyter, Laicus, Scholasticus, Diaconus, Novitius)
            $table->string('title', 100);
            
            // Description of the role
            $table->text('description')->nullable();
            
            $table->timestamps();
            
            // Index for quick lookups
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
