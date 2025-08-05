<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('owner_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            // Performance indexes
            $table->index(['owner_id', 'key']); // Composite index for faster lookups
            $table->index('owner_id'); // For owner-specific queries
            $table->index('key'); // For template-specific queries
            
            // Unique constraint
            $table->unique(['owner_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_settings');
    }
};
