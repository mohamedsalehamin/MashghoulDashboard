<?php

namespace App\DefaultPanel\Lib;

use Darryldecode\Cart\CartCollection;
use App\Currencies\Models\Currency;
use App\Ecommerce\Models\Cart;

class ArrayStorage {

    private array $cart = [];

    public function has($key) {
        return isset($this->cart[$key]);
    }

    public function get($key): CartCollection|array {
        if ($this->has($key)) {
            return new CartCollection($this->cart[$key]);
        } else {
            return [];
        }
    }

    public function put($key, $value) {
        $this->cart[$key] = $value;
    }
}

