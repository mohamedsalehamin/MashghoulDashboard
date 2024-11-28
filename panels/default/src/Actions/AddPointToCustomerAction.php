<?php

namespace App\DefaultPanel\Actions;

use App\ContentModule\Models\Point;
use App\UsersModule\Models\Users\Customer;
use Lorisleiva\Actions\Concerns\AsAction;


class AddPointToCustomerAction {
    use AsAction;


    public function handle(Customer $customer, $points, $meta_data): void {
        Point::create([
            'user_id' => $customer->id,
            'points'=>$points,
            'type' => 'customer',
        ]);
        return;
    }

}
