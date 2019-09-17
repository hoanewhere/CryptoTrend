<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTweetCntDefaultInTrendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trends', function (Blueprint $table) {
            $table->bigInteger('hour_cnt')->default(0)->change();
            $table->bigInteger('day_cnt')->default(0)->change();
            $table->bigInteger('week_cnt')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trends', function (Blueprint $table) {
            $table->bigInteger('hour_cnt')->default(false);
            $table->bigInteger('day_cnt')->default(false);
            $table->bigInteger('week_cnt')->default(false);
        });
    }
}
