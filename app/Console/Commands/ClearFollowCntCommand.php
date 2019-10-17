<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AccountListController;

class ClearFollowCntCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:clearFollowCnt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自動フォローのカウントをクリアする(1000/1day)';

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
        Log::debug('command:clearFollowCnt 実施');
        Log::debug('************************');
        AccountListController::clearFollowCntOfDayLimit();
        Log::debug('************************');
        Log::debug('command:clearFollowCnt 終了');
        Log::debug('************************');
    }
}
