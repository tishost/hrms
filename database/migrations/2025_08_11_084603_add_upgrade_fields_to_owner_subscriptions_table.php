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
        Schema::table('owner_subscriptions', function (Blueprint $table) {
            $table->foreignId('upgrade_request_id')->nullable()->constrained('subscription_upgrade_requests');
            $table->foreignId('previous_plan_id')->nullable()->constrained('subscription_plans');
            $table->timestamp('upgrade_date')->nullable();
            $table->boolean('is_upgrading')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owner_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['upgrade_request_id']);
            $table->dropForeign(['previous_plan_id']);
            $table->dropColumn(['upgrade_request_id', 'previous_plan_id', 'upgrade_date', 'is_upgrading']);
        });
    }
};
