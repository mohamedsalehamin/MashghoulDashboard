<?php

namespace App\Console\Commands;

use Log;
use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Actions\AddPointToCustomerAction;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Notifications\WiningGiftSuccessfullyNotification;
use App\UsersModule\Models\Users\Customer;
use Illuminate\Console\Command;

class CancelUnPaidReservations extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cancel-unpaid-reservations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel unpaid reservations';

    /**
     * Execute the console command.
     */
    public function handle() {
        $i = 0;
        $reservations = Reservation::where('status', ReservationStatus::PENDING)
            ->where('created_at', ">=", now()->subHours(2))
            ->whereHas('transactions', fn($query) => $query->where('status', 'pending'))
            ->get();
        foreach ($reservations as $reservation) {
            $i++;
            $reservation->update(['status' => ReservationStatus::CANCELED]);
        }
        Log::info("CancelUnPaidReservations $i");
        $this->info("CancelUnPaidReservations $i");
    }
}
