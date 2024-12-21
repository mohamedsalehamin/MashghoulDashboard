<?php

namespace App\DefaultPanel\Api\V1\Customer\Profile;

use App\CatalogModule\Models\Plan;
use App\ContentModule\Models\Level;
use App\ContentModule\Models\Point;
use App\DefaultPanel\Resources\Api\Customer\ExchangesPointResource;
use App\DefaultPanel\Resources\Api\Customer\MyPointResource;
use App\DefaultPanel\Resources\Api\Customer\UsagesPointResource;
use App\Models\PointsExchange;
use App\Models\PointsUsage;
use Tasawk\Api\Facade\Api;

class PointService {

    public function index() {

        return Api::isOk(__("Points"), MyPointResource::collection(auth()->user()->points()->latest()->paginate()))
            ->addAttribute('points', auth()->user()->getTotalPoints());
    }

    public function exchanges() {
        return Api::isOk(__("Points"), ExchangesPointResource::collection(PointsExchange::where('user_id', auth()->id())->latest()->paginate()));
    }

    public function usages() {
        return Api::isOk(__("Points"), UsagesPointResource::collection(PointsUsage::where('user_id', auth()->id())->latest()->paginate()));
    }

    public function exchange(Level $plan) {

        //get all User points
        $points = auth()->user()->points()->where('transferred', false);
//get total points
        $userTotalPoints = $points->sum('reset_points');
        $planTotalPoints = $plan->value;
//check if user has enough points
        if ($userTotalPoints < $planTotalPoints) {
            return Api::isError(__("You don't have enough points"));
        }
//loop through all points and deduct the points

        foreach ($points->get() as $point) {
            if ($planTotalPoints >= $point->reset_points) {

                $planTotalPoints -= $point->reset_points;
                $point->update(['transferred' => true, 'reset_points' => 0]);
            } else {
                $point->update(['reset_points' => $point->reset_points - $planTotalPoints]);
                break;
            }

        }

        PointsExchange::create([
            'user_id' => auth()->id(),
            'level_id' => $plan->id,
            'points' => $plan->value,
            'price' => $plan->price,
            'reset_price' => $plan->price,
            'used' => false,
            'expired_at' => now()->addDays($plan->duration),
        ]);
        return Api::isOk(__("Points converted successfully"));
    }


}
