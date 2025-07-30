<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('owner_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('subscription_plans');
            $table->enum('status', ['active', 'pending', 'suspended', 'cancelled', 'expired'])->default('active');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->integer('sms_credits')->default(0); // For Free users
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('owner_subscriptions');
    }
};
