<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();

            // Owner relationship
            $table->unsignedBigInteger('owner_id');

            // Building details
            $table->string('name');
            $table->string('type')->nullable(); // residential/commercial
            $table->string('address')->nullable();
            $table->string('country')->nullable();

            // Plan limitation
            $table->integer('unit_limit')->default(5); // Free plan limit

            // Status
            $table->boolean('is_active')->default(true);

            // Extra optional features
            $table->json('features')->nullable(); // Lift, generator, etc.

            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('properties');
    }
}