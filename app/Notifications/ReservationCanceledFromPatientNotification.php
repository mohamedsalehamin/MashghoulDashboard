<?php

namespace App\Notifications;

use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Lib\Firebase;
use App\DefaultPanel\Lib\NotificationMessageParser;
use App\UsersModule\Models\DeviceToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCanceledFromPatientNotification extends Notification {
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
            ->do();
    }

    public function toArray($notifiable): array {
        $this->toFirebase($notifiable);


        return [
            'title' => json_encode($this->getTitle($notifiable)),
            'body' => json_encode($this->getBody($notifiable)),
            'format' => 'filament',
            'viewData' => [
                "entity_id" => $this->reservation->id,
                "entity_type" => "reservation"
            ],
            'duration' => 'persistent'
        ];

    }

    public function getTitle($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.reservationcanceledfrompatientnotification', ['id' => $this->reservation->id])
            ->doctorMessage('panel.notifications.doctorreservationcanceledfrompatientnotification', ['id' => $this->reservation->id])
            ->parse();
    }

    public function getBody($notifiable) {

        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.reservationcanceledfrompatientnotificationbody', ['id' => $this->reservation->id])
            ->doctorMessage('panel.notifications.doctorreservationcanceledfrompatientnotificationbody', ['id' => $this->reservation->id])
            ->parse();
    }
}
