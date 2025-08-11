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
            if (!Schema::hasColumn('billing', 'upgrade_request_id')) {
                $table->foreignId('upgrade_request_id')->nullable()->constrained('subscription_upgrade_requests');
            }
            if (!Schema::hasColumn('billing', 'billing_type')) {
                $table->enum('billing_type', ['subscription', 'upgrade', 'renewal'])->default('subscription');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing', function (Blueprint $table) {
            if (Schema::hasColumn('billing', 'upgrade_request_id')) {
                $table->dropForeign(['upgrade_request_id']);
                $table->dropColumn('upgrade_request_id');
            }
            if (Schema::hasColumn('billing', 'billing_type')) {
                $table->dropColumn('billing_type');
            }
        });
    }
};
