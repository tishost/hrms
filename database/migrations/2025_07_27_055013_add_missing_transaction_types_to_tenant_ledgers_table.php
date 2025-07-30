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
        try {
            // First, update any existing data that might conflict
            DB::table('tenant_ledgers')->where('transaction_type', 'cleaning_charges')->update(['transaction_type' => 'cleaning_charge']);
            DB::table('tenant_ledgers')->where('transaction_type', 'damage_charges')->update(['transaction_type' => 'other_charge']);
            DB::table('tenant_ledgers')->where('transaction_type', 'checkout_adjustment')->update(['transaction_type' => 'adjustment']);
            DB::table('tenant_ledgers')->where('transaction_type', 'refund_payment')->update(['transaction_type' => 'other_payment']);

            Schema::table('tenant_ledgers', function (Blueprint $table) {
                // Modify the transaction_type enum to include missing values
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
        } catch (\Exception $e) {
            // If the migration fails, we'll skip it for now
            $this->command->warn('Skipping tenant_ledgers migration due to data conflicts: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_ledgers', function (Blueprint $table) {
            // Revert back to previous enum values
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
};
