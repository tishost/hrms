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
        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('content_bangla')->nullable();
            $table->text('content_english')->nullable();
            $table->json('variables')->nullable();
            $table->string('category')->default('system');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->integer('character_limit')->default(160);
            $table->integer('priority')->default(1);
            $table->json('tags')->nullable();
            $table->boolean('unicode_support')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_templates');
    }
};