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
        // Update bkash payment method settings
        $bkashMethod = DB::table('payment_methods')->where('code', 'bkash')->first();

        if ($bkashMethod) {
            $currentSettings = json_decode($bkashMethod->settings ?? '{}', true);

            // Update settings with proper sandbox mode
            $updatedSettings = array_merge($currentSettings, [
                'sandbox_mode' => $currentSettings['sandbox_mode'] ?? false,
                'merchant_id' => $currentSettings['merchant_id'] ?? '',
                'api_key' => $currentSettings['api_key'] ?? '',
                'api_secret' => $currentSettings['api_secret'] ?? '',
                'gateway_url' => $currentSettings['gateway_url'] ?? 'https://www.bkash.com/payment',
            ]);

            DB::table('payment_methods')
                ->where('code', 'bkash')
                ->update([
                    'settings' => json_encode($updatedSettings),
                    'updated_at' => now()
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous settings if needed
        $bkashMethod = DB::table('payment_methods')->where('code', 'bkash')->first();

        if ($bkashMethod) {
            $currentSettings = json_decode($bkashMethod->settings ?? '{}', true);

            // Revert sandbox mode to true (previous default)
            $revertedSettings = array_merge($currentSettings, [
                'sandbox_mode' => true,
            ]);

            DB::table('payment_methods')
                ->where('code', 'bkash')
                ->update([
                    'settings' => json_encode($revertedSettings),
                    'updated_at' => now()
                ]);
        }
    }
};
