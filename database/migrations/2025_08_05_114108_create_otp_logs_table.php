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
        Schema::create('otp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('otp', 6);
            $table->string('type'); // password_reset, registration, login, etc.
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->text('device_info')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['sent', 'verified', 'failed', 'blocked'])->default('sent');
            $table->integer('attempt_count')->default(1);
            $table->boolean('is_suspicious')->default(false);
            $table->integer('abuse_score')->default(0);
            $table->timestamp('blocked_until')->nullable();
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['phone', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['is_suspicious', 'created_at']);
            $table->index('user_id');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_logs');
    }
};
