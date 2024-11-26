<?php

namespace App\DefaultPanel\Settings;

use Spatie\LaravelSettings\Settings;

class SMSConfig extends Settings {

    public array $message_on_order_created;
    public array $message_on_order_on_the_way;
    public array $message_on_order_is_delivered;
    public array $message_on_receipt_from_branch;


    public static function group(): string {
        return 'sms';
    }
}
