<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DelayaOrderDelay;
use App\Console\Commands\GiftsEventsReminder;
use App\Console\Commands\TaxiLocationUpdator;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

        //DelayaOrderDelay::class,
        //GiftsEventsReminder::class,
        TaxiLocationUpdator::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule -> exec("php artisan schedule:run");
        //$schedule->command('Notify:OrderDelay')->everyMinute();
        //$schedule->command('Notify:GiftsEventsReminder')->dailyAt('06:00');
        $schedule->command('Notify:TaxiLocationUpdator')->everyMinute('06:00');
    }
}
