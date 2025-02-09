<?php

namespace App\DefaultPanel\Api\V1\Customer;

use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Actions\AddReservationCommissionAction;
use App\DefaultPanel\Actions\BuildCartInstanceAction;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Requests\Api\Customer\Order\CartCheckoutRequest;
use App\DefaultPanel\Requests\Api\Customer\Order\CartDetailsRequest;
use App\DefaultPanel\Resources\Api\Customer\Cart\CartResource;
use App\DefaultPanel\Resources\Api\Provider\ReservationResource;
use App\DefaultPanel\Settings\GeneralSettings;
use App\UsersModule\Models\Provider;
use Tasawk\Api\Facade\Api;


class CartServices {
    public function details(CartDetailsRequest $request, Provider $provider) {

        return Api::isOk(__("Cart details"), CartResource::make($request->cart()));
    }

    public function checkout(CartCheckoutRequest $request, Provider $provider) {
        $cart = BuildCartInstanceAction::run($request);
        $total = $provider->user?->options?->reservation_flow == 'fees' ? $cart->totals()['reservation_fees_include_taxes'] : $cart->getTotal();

        /**
         * @var Reservation $reservation
         * */
        $reservation = $provider->reservations()->create([
            'user_id' => auth()->id(),
            'seat_id' => $request->get('seat_id'),
            'date' => $request->date('date'),
            'from' => $request->date('from'),
            'to' => $request->date('to'),
            'status' => ReservationStatus::PENDING,
            'price' => $cart->getTotal(),
            'meta_data' => [
                'points' => GeneralSettings::getPointsOnAction('reserve'),
                'reservation_flow' => $provider->user?->options?->reservation_flow
            ]
        ]);
        $cart->saveItemsToOrder($reservation->id);

        if ($request->filled('wallet')) {
            $reservation->pay($request->float('wallet'), 'wallet');
        }
        if ($request->filled('points')) {
            $reservation->pay($request->float('points'), 'points');
        }
        if ($total> 0 ) {
            $reservation->pay($total);
        }
        AddReservationCommissionAction::run($reservation);
        return Api::isOk(__("Reservation created"), ReservationResource::make($reservation));
    }


}
