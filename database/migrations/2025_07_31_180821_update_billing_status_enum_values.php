<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // First update the enum to include new values
        DB::statement("ALTER TABLE billing MODIFY COLUMN status ENUM('pending', 'paid', 'failed', 'refunded', 'unpaid', 'cancel', 'fail', 'refund') DEFAULT 'pending'");
        
        // Then update existing status values
        DB::statement("UPDATE billing SET status = 'unpaid' WHERE status = 'pending'");
        DB::statement("UPDATE billing SET status = 'fail' WHERE status = 'failed'");
        
        // Finally update to only new values
        DB::statement("ALTER TABLE billing MODIFY COLUMN status ENUM('paid', 'unpaid', 'cancel', 'fail', 'refund') DEFAULT 'unpaid'");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Revert to original status values
        DB::statement("UPDATE billing SET status = 'pending' WHERE status = 'unpaid'");
        DB::statement("UPDATE billing SET status = 'failed' WHERE status = 'fail'");
        DB::statement("ALTER TABLE billing MODIFY COLUMN status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending'");
    }
};
