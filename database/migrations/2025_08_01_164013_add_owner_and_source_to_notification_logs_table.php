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
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->constrained('owners')->onDelete('cascade');
            $table->string('template_name')->nullable();
            $table->enum('source', ['owner', 'tenant', 'system'])->default('system');
            $table->index(['owner_id', 'type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn(['owner_id', 'template_name', 'source']);
            $table->dropIndex(['owner_id', 'type', 'created_at']);
        });
    }
};
