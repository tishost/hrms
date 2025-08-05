<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->enum('billing_cycle', ['monthly', 'yearly', 'lifetime'])->default('monthly')->after('price');
            $table->integer('duration_days')->default(30)->after('billing_cycle');
        });
    }

    public function down()
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['billing_cycle', 'duration_days']);
        });
    }
}; 