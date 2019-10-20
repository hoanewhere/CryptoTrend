<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDefaultToDayLimitFlgInFollowManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('follow_managements', function (Blueprint $table) {
            $table->boolean('day_limit_flg')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('follow_managements', function (Blueprint $table) {
            $table->boolean('day_limit_flg')->change();
        });
    }
}
