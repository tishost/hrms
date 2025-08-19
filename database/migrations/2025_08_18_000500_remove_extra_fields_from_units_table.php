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
            // Remove extra fields that were added
            if (Schema::hasColumn('units', 'advance_rent')) {
                $table->dropColumn('advance_rent');
            }
            
            if (Schema::hasColumn('units', 'security_deposit')) {
                $table->dropColumn('security_deposit');
            }
            
            if (Schema::hasColumn('units', 'rent_due_date')) {
                $table->dropColumn('rent_due_date');
            }
            
            if (Schema::hasColumn('units', 'late_fee')) {
                $table->dropColumn('late_fee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            // Re-add the columns if needed to rollback
            $table->decimal('advance_rent', 10, 2)->default(0.00)->after('rent');
            $table->decimal('security_deposit', 10, 2)->default(0.00)->after('advance_rent');
            $table->string('rent_due_date')->default('5th of each month')->after('security_deposit');
            $table->decimal('late_fee', 10, 2)->default(0.00)->after('rent_due_date');
        });
    }
};
