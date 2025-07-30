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
        Schema::table('checkout_records', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('property_image');
            $table->string('payment_method')->nullable()->after('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkout_records', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'payment_method']);
        });
    }
};
