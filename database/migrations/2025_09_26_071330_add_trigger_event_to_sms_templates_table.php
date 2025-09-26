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
        Schema::table('sms_templates', function (Blueprint $table) {
            $table->string('trigger_event')->nullable()->after('key');
            $table->json('trigger_conditions')->nullable()->after('trigger_event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_templates', function (Blueprint $table) {
            $table->dropColumn(['trigger_event', 'trigger_conditions']);
        });
    }
};
