<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTweetCntInTrendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trend', function (Blueprint $table) {
            $table->dropColumn('tweet_cnt');
            $table->bigInteger('hour_cnt');
            $table->bigInteger('day_cnt');
            $table->bigInteger('week_cnt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trend', function (Blueprint $table) {
            $table->bigInteger('tweet_cnt');
            $table->dropColumn('hour_cnt');
            $table->dropColumn('day_cnt');
            $table->dropColumn('week_cnt');
        });
    }
}
