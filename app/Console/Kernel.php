<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
//        \App\Console\Commands\PreTestCommand::class,
        \App\Console\Commands\BackupWithServerCommand::class,
        \App\Console\Commands\VerifyBalanceSyncCommand::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Verify balance sync nightly at 02:00 AM
        // Detect & fix any desync between user_balances and user_balance_transactions
        $schedule->command('balance:verify-sync --fix')
            ->dailyAt('02:00')
            ->name('balance-verify-sync')
            ->onFailure(function () {
                // Log failure, send alert if needed
                \Log::error('Balance sync verification failed');
            });

        // Full rebuild monthly (1st day of month at 03:00 AM)
        // Full recalculation from all transactions
        $schedule->command('balance:verify-sync --rebuild')
            ->monthlyOn(1, '03:00')
            ->name('balance-rebuild-monthly')
            ->onFailure(function () {
                \Log::error('Balance rebuild failed');
            });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
//        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
