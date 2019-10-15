<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteLoginIdInSearchedAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('searched_accounts', function (Blueprint $table) {
            $table->dropColumn('login_user_id');
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
            $table->bigInteger('login_user_id');
        });
    }
}
