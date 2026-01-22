<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void {
        $schedule->command('app:cancel-unpaid-reservations')->hourly();
        $schedule->command('app:notify-provider-about-processing-unfinised-reservations')->everySixHours();
        $schedule->command('app:send-gift-for-customer-whose-birthday-today')->daily();
        
        // Capture Tabby pending transactions every 30 minutes
        $schedule->command('app:capture-pending-transactions --hours=2')->everyFourMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void {
        $this->load(__DIR__ . '/Commands');

    }
}
