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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // bKash, Nagad, Rocket, Bank Transfer, etc.
            $table->string('code')->unique(); // bkash, nagad, rocket, bank_transfer
            $table->text('description')->nullable();
            $table->string('logo')->nullable(); // Icon or logo path
            $table->decimal('transaction_fee', 5, 2)->default(0); // Transaction fee percentage
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // Additional settings like API keys
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
