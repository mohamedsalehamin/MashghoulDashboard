<?php

namespace App\Console\Commands;

use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Enum\ReservationStatus;
use App\Notifications\RemindingPatientOfReservationDateNotification;
use App\Notifications\ReservationEndNotification;
use Illuminate\Console\Command;

class ChangeCompleteReservationStatusCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:change-complete-reservation-status-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle() {
        $count = 0;
        Reservation::timeIsUp()->get()->each(function (Reservation $reservation) use (&$count) {
            $reservation->update(['status' => ReservationStatus::COMPLETED]);
            $count++;
        });
        $this->info($count . ' reservations have been completed.');
    }
}
