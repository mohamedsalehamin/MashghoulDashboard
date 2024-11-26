<?php

namespace App\DefaultPanel\Lib;

use Http;
use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Enum\DeliveryMethods;

class Shipox {
    public static function make() {
        return new static();
    }

    public function createOrder(Order $order) {
        $authData = $this->authData();
        return Http::withToken($authData['id_token'])->post('https://prodapi.shipox.com/api/v2/customer/order', [
            "sender_data" => [
                "address_type" => "business",
                "name" => $authData['user']['full_name'] ?? '',
                "email" => $authData['user']['email'] ?? '',
                "apartment" => "",
                "building" => "",
                "street" => "Istanbul Street, near from Fetchr warehouse no7 AlSuly, Almashael",
                "landmark" => "",
                "city" => [
                    "code" => "riyadh"
                ],
                "country" => [
                    "id" => 191
                ],
                "phone" => $authData['user']['phone'] ?? ''
            ],
            "recipient_data" => [
                "address_type" => "business",
                "name" => $order->customer->name,
                "email" => $order->customer->email,
                "phone" => $order->customer->phone,
                "apartment" => "",
                "building" => "",
                "street" => $order->address->address_name,
                "landmark" => "",
                "city" => [
                    "id" => $order->address->city_id
                ],
                "country" => [
                    "id" => 191
                ],

            ],
            "dimensions" => [
                "weight" => 1,
                "width" => 10,
                "length" => 10,
                "height" => 10,
                "unit" => "METRIC",
                "domestic" => false
            ],
            "package_type" => [
                "courier_type" => $order->receipt_method == DeliveryMethods::DELIVERY ? 'COLD_SC' : 'COLD_I'
            ],
            "charge_items" => [
                [
                    "charge_type" => "cod",
                    "charge" => 188,
                    "payer" => "recipient"
                ],
                [
                    "charge_type" => "service_custom",
                    "charge" => 0,
                    "payer" => "recipient"
                ]
            ],
            "recipient_not_available" => "do_not_deliver",
            "payment_type" => "credit_balance",
            "payer" => "recipient",
            "parcel_value" => 0,
            "fragile" => false,
            "note" => json_encode($order->address->map_location),
            "piece_count" => $order->itemsLine()->count(),
            "force_create" => true,
            "reference_id" => $order->id,
        ])->json();
    }

    public function authData() {
        return Http::post('https://prodapi.shipox.com/api/v1/customer/authenticate', [
            "username" => "info@anamcom.com",
            "password" => "Anaamcom@123",
            "remember_me" => true
        ])->collect('data');
    }

    public function cities($page = 1) {
        return Http::withToken($this->authData()['id_token'])->get("https://prodapi.shipox.com/api/v2/customer/cities?country_id=191&page=$page")->collect('data');
    }
}
