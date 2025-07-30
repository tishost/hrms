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
            // Add 'expired' status to the enum
            $table->enum('status', ['active', 'pending', 'suspended', 'cancelled', 'expired'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owner_subscriptions', function (Blueprint $table) {
            // Remove 'expired' status from the enum
            $table->enum('status', ['active', 'pending', 'suspended', 'cancelled'])->default('active')->change();
        });
    }
};
