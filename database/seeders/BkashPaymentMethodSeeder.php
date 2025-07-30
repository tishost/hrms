<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BkashPaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if bkash payment method exists
        $bkashMethod = DB::table('payment_methods')->where('code', 'bkash')->first();

        if ($bkashMethod) {
            // Update existing bkash method with proper settings
            DB::table('payment_methods')
                ->where('code', 'bkash')
                ->update([
                    'settings' => json_encode([
                        'merchant_id' => '',
                        'api_key' => '',
                        'api_secret' => '',
                        'gateway_url' => 'https://www.bkash.com/payment',
                        'sandbox_mode' => false, // Default to live mode
                    ]),
                    'is_active' => false, // Default to inactive until configured
                    'transaction_fee' => 1.5,
                    'updated_at' => now()
                ]);
        } else {
            // Create bkash payment method if it doesn't exist
            DB::table('payment_methods')->insert([
                'name' => 'bKash',
                'code' => 'bkash',
                'description' => 'bKash TokenizedCheckout Payment Gateway',
                'is_active' => false,
                'transaction_fee' => 1.5,
                'settings' => json_encode([
                    'merchant_id' => '',
                    'api_key' => '',
                    'api_secret' => '',
                    'gateway_url' => 'https://www.bkash.com/payment',
                    'sandbox_mode' => false, // Default to live mode
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('bKash payment method configured with proper sandbox mode settings.');
    }
}
