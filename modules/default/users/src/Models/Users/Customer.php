<?php

namespace App\UsersModule\Models\Users;


use App\Notifications\WiningGiftSuccessfullyNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\DefaultPanel\Actions\AddPointToCustomerAction;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Models\User;

class Customer extends User {
    protected $guarded = ['id'];
    const ROLE = 'customer';


    protected static function booted() {
        parent::booted();
        static::addGlobalScope('customer', fn($query) => $query->whereHas('roles', fn($query) => $query->where('name', self::ROLE)));
        static::created(function ($customer) {
            AddPointToCustomerAction::run($customer, GeneralSettings::getPointsOnAction('register'), ['description' => [
                'ar' => __("panel.messages.gift_for_register", [], 'ar'),
                'en' => __("panel.messages.gift_for_register", [], 'en')
            ]]);
            $customer->notify(new WiningGiftSuccessfullyNotification([
                'ar' => __("panel.messages.you_have_gain_points_due_to_register", ['points' => GeneralSettings::getPointsOnAction('register')], 'ar'),
                'en' => __("panel.messages.you_have_gain_points_due_to_register", ['points' => GeneralSettings::getPointsOnAction('register')], 'en'),
            ]));
            return $customer->assignRole(self::ROLE);
        });

    }

    public function getMorphClass(): string {
        return User::class;
    }

    public function completedReservations(): HasMany {
        return $this->reservations()->where('status', ReservationStatus::COMPLETED);
    }
}
