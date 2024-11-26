<?php

namespace App\DefaultPanel\Actions\Shared\Order;

use App\Models\User;
use Str;
use App\Coupons\Models\Coupons;
use App\Employees\Models\Contractor;
use App\Orders\Models\Order;
use App\Orders\Models\Order\Timeline;
use App\Orders\Models\OrderStatuses;
use App\Orders\Notifications\NewOrderNotification;
use App\Utilities\Lib\ActionWithData;

class CheckoutTutorialOrderAction extends ActionWithData
{
    protected $data = [];

    public function __construct($cart)
    {
        $order = Order::create([
            "uuid" => Str::uuid(),
            "status" => OrderStatuses::COMPLETED,
            'user_id' => auth()->id(),
            'total' => $cart->getTotal(),
            "date" => now()->toDateTime(),
            "contractor_id" => User::first()?->id,
            'order_type' => 'purchase_tutorials'
        ]);
        $this->data = $order;
        $order->addTimeLine(Timeline::EVENT_CREATED);
    }

    function data()
    {
        return $this->data;
    }
}
