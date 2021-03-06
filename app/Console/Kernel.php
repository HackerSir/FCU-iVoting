<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\QuickBackup::class,
        \App\Console\Commands\LogApacheStatus::class,
        \App\Console\Commands\DeleteUnConfirmUser::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('inspire')->hourly();
        $schedule->command('db:quick-backup')->dailyAt('5:00');
        $schedule->command('user:delete-unconfirm')->dailyAt('5:30');
        //$schedule->command('apache-status:log')->everyMinute();
    }
}
