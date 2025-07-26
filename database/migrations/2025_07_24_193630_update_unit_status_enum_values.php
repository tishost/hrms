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
        // First, update existing values
        DB::statement("UPDATE units SET status = 'rented' WHERE status = 'rent'");
        DB::statement("UPDATE units SET status = 'vacant' WHERE status = 'free'");

        // Then, modify the enum
        DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('rented', 'vacant', 'maintained') DEFAULT 'vacant'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the enum back to original values
        DB::statement("UPDATE units SET status = 'rent' WHERE status = 'rented'");
        DB::statement("UPDATE units SET status = 'free' WHERE status = 'vacant'");
        DB::statement("UPDATE units SET status = 'free' WHERE status = 'maintained'");

        DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('rent', 'free') DEFAULT 'free'");
    }
};
