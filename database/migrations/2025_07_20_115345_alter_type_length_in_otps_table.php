<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('otps', function (Blueprint $table) {
            $table->string('type', 30)->change();
        });
    }

    public function down()
    {
        Schema::table('otps', function (Blueprint $table) {
            $table->string('type', 10)->change(); // আগের মতো
        });
    }
};
