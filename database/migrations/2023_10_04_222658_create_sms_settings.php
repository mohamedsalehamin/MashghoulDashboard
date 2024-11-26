<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('sms.message_on_order_created', [
            "ar" => "مرحبا بك في مطعم كوفة، تم استلام طلبك بنجاح",
            "en" => "Welcome to Kufa Restaurant, your order has been received successfully"
        ]);
        $this->migrator->add('sms.message_on_order_on_the_way', [
            "ar" => "لقد تم تحضير طلبك بنجاح وسوف يتم توصيله لك في اقرب وقت",
            "en" => "Your order has been successfully prepared and will be delivered to you as soon as possible"
        ]);
        $this->migrator->add('sms.message_on_order_is_delivered', [
            "ar" => "بإلهنا والشفا، مطاعم كوفة تتمنى لكم وجبة شهية",
            "en" => "May God bless you and grant you peace, Kufa Restaurants wishes you a delicious meal"
        ]);
        $this->migrator->add('sms.message_on_receipt_from_branch', [
            "ar" => "بإلهنا والشفا، مطاعم كوفة تتمنى لكم وجبة شهية",
            "en" => "May God bless you and grant you peace, Kufa Restaurants wishes you a delicious meal"
        ]);

    }
};
