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
        Schema::create('checkout_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('owner_id')->constrained()->onDelete('cascade');
            $table->date('check_out_date');
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->decimal('deposit_returned', 10, 2)->default(0);
            $table->decimal('outstanding_dues', 10, 2)->default(0);
            $table->decimal('utility_bills', 10, 2)->default(0);
            $table->decimal('cleaning_charges', 10, 2)->default(0);
            $table->decimal('other_charges', 10, 2)->default(0);
            $table->decimal('final_settlement_amount', 10, 2)->default(0);
            $table->enum('settlement_status', ['pending', 'completed', 'partial'])->default('pending');
            $table->string('check_out_reason')->nullable();
            $table->date('handover_date')->nullable();
            $table->text('handover_condition')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_records');
    }
};
