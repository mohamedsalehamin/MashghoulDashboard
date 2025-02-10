<?php

namespace App\Notifications;

use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Lib\Firebase;
use App\DefaultPanel\Lib\NotificationMessageParser;
use App\UsersModule\Models\DeviceToken;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservationStatusChangedNotification extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Reservation $reservation) {
        //
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
                'entity_type' => 'reservation',
                'entity_id' => $this->reservation->id,
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
                'entity_type' => 'reservation',
                'entity_id' => $this->reservation->id,
            ],
            'duration' => 'persistent'
        ];

    }

    public function getTitle($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.reservationstatuschangednotification', ['id' => $this->reservation->id])
            ->doctorMessage('panel.notifications.reservationstatuschangednotification', ['id' => $this->reservation->id])
            ->parse();
    }

    public function getBody($notifiable) {

        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.reservationstatuschangednotification', ['id' => $this->reservation->id,])
            ->doctorMessage('panel.notifications.reservationstatuschangednotification', ['id' => $this->reservation->id,])
            ->parse();
    }
}
