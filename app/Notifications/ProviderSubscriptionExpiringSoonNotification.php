<?php

namespace App\Notifications;

use App\DefaultPanel\Lib\NotificationMessageParser;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProviderSubscriptionExpiringSoonNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => json_encode($this->getTitle($notifiable)),
            'body' => json_encode($this->getBody($notifiable)),
            'format' => 'filament',
            'viewData' => [
                'entity_type' => 'subscription',
                'entity_id' => null,
            ],
            'duration' => 'persistent',
        ];
    }

    public function getTitle($notifiable): array
    {
        return NotificationMessageParser::init($notifiable)
            ->doctorMessage('panel.notifications.provider_subscription_expiring_soon')
            ->parse();
    }

    public function getBody($notifiable): array
    {
        return NotificationMessageParser::init($notifiable)
            ->doctorMessage('panel.notifications.provider_subscription_expiring_soon_body')
            ->parse();
    }
}
