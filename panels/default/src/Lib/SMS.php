<?php

namespace App\DefaultPanel\Lib;

use Illuminate\Support\Facades\Http;
use Iterator;
use Lorisleiva\Actions\Concerns\AsAction;

class SMS {
    use AsAction;

    public function handle($phone, $message) {

        $response = Http::withQueryParameters([
            'bearerTokens' => env('SMS_TOKEN', 'c7bf7ae887bd0c3e617491765426ebd6'),
            'sender' => env("SMS_SENDER_NAME", ''),
            'recipients' => $phone,
            'body' => $message
        ])->post('https://api.taqnyat.sa/v1/messages')
            ->json();
        \Log::info("send sms to $phone", $response);
        return $response;
    }
}
