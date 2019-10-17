<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TrendRankingController;
use App\Http\Controllers\AccountListController;


class TenMinutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:TenMinutes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '10分に１回実施するコマンドをまとめたもの。';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // autoFollow
        Log::debug('************************');
        Log::debug('autoFollow 開始');
        Log::debug('************************');
        $account_list = New AccountListController();
        $account_list->toFollowAutoLimit();
        Log::debug('************************');
        Log::debug('autoFollow 終了');
        Log::debug('************************');

        // getUsers
        Log::debug('************************');
        Log::debug('getUsers 実施');
        Log::debug('************************');
        $account_list = New AccountListController();
        $account_list->getUsers();
        Log::debug('************************');
        Log::debug('getUsers 終了');
        Log::debug('************************');

        // aggregateTweetTrend
        Log::debug('************************');
        Log::debug('aggregateTweetTrend 実施');
        Log::debug('************************');
        $trend_ranking = New TrendRankingController();
        $trend_ranking->aggregateTweetTrend();
        Log::debug('************************');
        Log::debug('aggregateTweetTrend 終了');
        Log::debug('************************');
    }
}
