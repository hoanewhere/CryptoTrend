<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AccountListController;

class AutoFollowCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:autoFollow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '定期的に、ツイッターアカウントの自動フォローをする';

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
        Log::info('command:autoFollow 開始');
        Log::info('************************');
        $account_list = New AccountListController();
        $account_list->toFollowAutoLimit();
        Log::info('************************');
        Log::info('command:autoFollow 終了');
        Log::info('************************');
    }
}
