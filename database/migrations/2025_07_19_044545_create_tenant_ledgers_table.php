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
        Schema::create('tenant_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('owner_id')->constrained()->onDelete('cascade');

            // Transaction Details
            $table->enum('transaction_type', [
                'rent_payment',           // Rent payment received
                'rent_due',               // Rent amount due
                'security_deposit',       // Security deposit received
                'deposit_return',         // Security deposit returned
                'utility_bill',           // Utility bill charged
                'utility_payment',        // Utility bill paid
                'maintenance_charge',     // Maintenance charge
                'maintenance_payment',    // Maintenance payment
                'late_fee',               // Late payment fee
                'late_fee_payment',       // Late fee payment
                'cleaning_charge',        // Cleaning charge
                'cleaning_payment',       // Cleaning payment
                'other_charge',           // Other charges
                'other_payment',          // Other payments
                'adjustment',             // Manual adjustment
                'checkout_settlement'     // Final settlement on checkout
            ]);

                        $table->string('reference_type')->nullable(); // 'invoice', 'rent_payment', 'checkout', etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of related record
            $table->string('invoice_number')->nullable(); // Invoice number for reference

            // Financial Details
            $table->decimal('debit_amount', 12, 2)->default(0); // Amount tenant owes
            $table->decimal('credit_amount', 12, 2)->default(0); // Amount tenant paid
            $table->decimal('balance', 12, 2); // Running balance after this transaction

            // Transaction Info
            $table->string('description'); // Human readable description
            $table->text('notes')->nullable(); // Additional notes
            $table->date('transaction_date');
            $table->date('due_date')->nullable(); // For charges that have due dates

            // Payment Details (for payments)
            $table->string('payment_method')->nullable(); // cash, bank, mobile_banking, etc.
            $table->string('payment_reference')->nullable(); // transaction ID, receipt number
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');

            // Audit Fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['tenant_id', 'transaction_date']);
            $table->index(['unit_id', 'transaction_date']);
            $table->index(['owner_id', 'transaction_date']);
            $table->index(['transaction_type', 'transaction_date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_ledgers');
    }
};
