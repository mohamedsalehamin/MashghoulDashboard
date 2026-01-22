<?php

use Tasawk\Api\Facade\Api;

if(!function_exists('generate_api_token')) {
    function generate_api_token() {
        $random = \Illuminate\Support\Str::random(60);
        $check = \App\Models\User::where(['api_token' => $random])->first();
        if (!is_null($check)) {
            generate_api_token();
        }
        return $random;
    }
}

if(!function_exists('api_model_set_pagination')) {
    function api_model_set_pagination($model) {
        $pagination['total'] = $model->total();
        $pagination['lastPage'] = $model->lastPage();
        $pagination['perPage'] = $model->perPage();
        $pagination['currentPage'] = $model->currentPage();
        return $pagination;
    }
}
if(!function_exists('abort_with_message')) {
    function abort_with_message($key,$value) {
        return Api::setStatusError()
            ->setMessage("something went Wrong")
            ->setErrors(['key' => $key, 'value' => $value,])
            ->build();
    }
}


if (!function_exists('PushNotification')) {
    function PushNotification($device_token, $data, $type = 1)
    {
        $fields = [
            'registration_ids' => (is_array($device_token)) ? $device_token : [$device_token],
            'notification' => [
                'title' => $data['title'],
                'body' => $data['message'],
                'type' => $type,
                'tickerText' => '',
                'vibrate' => 1,
                'sound' => 1,
                'largeIcon' => 'large_icon',
                'smallIcon' => 'small_icon',
            ]
        ];
        $headers = [
            'Authorization: key= '.settings()->group('third-party')->get('fire_base_server_key'),
            'Content-Type: application/json'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

if (!function_exists('push_send')) {
    function push_send($data = ['title' => '', 'message' => ''], $tokens = [])
    {
        $pp = [];
        if (count($tokens) > 500) {
            $arrays = array_chunk($tokens, 500);
            foreach ($arrays as $array) {
                $pp[] = PushNotification($array, $data, 1);
            }
        } else {
            $arrays = $tokens;
        }
        if (!empty($arrays)) {
            $pp[] = PushNotification($arrays, $data, 1);
        }
        return $pp;
    }
}

if (!function_exists('moneyFormat')) {

    function moneyFormat($value = 0)
    {

        return number_format($value,2,",",".");
    }
}
