<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTableNameInCryptoTrendUpdateTimeSearchedAccountFollowManagementAutoFollow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('crypto','cryptos');
        Schema::rename('trend','trends');
        Schema::rename('updated_time','updated_times');
        Schema::rename('searched_account','searched_accounts');
        Schema::rename('follow_management','follow_managements');
        Schema::rename('auto_follow','auto_follows');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('cryptos','crypto');
        Schema::rename('trends','trend');
        Schema::rename('updated_times','updated_time');
        Schema::rename('searched_accounts','searched_account');
        Schema::rename('follow_managements','follow_management');
        Schema::rename('auto_follows','auto_follow');
    }
}
