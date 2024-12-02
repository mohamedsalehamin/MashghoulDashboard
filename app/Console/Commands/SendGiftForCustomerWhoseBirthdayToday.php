<?php

namespace App\Console\Commands;

use App\DefaultPanel\Actions\AddPointToCustomerAction;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Notifications\WiningGiftSuccessfullyNotification;
use App\UsersModule\Models\Users\Customer;
use Illuminate\Console\Command;

class SendGiftForCustomerWhoseBirthdayToday extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-gift-for-customer-whose-birthday-today';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send gift for customer whose birthday today';

    /**
     * Execute the console command.
     */
    public function handle() {

        Customer::whereMonth('dob', now()->month)
            ->whereDay('dob', now()->day)
            ->get()->each(function (Customer $customer) {
                $customer->notify(new WiningGiftSuccessfullyNotification([
                    'ar' => __('panel.messages.send_gift_for_customer_whose_birthday_today', ['points' => GeneralSettings::getPointsOnAction('dob')], 'ar'),
                    'en' => __('panel.messages.send_gift_for_customer_whose_birthday_today', ['points' => GeneralSettings::getPointsOnAction('dob')], 'en'),

                ]));
                AddPointToCustomerAction::run($customer, GeneralSettings::getPointsOnAction('dob'), ['description' => [
                    'ar' => __("panel.messages.gift_for_birthday", [], 'ar'),
                    'en' => __("panel.messages.gift_for_birthday", [], 'en')
                ]]);
            });
    }
}
