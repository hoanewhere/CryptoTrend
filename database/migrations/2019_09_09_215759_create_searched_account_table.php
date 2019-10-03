<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchedAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('searched_account', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('twitter_account');
            $table->string('twitter_user_id');
            $table->string('profile_img_url');
            $table->bigInteger('follow_cnt');
            $table->bigInteger('follower_cnt');
            $table->string('discription');
            $table->json('recent_tweet_json');
            $table->boolean('follow_flg');
            $table->bigInteger('login_user_id');
            $table->bigInteger('update_time_id');
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
        Schema::dropIfExists('searched_account');
    }
}
