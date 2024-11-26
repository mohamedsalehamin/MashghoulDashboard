<?php

namespace App\DefaultPanel\Actions\Doctor;

use App\UsersModule\Models\Service;
use Darryldecode\Cart\Exceptions\InvalidConditionException;
use Lorisleiva\Actions\Concerns\AsAction;
use App\DoctorPanel\Filament\Resources\Product;
use App\CrmModule\Models\AddressBook;
use App\DefaultPanel\Lib\Cart;


class BuildCartInstanceAction {
    use AsAction;

    protected $data = [];

    /**
     * @throws InvalidConditionException
     */
    public function handle() {
        /**
         *
         * @var Cart $cart
         * */
        $cart = app('cart');

        $service = Service::find(request()->get('service_id'));
        $cart->applyItem($service,1, [], []);

//        $cart->applyTaxes();



        return $cart;

    }

}
