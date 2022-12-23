<?php

namespace App\Console;

use App\Console\Commands\ClearExpiredOtpsCommand;
use App\Console\Commands\DeleteOldUnverifiedTenantsCommand;
use App\Console\Commands\RenewTenantsSubscriptionsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')->hourly();

        $schedule->command(RenewTenantsSubscriptionsCommand::class)
            ->dailyAt('1:00')
            ->timezone('Africa/Lagos');

        $schedule->command(DeleteOldUnverifiedTenantsCommand::class)
            ->dailyAt('6:00')
            ->timezone('Africa/Lagos');

        $schedule->command(ClearExpiredOtpsCommand::class)
            ->fridays()
            ->at('6:00')
            ->timezone('Africa/Lagos');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
