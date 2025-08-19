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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->string('url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('show_on_owner_dashboard')->default(false);
            $table->boolean('show_on_tenant_dashboard')->default(false);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('display_order')->default(0);
            $table->integer('clicks_count')->default(0);
            $table->integer('impressions_count')->default(0);
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['is_active', 'start_date', 'end_date']);
            $table->index(['show_on_owner_dashboard', 'is_active']);
            $table->index(['show_on_tenant_dashboard', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
