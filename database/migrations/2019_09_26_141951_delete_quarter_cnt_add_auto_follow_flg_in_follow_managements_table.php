<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteQuarterCntAddAutoFollowFlgInFollowManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('follow_managements', function (Blueprint $table) {
            $table->dropColumn('quarter_cnt');
            $table->boolean('auto_follow_flg')->default(false);
            $table->integer('day_cnt')->default(0)->change();
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
            $table->integer('quarter_cnt');
            $table->dropColumn('auto_follow_flg');
        });
    }
}
