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
        Schema::create('app_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // 'app_install', 'screen_view', 'feature_usage', 'error', 'performance'
            $table->string('device_type'); // 'android', 'ios', 'web', 'unknown'
            $table->string('os_version')->nullable();
            $table->string('app_version')->nullable();
            $table->string('device_model')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('screen_resolution')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('additional_data')->nullable(); // Store extra analytics data
            $table->string('session_id')->nullable(); // Track user sessions
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('event_timestamp');
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['event_type', 'created_at']);
            $table->index(['device_type', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['event_timestamp']);

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_analytics');
    }
};
