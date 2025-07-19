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
        Schema::table('tenants', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'checked_out'])->default('active');
            $table->date('check_in_date')->nullable();
            $table->date('check_out_date')->nullable();
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->decimal('cleaning_charges', 10, 2)->default(0);
            $table->decimal('other_charges', 10, 2)->default(0);
            $table->string('check_out_reason')->nullable();
            $table->date('handover_date')->nullable();
            $table->text('handover_condition')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'check_in_date',
                'check_out_date',
                'security_deposit',
                'cleaning_charges',
                'other_charges',
                'check_out_reason',
                'handover_date',
                'handover_condition'
            ]);
        });
    }
};
