<?php

namespace App\Console;

use App\Jobs\ServerExpiry;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new ServerExpiry, 'long')->everyTenMinutes();
        $schedule->command('queue:work --sansdaemon --queue=high,default --tries=3')->everyMinute()->runInBackground();
        $schedule->command('queue:work --sansdaemon --queue=long --tries=3')->everyFiveMinutes()->runInBackground();
        $schedule->command('queue:retry all')->hourly()->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
