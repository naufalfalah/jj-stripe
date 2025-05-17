<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

// use App\Console\Commands\ProcessCheckoutsCommand;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\ProcessCheckoutsCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('daily-ads-spents:check')->everyTwoMinutes();
        $schedule->command('apilog:delete-old')->daily();
        $schedule->command('notifications:send-scheduled')->everyMinute();
        // $schedule->command('checkout:pending')->everyMinute();
        // $schedule->command('inspire')->hourly();
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
