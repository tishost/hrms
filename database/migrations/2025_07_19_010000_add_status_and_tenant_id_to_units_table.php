<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->after('property_id');
            $table->enum('status', ['rent', 'free'])->default('free')->after('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'status']);
        });
    }
};
