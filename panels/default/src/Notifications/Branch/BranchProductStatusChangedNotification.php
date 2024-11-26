<?php

namespace App\DefaultPanel\Notifications\Branch;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\CatalogModule\Models\Branch;
use App\DoctorPanel\Filament\Resources\Branch\Inventory;
use App\DefaultPanel\Lib\Firebase;
use App\DefaultPanel\Lib\NotificationMessageParser;
use App\UsersModule\Models\DeviceToken;

class BranchProductStatusChangedNotification extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Branch $branch, public Inventory $inventory) {
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
        Firebase::make()
            ->setTokens($tokens)
            ->setTitle($this->getTitle($notifiable)[$notifiable->preferredLocale()])
            ->setBody($this->getBody($notifiable)[$notifiable->preferredLocale()])
            ->setMoreData([
                'entity_id' => $this->branch->id,
                'entity_type' => 'branch',
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
                'entity_id' => $this->branch->id,
                'entity_type' => 'branch',
            ],
            'duration' => 'persistent'
        ];

    }

    public function getTitle($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->adminMessage('panel.notifications.branch_manager_change_product_status', ['branch_name' => $this->branch->name])
            ->parse();
    }

    public function getBody($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->adminMessage('panel.notifications.branch_manager_change_product_status_body', ["branch_name" => $this->branch->name, 'product_name'=>$this->inventory->product->title,'status' => $this->inventory->status ? __("panel.messages.activated") : __("panel.messages.deactivated")])
            ->parse();
    }
}
