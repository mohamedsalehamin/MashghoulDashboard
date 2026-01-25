<?php

namespace App\DefaultPanel\Actions;

use Http;
use App\UsersModule\Models\Lab\Service;
use Darryldecode\Cart\Exceptions\InvalidConditionException;
use Lorisleiva\Actions\Concerns\AsAction;
use App\DefaultPanel\Lib\Cart;
use Pushok\AuthProvider\Token;
use Pushok\Client;
use Pushok\Notification;
use Pushok\Payload;
use Pushok\Payload\Alert;
use Str;


class GetUserLocationBasedOnIPAction {
    use AsAction;

    public function handle($ip) {
        return Http::get("http://ip-api.com/json/$ip")->collect()->only('lat', 'lon')->values();
    }

}
