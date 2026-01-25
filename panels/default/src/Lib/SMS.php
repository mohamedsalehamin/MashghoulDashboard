<?php

namespace App\DefaultPanel\Lib;

use Log;
use Illuminate\Support\Facades\Http;
use Iterator;
use Lorisleiva\Actions\Concerns\AsAction;

class SMS {
    public function __construct(public string $phone, public string $message, public string $sender) {
    }

    public static function make($phone, $message, $sender = 'SmSupMrk'): SMS {
        return new self($phone, $message, $sender);
    }

    public function send() {

        $response = Http::withQueryParameters([
            'bearerTokens' => env('SMS_TOKEN', 'c7bf7ae887bd0c3e617491765426ebd6'),
            'sender' => $this->sender,
            'recipients' => $this->phone,
            'body' => $this->message
        ])->post('https://api.taqnyat.sa/v1/messages')
            ->json();
        Log::info("send sms to {$this->phone}", $response);
        return $response;
    }
}
