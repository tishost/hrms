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
        DB::statement("ALTER TABLE owner_subscriptions MODIFY COLUMN status ENUM('active', 'pending', 'pending_upgrade', 'suspended', 'cancelled', 'expired') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE owner_subscriptions MODIFY COLUMN status ENUM('active', 'pending', 'suspended', 'cancelled', 'expired') DEFAULT 'active'");
    }
};
