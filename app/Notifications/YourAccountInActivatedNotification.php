<?php

namespace App\Notifications;

use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Lib\Firebase;
use App\DefaultPanel\Lib\NotificationMessageParser;
use App\UsersModule\Models\DeviceToken;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class YourAccountInActivatedNotification extends Notification
{
    use Queueable;



    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toFirebase($notifiable) {
        $tokens = DeviceToken::where('user_id', $notifiable->id)->pluck('token')->unique()->toArray();
        return Firebase::make()
            ->setTokens($tokens)
            ->setTitle($this->getTitle($notifiable)[$notifiable->preferredLocale()])
            ->setBody($this->getBody($notifiable)[$notifiable->preferredLocale()])
            ->setMoreData([
                'entity_type' => 'user',
                'entity_id' => $notifiable->id,
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
                'entity_type' => 'user',
                'entity_id' => $notifiable->id
            ],
            'duration' => 'persistent'
        ];

    }

    public function getTitle($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.youraccountinactivatednotification')
            ->doctorMessage('panel.notifications.youraccountinactivatednotification')
            ->parse();
    }

    public function getBody($notifiable) {

        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.youraccountinactivatednotificationbody')
            ->doctorMessage('panel.notifications.youraccountinactivatednotificationbody')
            ->parse();
    }
}
