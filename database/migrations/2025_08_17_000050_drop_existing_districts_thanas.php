<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop thanas first to avoid FK constraint issues, then districts
        if (Schema::hasTable('thanas')) {
            Schema::drop('thanas');
        }
        if (Schema::hasTable('districts')) {
            Schema::drop('districts');
        }
    }

    public function down(): void
    {
        // No-op: this migration is destructive by intent
    }
};


