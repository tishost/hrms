<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('thanas')) {
            return; // table already exists
        }
        Schema::create('thanas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->string('name_en');
            $table->string('name_bn')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->unique(['district_id', 'name_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanas');
    }
};


