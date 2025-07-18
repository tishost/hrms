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
     Schema::create('owners', function (Blueprint $table) {
    $table->id(); // Primary key

    $table->string('name');
    $table->string('email')->unique();
    $table->string('phone')->nullable();
    $table->string('address')->nullable();
    $table->string('country')->nullable();

    $table->unsignedInteger('total_properties')->default(0); // Calculated dynamically
    $table->unsignedInteger('total_tenants')->default(0);    // Calculated dynamically

    $table->string('owner_uid')->unique(); // System-generated ID like "OWN-123XYZ"
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owners');
    }
};
