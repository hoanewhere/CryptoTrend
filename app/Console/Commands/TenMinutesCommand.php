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
        Log::info('************************');
        Log::info('autoFollow 開始');
        Log::info('************************');
        $account_list = New AccountListController();
        $account_list->toFollowAutoLimit();
        Log::info('************************');
        Log::info('autoFollow 終了');
        Log::info('************************');

        // getUsers
        Log::info('************************');
        Log::info('getUsers 実施');
        Log::info('************************');
        $account_list = New AccountListController();
        $account_list->getUsers();
        Log::info('************************');
        Log::info('getUsers 終了');
        Log::info('************************');

        // aggregateTweetTrend
        Log::info('************************');
        Log::info('aggregateTweetTrend 実施');
        Log::info('************************');
        $trend_ranking = New TrendRankingController();
        $trend_ranking->aggregateTweetTrend();
        Log::info('************************');
        Log::info('aggregateTweetTrend 終了');
        Log::info('************************');
    }
}
