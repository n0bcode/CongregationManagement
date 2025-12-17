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
            // Add unique code for directory identification (AFE 2, AFE 3, etc.)
            $table->string('code', 20)->unique()->after('id')->nullable();
            
            // Add postal code for complete address
            $table->string('postal_code', 20)->nullable()->after('location');
            
            // Add country field
            $table->string('country', 100)->nullable()->after('postal_code');
            
            // Add activities/ministries (VTC, Parish, School, etc.)
            $table->text('activities')->nullable()->after('phone');
            
            // Add index for code
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->dropColumn(['code', 'postal_code', 'country', 'activities']);
        });
    }
};
