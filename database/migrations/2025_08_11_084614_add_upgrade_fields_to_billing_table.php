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
        Schema::table('billing', function (Blueprint $table) {
            $table->foreignId('upgrade_request_id')->nullable()->constrained('subscription_upgrade_requests');
            $table->enum('billing_type', ['subscription', 'upgrade', 'renewal'])->default('subscription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing', function (Blueprint $table) {
            $table->dropForeign(['upgrade_request_id']);
            $table->dropColumn(['upgrade_request_id', 'billing_type']);
        });
    }
};
