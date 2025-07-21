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
        // Add OTP send limit setting (default 5)
        \DB::table('system_settings')->insert([
            'key' => 'otp_send_limit',
            'value' => '5',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove OTP send limit setting
        \DB::table('system_settings')->where('key', 'otp_send_limit')->delete();
    }
};
