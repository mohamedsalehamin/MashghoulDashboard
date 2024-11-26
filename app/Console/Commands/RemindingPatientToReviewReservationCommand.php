<?php

namespace App\Console\Commands;

use App\CatalogModule\Models\Reservation;
use App\Notifications\RemindingPatientOfReservationDateNotification;
use App\Notifications\ReservationEndNotification;
use Illuminate\Console\Command;

class RemindingPatientToReviewReservationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminding-patient-to-review-reservation-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;
        Reservation::consultations()->timeIsUp()->get()->each(function (Reservation $reservation) use (&$count) {
            $count++;
            $reservation->patient->notify(new ReservationEndNotification($reservation));
        });
        $this->info($count . ' patients have been reminded of their upcoming reservation.');
    }
}
