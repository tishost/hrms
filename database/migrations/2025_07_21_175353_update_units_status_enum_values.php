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
        // First, update any existing 'rented' values to 'occupied' temporarily
        DB::statement("UPDATE units SET status = 'vacant' WHERE status = 'rented'");

        // Drop the existing enum and recreate it with the correct values
        DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('vacant', 'rented', 'maintenance') DEFAULT 'vacant'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum if needed
        DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('vacant', 'occupied', 'maintenance') DEFAULT 'vacant'");
    }
};
