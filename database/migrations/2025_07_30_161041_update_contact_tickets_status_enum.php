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
        Schema::table('contact_tickets', function (Blueprint $table) {
            // First, drop the existing enum column
            $table->dropColumn('status');
        });

        Schema::table('contact_tickets', function (Blueprint $table) {
            // Add the new enum column with updated values
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'closed'])->default('pending')->after('details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_tickets', function (Blueprint $table) {
            // Drop the new enum column
            $table->dropColumn('status');
        });

        Schema::table('contact_tickets', function (Blueprint $table) {
            // Restore the original enum column
            $table->enum('status', ['pending', 'responded', 'closed'])->default('pending')->after('details');
        });
    }
};
