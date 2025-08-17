<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Rename tenants.state -> tenants.district, tenants.city -> tenants.upazila
        if (Schema::hasTable('tenants')) {
            // city -> upazila
            if (Schema::hasColumn('tenants', 'city') && !Schema::hasColumn('tenants', 'upazila')) {
                try {
                    // Attempt native rename (MySQL 8+)
                    DB::statement('ALTER TABLE tenants RENAME COLUMN city TO upazila');
                } catch (\Throwable $e) {
                    // Fallback: CHANGE with type
                    try { DB::statement("ALTER TABLE tenants CHANGE city upazila VARCHAR(191) NULL"); } catch (\Throwable $e2) {}
                }
            }
            // state -> district
            if (Schema::hasColumn('tenants', 'state') && !Schema::hasColumn('tenants', 'district')) {
                try {
                    DB::statement('ALTER TABLE tenants RENAME COLUMN state TO district');
                } catch (\Throwable $e) {
                    try { DB::statement("ALTER TABLE tenants CHANGE state district VARCHAR(191) NULL"); } catch (\Throwable $e2) {}
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tenants')) {
            if (Schema::hasColumn('tenants', 'upazila') && !Schema::hasColumn('tenants', 'city')) {
                try {
                    DB::statement('ALTER TABLE tenants RENAME COLUMN upazila TO city');
                } catch (\Throwable $e) {
                    try { DB::statement("ALTER TABLE tenants CHANGE upazila city VARCHAR(191) NULL"); } catch (\Throwable $e2) {}
                }
            }
            if (Schema::hasColumn('tenants', 'district') && !Schema::hasColumn('tenants', 'state')) {
                try {
                    DB::statement('ALTER TABLE tenants RENAME COLUMN district TO state');
                } catch (\Throwable $e) {
                    try { DB::statement("ALTER TABLE tenants CHANGE district state VARCHAR(191) NULL"); } catch (\Throwable $e2) {}
                }
            }
        }
    }
};


