<?php

namespace App\DefaultPanel\Notifications\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\DefaultPanel\Lib\Firebase;
use App\DefaultPanel\Lib\NotificationMessageParser;
use App\UsersModule\Models\DeviceToken;

class CustomerStatusChangedNotification extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct() {
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
        $title = NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.customer_status_changed')
            ->parse();
        $body = NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.customer_status_changed_body',["status"=>$notifiable->active?__('panel.enums.ACTIVE'):__('panel.enums.INACTIVE')])
            ->parse();
        $tokens = DeviceToken::where('user_id', $notifiable->id)->pluck('token')->unique()->toArray();
        return Firebase::make()
            ->setTokens($tokens)
            ->setTitle($title[$notifiable->preferredLocale()])
            ->setBody($body[$notifiable->preferredLocale()])
            ->setMoreData([
                'entity_id' => $notifiable->id,
                'entity_type' => 'profile',
            ])
            ;
    }

    public function toArray($notifiable): array {
        $this->toFirebase($notifiable);

        $title = NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.customer_status_changed')
            ->parse();
        $body = NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.customer_status_changed_body',["status"=>$notifiable->active?__('panel.enums.ACTIVE'):__('panel.enums.INACTIVE')])
            ->parse();
        return [
            'title' => json_encode($title),
            'body' => json_encode($body),
            'format' => 'filament',
            'viewData' => [
                'entity_id' =>$notifiable->id,
                'entity_type' => 'profile',
            ],
            'duration' => 'persistent'
        ];

    }
}
