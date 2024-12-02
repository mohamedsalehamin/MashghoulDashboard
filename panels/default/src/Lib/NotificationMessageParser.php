<?php

namespace App\DefaultPanel\Lib;

use App\CrmModule\Models\Customer;
use App\Models\User;
use App\UsersModule\Models\User\Patient;

class NotificationMessageParser {
    public $adminMessage = null;
    public $doctorMessage = null;
    public $customerMessage = null;
    private User $notifiable;

    public function __construct(User $notifiable) {
        $this->notifiable = $notifiable;
    }

    public static function init(User $notifiable) {
        return new static($notifiable);
    }

    public function adminMessage($text, $params = []): static {
        $this->adminMessage = Utils::convertStringToArrayLanguage($text, $params);
        return $this;
    }

    public function doctorMessage($text, $params = []): static {
        $this->doctorMessage = Utils::convertStringToArrayLanguage($text, $params);
        return $this;
    }

    public function customerMessage($text, $params = []): static {
        $this->customerMessage = Utils::convertStringToArrayLanguage($text, $params);
        return $this;
    }

    public function parse() {

        return match ($this->notifiable->roles()?->where('name','!=','panel_user')?->first()?->name) {
            'customer' => $this->customerMessage,
            default => $this->adminMessage,
        };

    }

}
