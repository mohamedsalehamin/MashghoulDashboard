<?php

namespace App\DefaultPanel\Api\V1;

use App\DefaultPanel\Actions\Labs\BuildCartInstanceAction;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Requests\Api\Order\CartDetailsRequest;
use App\DefaultPanel\Resources\Api\Cart\CartResource;
use App\DefaultPanel\Resources\Api\ReservationResource;
use App\UsersModule\Models\Provider;
use Tasawk\Api\Facade\Api;


class CartServices {
    public function details(CartDetailsRequest $request,Provider $provider) {
        $cart = BuildCartInstanceAction::run($request);
        return CartResource::make($cart);
    }

    public function checkout(CartDetailsRequest $request, Provider $provider) {

        $cart = BuildCartInstanceAction::run($request);
        $reservation = $provider->reservations()->create([
            'user_id' => auth()->id(),
            'seat_id' => $request->get('seat_id'),
            'date' => $request->date('date'),
            'from' => $request->date('from'),
            'to' => $request->date('to'),
            'status' => ReservationStatus::PENDING,
            'price' => $cart->getTotal()
        ]);
        $cart->saveItemsToOrder($reservation->id);
        $reservation->pay();
        return Api::isOk(__("Reservation created"), ReservationResource::make($reservation));
    }


}
