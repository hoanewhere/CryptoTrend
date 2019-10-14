<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnForSearchedAccountIdInTwitterFollowingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twitter_followings', function (Blueprint $table) {
            // $table->renameColumn('searched_account_id', 'searched_twitter_id_str');
            $table->string('searched_account_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twitter_followings', function (Blueprint $table) {
            $table->bigInteger('searched_account_id')->change();
            // $table->renameColumn('searched_twitter_id_str', 'searched_account_id');
        });
    }
}
