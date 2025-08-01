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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('visitor_ip')->nullable();
            $table->string('visitor_user_agent')->nullable();
            $table->enum('message_type', ['user', 'bot', 'agent'])->default('user');
            $table->text('message');
            $table->string('intent')->nullable(); // pricing, demo, support, features
            $table->unsignedBigInteger('agent_id')->nullable(); // Agent who responded
            $table->enum('status', ['active', 'waiting', 'resolved', 'transferred'])->default('active');
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
