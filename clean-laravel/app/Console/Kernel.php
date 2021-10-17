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
        Commands\MorningNotification::class,
        Commands\ML2::class,
        Commands\Challenge::class,
        Commands\ChallengeDaily::class,
        Commands\UsageNotification::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('morning:notification')->dailyAt('08:00')->timezone('Asia/Bangkok'); 
        $schedule->command('ML2:notification')->everyThirtyMinutes()->timezone('Asia/Bangkok');
        $schedule->command('challenge:notification')->monthly()->timezone('Asia/Bangkok');
        $schedule->command('challengedaily:notification')->everyFiveMinutes()->timezone('Asia/Bangkok');
        $schedule->command('usage:notification')->everyFiveMinutes()->timezone('Asia/Bangkok');
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
