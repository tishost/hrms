<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->onDelete('cascade');
            $table->string('limit_type'); // properties, units, tenants, sms, emails, etc.
            $table->integer('current_usage')->default(0);
            $table->integer('max_limit');
            $table->date('reset_date'); // When to reset usage
            $table->string('reset_frequency'); // monthly, yearly, never
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['owner_id', 'limit_type']);
            $table->index(['owner_id', 'limit_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_limits');
    }
};
