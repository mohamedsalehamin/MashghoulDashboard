<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Request;
use App\DefaultPanel\Enum\GenderEnum;
use App\DefaultPanel\Enum\WalletWithdrawEnum;
use App\Models\PointsExchange;
use Illuminate\Http\Resources\Json\JsonResource;
 
class CustomerResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $pendingWithdrawalAmount = $this->withdrawalRequests() 
        ->whereIn('status', [WalletWithdrawEnum::PENDING, WalletWithdrawEnum::WAITING_TRANSFER])
        ->sum('amount');

        $availableBalance = $this->wallet?->balance - $pendingWithdrawalAmount;
        return [
            'id' => $this->id,
            'avatar' => $this->getFirstMediaUrl(),
            'name' => $this->name,
            "first_name" => $this->data['first_name'] ?? '',
            "last_name" => $this->data['last_name'] ?? '',
            'email' => $this->email,
            'phone' => $this->phone,
            'dob' => $this->dob,
            'api_token' => $this->api_token,
            'country' => CountryResource::make($this?->city?->state?->country),
            'state' => StateResource::make($this->city?->state),
            'city' => CityResource::make($this->city),
            'gender' => GenderEnum::from($this->gender)->getLabel(),
            'gender_enum' => GenderEnum::from($this->gender)->value,
            'wallet_balance'=>(float)$availableBalance ?? 0,
            // Same as web checkout (getTotalPoints); not PointsExchange::getTotalPointsBalance.
            'points_balance' => (int) $this->getTotalPoints(),
            'points_exchange_balance' => (float) $this->getTotalPointsBalance(),
            'points' => (int) $this->getTotalPoints(),
            'notification_status' => $this->settings['notification_status'] ?? 1,
            'unread_notifications_count' => $this->unreadNotifications()->count(),
            'preferred_language' => $this->settings['preferred_language'] ?? 'ar',
            "phone_verified" => (int)!is_null($this->phone_verified_at),
        ];
    }
}
