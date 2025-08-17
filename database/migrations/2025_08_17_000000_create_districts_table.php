<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('districts')) {
            return; // table already exists
        }
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->unique();
            $table->string('name_bn')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('division_en')->nullable();
            $table->string('division_bn')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};


