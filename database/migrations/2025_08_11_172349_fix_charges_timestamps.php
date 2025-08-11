<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing records with null timestamps
        DB::table('charges')
            ->whereNull('created_at')
            ->update(['created_at' => now()]);
            
        DB::table('charges')
            ->whereNull('updated_at')
            ->update(['updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration only fixes data, no rollback needed
    }
};
