<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('tenants', function (Blueprint $table) {
			if (!Schema::hasColumn('tenants', 'spouse_name')) {
				$table->string('spouse_name', 150)->nullable()->after('family_types');
			}
			if (!Schema::hasColumn('tenants', 'father_name')) {
				$table->string('father_name', 150)->nullable()->after('spouse_name');
			}
			if (!Schema::hasColumn('tenants', 'mother_name')) {
				$table->string('mother_name', 150)->nullable()->after('father_name');
			}
			if (!Schema::hasColumn('tenants', 'sister_name')) {
				$table->string('sister_name', 150)->nullable()->after('mother_name');
			}
			if (!Schema::hasColumn('tenants', 'brother_name')) {
				$table->string('brother_name', 150)->nullable()->after('sister_name');
			}
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('tenants', function (Blueprint $table) {
			if (Schema::hasColumn('tenants', 'brother_name')) {
				$table->dropColumn('brother_name');
			}
			if (Schema::hasColumn('tenants', 'sister_name')) {
				$table->dropColumn('sister_name');
			}
			if (Schema::hasColumn('tenants', 'mother_name')) {
				$table->dropColumn('mother_name');
			}
			if (Schema::hasColumn('tenants', 'father_name')) {
				$table->dropColumn('father_name');
			}
			if (Schema::hasColumn('tenants', 'spouse_name')) {
				$table->dropColumn('spouse_name');
			}
		});
	}
};


