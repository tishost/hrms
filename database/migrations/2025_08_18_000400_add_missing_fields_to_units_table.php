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
        Schema::table('units', function (Blueprint $table) {
            // Add missing fields for rent agreement
            if (!Schema::hasColumn('units', 'advance_rent')) {
                $table->decimal('advance_rent', 10, 2)->default(0.00)->after('rent');
            }
            
            if (!Schema::hasColumn('units', 'security_deposit')) {
                $table->decimal('security_deposit', 10, 2)->default(0.00)->after('advance_rent');
            }
            
            if (!Schema::hasColumn('units', 'rent_due_date')) {
                $table->string('rent_due_date')->default('5th of each month')->after('security_deposit');
            }
            
            if (!Schema::hasColumn('units', 'late_fee')) {
                $table->decimal('late_fee', 10, 2)->default(0.00)->after('rent_due_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['advance_rent', 'security_deposit', 'rent_due_date', 'late_fee']);
        });
    }
};
