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
        Schema::table('tenant_ledgers', function (Blueprint $table) {
            // Modify the transaction_type enum to include 'invoice_payment'
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
                'invoice_payment',        // Invoice payment
                'adjustment',             // Manual adjustment
                'checkout_settlement'     // Final settlement on checkout
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_ledgers', function (Blueprint $table) {
            // Revert back to original enum values
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
            ])->change();
        });
    }
};
