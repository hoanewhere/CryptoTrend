<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DaylyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:Dayly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '１日に１回実施するコマンドをまとめたもの。';

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
        Log::info('************************');
        Log::info('clearFollowCnt 実施');
        Log::info('************************');
        AccountListController::clearFollowCntOfDayLimit();
        Log::info('************************');
        Log::info('clearFollowCnt 終了');
        Log::info('************************');
    }
}
