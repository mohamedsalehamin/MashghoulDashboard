<?php

namespace App\DefaultPanel\Lib;

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


class VoipNotification {
    static public function make() {
        return new static();
    }

    public function getOptions() {
        return [
            'key_id' => 'C2T2B439HL', // The Key ID obtained from Apple developer account
            'team_id' => 'AXP4WF2HQ7', // The Team ID obtained from Apple developer account
            'app_bundle_id' => 'org.reactjs.native.example.Tmoono', // The bundle ID for app obtained from Apple developer account
            'private_key_path' => public_path('/AuthKey_C2T2B439HL.p8'),
            'private_key_secret' => null // Private key secret
        ];
    }



    public function send($token, $title, $description, $options) {;
        $authProvider = Token::create($this->getOptions());

        $alert = Alert::create()->setTitle($title);
        $alert = $alert->setBody($description);
        $payload = Payload::create()->setAlert($alert);
        $payload->setSound('default');
        $payload->setCustomValue('uuid', Str::uuid());
        foreach ($options as $key => $value) {
            $payload->setCustomValue($key, $value);
        }
        $payload->setPushType('voip');

        $client = new Client($authProvider, $production = false);
        $client->addNotifications([new Notification($payload, $token)]);
        $client->push();


    }

}
