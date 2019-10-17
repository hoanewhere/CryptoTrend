<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HourlyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:Hourly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '１時間に１回実施するコマンドをまとめたもの。';

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
        Log::info('saveFollowing 実施');
        Log::info('************************');
        $account_list = New AccountListController();
        $account_list->saveFollowingDataAllUsers();
        Log::info('************************');
        Log::info('saveFollowing 終了');
        Log::info('************************');
    }
}
