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
        Schema::table('properties', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('owners', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('units', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('tenants', function (Blueprint $table) {
            $table->softDeletes();
            $table->unsignedBigInteger('building_id')->nullable()->after('company_name'); // property_id
            $table->unsignedBigInteger('owner_id')->nullable()->after('building_id');
            $table->string('status')->nullable()->after('owner_id');
            $table->date('check_in_date')->nullable()->after('status');
            $table->date('check_out_date')->nullable()->after('check_in_date');
            $table->decimal('security_deposit', 10, 2)->nullable()->after('check_out_date');
            $table->decimal('cleaning_charges', 10, 2)->nullable()->after('security_deposit');
            $table->decimal('other_charges', 10, 2)->nullable()->after('cleaning_charges');
            $table->string('check_out_reason')->nullable()->after('other_charges');
            $table->date('handover_date')->nullable()->after('check_out_reason');
            $table->string('handover_condition')->nullable()->after('handover_date');
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
        Schema::table('properties', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('owners', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('units', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'building_id',
                'owner_id',
                'status',
                'check_in_date',
                'check_out_date',
                'security_deposit',
                'cleaning_charges',
                'other_charges',
                'check_out_reason',
                'handover_date',
                'handover_condition',
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
