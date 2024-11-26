<?php

namespace App\DefaultPanel\Notifications\Branch;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\DoctorPanel\Filament\Resources\Branch\Inventory;
use App\DoctorPanel\Filament\Resources\Branch\InventoryOption;
use App\DefaultPanel\Lib\Firebase;
use App\DefaultPanel\Lib\NotificationMessageParser;
use App\UsersModule\Models\DeviceToken;

class BranchProductOptionStatusChangedNotification extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct( public Inventory $inventory,public InventoryOption $option) {
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
                'entity_id' => $this->inventory->branch->id,
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
                'entity_id' => $this->inventory->branch->id,
                'entity_type' => 'branch',
            ],
            'duration' => 'persistent'
        ];

    }

    public function getTitle($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->adminMessage('panel.notifications.branch_manager_change_product_option_status', ['branch_name' => $this->inventory->branch->name, 'product_name' => $this->inventory->product->title, 'option_name' => $this->option->option->title])
            ->parse();
    }

    public function getBody($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->adminMessage('panel.notifications.branch_manager_change_product_option_status_body', ["branch_name" => $this->inventory->branch->name, 'product_name' => $this->inventory->product->title,'option_name' => $this->option->option->name, 'status' => $this->option->status ? __("panel.messages.activated") : __("panel.messages.deactivated")])
            ->parse();
    }
}
