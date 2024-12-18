<?php

namespace App\DefaultPanel\Actions;

use App\ContentModule\Models\Point;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Models\User;
use App\UsersModule\Models\Users\Customer;
use Lorisleiva\Actions\Concerns\AsAction;


class AddReservationCommissionAction {
    use AsAction;


    public function handle($reservation): void {
        $settings = new GeneralSettings();
        $percentage = 100 - $settings->app_percentage;

        $amount = ($reservation->as_cart->getNetProfitTotal() / 100) * $percentage;
        $reservation->commission()->create([
            'percentage' => $percentage,
            'amount' => $amount
        ]);
    }

}
