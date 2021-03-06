<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AccountListController;


class GetUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '一日に一回、対象のツイッターアカウントを取得する';

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
        Log::debug('command:getUsers 実施');
        Log::debug('************************');
        $account_list = New AccountListController();
        $account_list->getUsers();
        Log::debug('************************');
        Log::debug('command:getUsers 終了');
        Log::debug('************************');
    }
}
