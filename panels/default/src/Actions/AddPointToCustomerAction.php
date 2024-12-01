<?php

namespace App\DefaultPanel\Actions;

use App\ContentModule\Models\Point;
use App\Models\User;
use App\UsersModule\Models\Users\Customer;
use Lorisleiva\Actions\Concerns\AsAction;


class AddPointToCustomerAction {
    use AsAction;


    public function handle(User $customer, $points, $meta_data): void {
        Point::create([
            'user_id' => $customer->id,
            'reset_points'=>$points,
            'points_count'=>$points,
            'transferred' => 0,
            'meta_data' => $meta_data
        ]);
        return;
    }

}
