<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    public function schedule(Schedule $schedule): void
    {
        // Generate sitemap daily at 2 AM
        $schedule->command('sitemap:generate')->dailyAt('02:00');
    }
}