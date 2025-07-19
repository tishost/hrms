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
        // First modify the enum column to include new values
        DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('rent', 'free', 'vacant', 'occupied', 'maintenance') NOT NULL DEFAULT 'free'");

        // Then update existing data to use new enum values
        DB::statement("UPDATE units SET status = 'vacant' WHERE status = 'free'");
        DB::statement("UPDATE units SET status = 'occupied' WHERE status = 'rent'");

        // Finally, remove old enum values
        DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('vacant', 'occupied', 'maintenance') NOT NULL DEFAULT 'vacant'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back old enum values
        DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('vacant', 'occupied', 'maintenance', 'rent', 'free') NOT NULL DEFAULT 'vacant'");

        // Revert data back to original values
        DB::statement("UPDATE units SET status = 'free' WHERE status = 'vacant'");
        DB::statement("UPDATE units SET status = 'rent' WHERE status = 'occupied'");

        // Remove new enum values
        DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('rent', 'free') NOT NULL DEFAULT 'free'");
    }
};
