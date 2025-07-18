<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

// database/migrations/xxxx_xx_xx_add_user_id_to_owners_table.php
    public function up()
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
             $table->dropColumn('password'); // <-- এই লাইনটি যোগ করুন
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            //
        });
    }
};
