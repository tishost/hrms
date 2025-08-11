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
            if (!Schema::hasColumn('owner_subscriptions', 'upgrade_request_id')) {
                $table->foreignId('upgrade_request_id')->nullable()->constrained('subscription_upgrade_requests');
            }
            if (!Schema::hasColumn('owner_subscriptions', 'previous_plan_id')) {
                $table->foreignId('previous_plan_id')->nullable()->constrained('subscription_plans');
            }
            if (!Schema::hasColumn('owner_subscriptions', 'upgrade_date')) {
                $table->timestamp('upgrade_date')->nullable();
            }
            if (!Schema::hasColumn('owner_subscriptions', 'is_upgrading')) {
                $table->boolean('is_upgrading')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owner_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('owner_subscriptions', 'upgrade_request_id')) {
                $table->dropForeign(['upgrade_request_id']);
                $table->dropColumn('upgrade_request_id');
            }
            if (Schema::hasColumn('owner_subscriptions', 'previous_plan_id')) {
                $table->dropForeign(['previous_plan_id']);
                $table->dropColumn('previous_plan_id');
            }
            if (Schema::hasColumn('owner_subscriptions', 'upgrade_date')) {
                $table->dropColumn('upgrade_date');
            }
            if (Schema::hasColumn('owner_subscriptions', 'is_upgrading')) {
                $table->dropColumn('is_upgrading');
            }
        });
    }
};
