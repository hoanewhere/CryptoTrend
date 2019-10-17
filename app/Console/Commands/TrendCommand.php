<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TrendRankingController;

class TrendCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:aggregateTweetTrend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '対象ワードのツイート数の集計を実施';

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
        Log::debug('************************');
        Log::debug('command:aggregateTweetTrend 実施');
        Log::debug('************************');
        $trend_ranking = New TrendRankingController();
        $trend_ranking->aggregateTweetTrend();
        Log::debug('************************');
        Log::debug('command:aggregateTweetTrend 終了');
        Log::debug('************************');
    }
}
