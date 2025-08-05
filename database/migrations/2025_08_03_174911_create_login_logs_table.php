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
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('device_type')->default('web'); // web, mobile, tablet
            $table->string('platform')->nullable(); // ios, android, web, desktop
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('os')->nullable();
            $table->string('os_version')->nullable();
            $table->string('device_model')->nullable();
            $table->string('location')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('timezone')->nullable();
            $table->string('status'); // success, failed, blocked
            $table->text('failure_reason')->nullable();
            $table->string('login_method')->default('email'); // email, phone, social
            $table->string('app_version')->nullable();
            $table->string('api_version')->nullable();
            $table->json('additional_data')->nullable();
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->integer('session_duration')->nullable(); // in seconds
            $table->timestamps();

            $table->index(['user_id', 'login_at']);
            $table->index(['ip_address', 'login_at']);
            $table->index(['status', 'login_at']);
            $table->index(['device_type', 'login_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
