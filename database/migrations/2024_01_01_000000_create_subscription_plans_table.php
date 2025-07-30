<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Free, Lite, Advance
            $table->decimal('price', 10, 2); // 0, 999, 1999
            $table->integer('properties_limit'); // 1, 2, -1 (unlimited)
            $table->integer('units_limit'); // 4, 30, -1 (unlimited)
            $table->integer('tenants_limit'); // 10, 100, -1 (unlimited)
            $table->boolean('sms_notification'); // false, true, true
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }
};
