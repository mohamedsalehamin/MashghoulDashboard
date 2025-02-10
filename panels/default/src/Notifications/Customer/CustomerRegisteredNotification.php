<?php

namespace App\DefaultPanel\Notifications\Customer;

use App\UsersModule\Models\Users\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\DefaultPanel\Lib\Firebase;
use App\DefaultPanel\Lib\NotificationMessageParser;
use App\UsersModule\Models\DeviceToken;

class CustomerRegisteredNotification extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public  Customer $customer) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array {
        return ['database'];
    }


    public function toFirebase($notifiable) {

        $tokens = DeviceToken::where('user_id', $notifiable->id)->pluck('token')->unique()->toArray();
        return Firebase::make()
            ->setTokens($tokens)
            ->setTitle($this->getTitle($notifiable)[$notifiable->preferredLocale()])
            ->setBody($this->getBody($notifiable)[$notifiable->preferredLocale()])
            ->setMoreData([
                'entity_id' => $notifiable->id,
                'entity_type' => 'customer',
            ])
            ;
    }

    public function toArray($notifiable): array {
        $this->toFirebase($notifiable);


        return [
            'title' => json_encode($this->getTitle($notifiable)),
            'body' => json_encode($this->getBody($notifiable)),
            'format' => 'filament',
            'viewData' => [
                'entity_id' => $this->customer->id,
                'entity_type' => 'customer',
            ],
            'duration' => 'persistent'
        ];

    }

    public function getTitle($notifiable) {
        return  NotificationMessageParser::init($notifiable)
            ->adminMessage('panel.notifications.customer_registered')
            ->parse();
    }

    public function getBody($notifiable) {

        return  NotificationMessageParser::init($notifiable)
            ->adminMessage('panel.notifications.customer_registered_body',['name'=>$this->customer->name])
            ->parse();
    }
}
