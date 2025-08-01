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
        Schema::table('owners', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('user_id');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('is_super_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn(['is_super_admin', 'status']);
        });
    }
};
