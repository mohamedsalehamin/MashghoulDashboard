<?php

namespace App\Console\Commands;

use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Actions\AddPointToCustomerAction;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Notifications\WiningGiftSuccessfullyNotification;
use App\UsersModule\Models\Users\Customer;
use Illuminate\Console\Command;
use App\Notifications\ReservationProcessingUnfinisedNotification;
class NotifyProviderProcessingUnfinisedReservations extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-provider-about-processing-unfinised-reservations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify provider about processing unfinised reservations';

    /**
     * Execute the console command.
     */
    public function handle() {
        $i = 0;
        $reservations = Reservation::where('status', ReservationStatus::PROCESSING)
            ->where('to','>',now())
            ->get();
        foreach ($reservations as $reservation) {
            $i++;
            $reservation->reservable->user->notify->notify(new ReservationProcessingUnfinisedNotification($reservation));
        }
        \Log::info("NotifyProviderProcessingUnfinisedReservations $i");
        $this->info("NotifyProviderProcessingUnfinisedReservations $i");
    }
}
