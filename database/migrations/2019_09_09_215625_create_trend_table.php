<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trend', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('crypto_id');
            $table->integer('search_term');
            $table->bigInteger('tweet_cnt');
            $table->double('transaction_price_max');
            $table->double('transaction_price_min');
            $table->bigInteger('time_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trend');
    }
}
