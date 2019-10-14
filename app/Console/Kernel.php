<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Http\Controllers\TrendRankingController;
use App\Http\Controllers\AccountListController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 一日に一回、対象ワードのツイート数の集計を実施
        // （一回の取得件数に制限があるため、その日の集計が完了するまで本タスクを定期的に実施）
        // $schedule->call(function() {
        //     $trend_ranking = New TrendRankingController();
        //     $trend_ranking->aggregateTweetTrend();
        // })->everyFiveMinutes()
        // ->name('task-aggregateTweetTrend')
        // ->withoutOverlapping();

        // 1日に１回、対象のツイッターユーザー情報を取得、更新する
        $schedule->call(function() {
            $account_list1 = New AccountListController();
            $account_list1->getUsers();
        })->everyTenMinutes()
        ->name('task-getUsers')
        ->withoutOverlapping();

        // 定期的に、ツイッターアカウントの自動フォローをする
        //（API制限にかからないように実施(15/15min, 1000/1day)）
        // $schedule->call(function() {
        //     $account_list2 = New AccountListController();
        //     $account_list2->toFollowAutoLimit();
        // })->everyFifteenMinutes()
        // ->name('task-autoFollow')
        // ->withoutOverlapping();

        // 一日に一回、自動フォローのカウントをクリアする(1000/1day)
        $schedule->call(function() {
            AccountListController::clearFollowCntOfDayLimit();
        })->daily()
        ->name('task-clearFollowCnt')
        ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
