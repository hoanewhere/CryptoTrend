<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\Type;

class ChangeTweetCntInTrendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }

        Schema::table('trend', function (Blueprint $table) {
            $table->dropColumn('tweet_cnt');
            $table->dropColumn('search_term');
            $table->bigInteger('hour_cnt');
            $table->bigInteger('day_cnt');
            $table->bigInteger('week_cnt');
            $table->double('transaction_price_max')->nullable()->change();
            $table->double('transaction_price_min')->nullable()->change();
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
            $table->integer('search_term');
            $table->dropColumn('hour_cnt');
            $table->dropColumn('day_cnt');
            $table->dropColumn('week_cnt');
            $table->double('transaction_price_max')->nullable(false)->change();
            $table->double('transaction_price_min')->nullable(false)->change();
        });
    }
}
