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
        Schema::create('otp_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(true);
            $table->integer('otp_length')->default(6);
            $table->integer('otp_expiry_minutes')->default(10);
            $table->integer('max_attempts')->default(3);
            $table->integer('resend_cooldown_seconds')->default(60);
            $table->boolean('require_otp_for_registration')->default(true);
            $table->boolean('require_otp_for_login')->default(false);
            $table->boolean('require_otp_for_password_reset')->default(true);
            $table->text('otp_message_template')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_settings');
    }
};
