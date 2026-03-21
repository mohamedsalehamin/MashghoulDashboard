<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
|
| Here you may define all of your scheduled tasks. The scheduler will run
| these tasks automatically based on their scheduled times.
|
*/

Schedule::command('app:cancel-unpaid-reservations')->hourly();
Schedule::command('app:notify-provider-about-processing-unfinised-reservations')->everySixHours();
Schedule::command('app:send-gift-for-customer-whose-birthday-today')->daily();

// Capture Tabby pending transactions every 4 minutes
Schedule::command('app:capture-pending-transactions --hours=2')->everyFourMinutes();

Schedule::command('sitemap:generate')->daily();

// Provider subscription notifications: 3 days before + 1 day after expiry
Schedule::command('app:provider-subscription-notifications')->daily();
