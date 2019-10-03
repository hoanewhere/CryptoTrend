<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountDataJsonInSearchedAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('searched_accounts', function (Blueprint $table) {
            $table->json('account_data');
            $table->dropColumn('twitter_account');
            $table->dropColumn('twitter_user_id');
            $table->dropColumn('profile_img_url');
            $table->dropColumn('follow_cnt');
            $table->dropColumn('follower_cnt');
            $table->dropColumn('discription');
            $table->dropColumn('recent_tweet_json');
            $table->dropColumn('follow_flg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('searched_accounts', function (Blueprint $table) {
            $table->dropColumn('account_data');
            $table->string('twitter_account');
            $table->string('twitter_user_id');
            $table->string('profile_img_url');
            $table->bigInteger('follow_cnt');
            $table->bigInteger('follower_cnt');
            $table->string('discription');
            $table->json('recent_tweet_json');
            $table->boolean('follow_flg');
        });
    }
}
