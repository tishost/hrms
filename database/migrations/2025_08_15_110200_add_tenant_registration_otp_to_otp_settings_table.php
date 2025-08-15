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
        Schema::table('otp_settings', function (Blueprint $table) {
            $table->boolean('require_otp_for_tenant_registration')->default(false)->after('require_otp_for_registration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otp_settings', function (Blueprint $table) {
            $table->dropColumn('require_otp_for_tenant_registration');
        });
    }
};
