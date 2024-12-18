<?php

namespace App\DefaultPanel\Api\V1\Customer;

use App\DefaultPanel\Notifications\SendAdminMessagesNotification;
use App\DefaultPanel\Resources\Api\Customer\NotificationResource;
use Notification;
use Tasawk\Api\Facade\Api;


class NotificationServices {
    public function all() {
        $notifications = NotificationResource::collection(auth()->user()->notifications()->latest()->paginate());
        return Api::isOk(__("Notification list"))->setData($notifications);
    }

    public function destroy($notification = null) {
        !$notification
            ? auth()->user()->notifications()->delete()
            : auth()->user()->notifications()->where("id", $notification)->delete();
        return Api::isOk(__("Notification has been deleted"));
    }

    public function seen(\App\Models\Notification $notification) {

        $notification->markAsRead();
        return Api::isOk("done");
    }

    public function fcm() {
        return Notification::send(auth()->user(), new SendAdminMessagesNotification(...request()->only('title', 'body')));
    }

}
