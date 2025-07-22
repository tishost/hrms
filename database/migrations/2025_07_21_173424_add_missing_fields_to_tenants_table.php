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
        Schema::table('tenants', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('handover_condition');
            $table->string('family_types')->nullable()->after('remarks');
            $table->integer('child_qty')->nullable()->after('family_types');
            $table->string('city')->nullable()->after('child_qty');
            $table->string('state')->nullable()->after('city');
            $table->string('zip')->nullable()->after('state');
            $table->string('college_university')->nullable()->after('zip');
            $table->string('business_name')->nullable()->after('college_university');
            $table->string('frequency')->nullable()->after('business_name');
            $table->string('nid_picture')->nullable()->after('frequency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'remarks',
                'family_types',
                'child_qty',
                'city',
                'state',
                'zip',
                'college_university',
                'business_name',
                'frequency',
                'nid_picture',
            ]);
        });
    }
};
