<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AccountListController;

class SaveFollowingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:saveFollowing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '定期的にユーザのフォロー状態を取得、保存する。';

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
        Log::debug('command:saveFollowing 実施');
        Log::debug('************************');
        $account_list = New AccountListController();
        $account_list->saveFollowingDataAllUsers();
        Log::debug('************************');
        Log::debug('command:saveFollowing 終了');
        Log::debug('************************');
    }
}
