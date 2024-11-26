<?php

namespace App\DefaultPanel\Actions\Labs;

use App\UsersModule\Models\Lab\Service;
use Darryldecode\Cart\Exceptions\InvalidConditionException;
use Lorisleiva\Actions\Concerns\AsAction;
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

        $services = Service::findMany(request()->get('services'));
        foreach ($services as $service){
            $cart->applyItem($service,1, [], []);
        }




        return $cart;

    }

}
