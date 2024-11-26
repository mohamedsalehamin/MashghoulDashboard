<?php

namespace App\DefaultPanel\Lib;

use Iterator;
use Lorisleiva\Actions\Concerns\AsAction;

class SMS {
    use AsAction;

    public function handle($phone, $message) {
        \Http::get("https://mora-sa.com/api/v1/sendsms", [
            'username' => "Mosa",
            'sender' => 'TMOONO',
            'message' => $message,
            'numbers' => $phone,
            'response' => 'json',
            'api_key' => 'd32c251e97c6c2fe7d7d05c6b6228c5c388d98d3'
        ])->json();
    }
}
