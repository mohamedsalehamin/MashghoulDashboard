<?php

namespace App\Notifications;

use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Lib\Firebase;
use App\DefaultPanel\Lib\NotificationMessageParser;
use App\UsersModule\Models\DeviceToken;
use App\UsersModule\Models\WithdrawalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WithdrawalRequestStatusChangedNotification extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public WithdrawalRequest $withdrawalRequest) {
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
                'entity_type' => 'withdrawalrequest',
                'entity_id' => $this->withdrawalRequest->id,
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
                'entity_type' => 'withdrawalrequest',
                'entity_id' => $this->withdrawalRequest->id,
            ],
            'duration' => 'persistent'
        ];

    }

    public function getTitle($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.withdrawalrequeststatuschangednotification', ['id' => $this->withdrawalRequest->id])
            ->adminMessage('panel.notifications.withdrawalrequeststatuschangednotification', ['id' => $this->withdrawalRequest->id])
            ->parse();
    }

    public function getBody($notifiable) {

        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.withdrawalrequeststatuschangednotificationbody', ['id' => $this->withdrawalRequest->id,])
            ->adminMessage('panel.notifications.withdrawalrequeststatuschangednotificationbody', ['id' => $this->withdrawalRequest->id,])
            ->parse();
    }
}
