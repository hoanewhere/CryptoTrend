<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteTableInAutoFollowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('auto_follows');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('auto_follows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('auto_follow_flg');
            $table->bigInteger('login_user_id');
            $table->timestamps();
        });
    }
}
