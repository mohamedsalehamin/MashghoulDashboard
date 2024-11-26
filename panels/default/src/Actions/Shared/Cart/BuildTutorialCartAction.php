<?php

namespace App\DefaultPanel\Actions\Shared\Cart;

use App\Employees\Models\Tutorial;
use App\Utilities\Lib\ActionWithData;
use Utilities;

class BuildTutorialCartAction extends ActionWithData {
    protected $data = [];
    protected $cart;

    public function __construct($request) {
        $cart = Utilities::DBCart(md5("API_CART_" . uniqid()), 'api_cart');

        foreach ($request->input('tutorials_ids', []) as $tutorial) {
            $cart->applyItem(Tutorial::find($tutorial), 1);
        }
        $this->data = $cart;
    }

    function data() {
        return $this->data;
    }
}
