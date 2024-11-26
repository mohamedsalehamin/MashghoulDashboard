<?php

namespace App\DefaultPanel\Api\V1;

use App\DefaultPanel\Notifications\SendAdminMessagesNotification;
use App\DefaultPanel\Resources\Api\NotificationResource;
use Notification;
use Tasawk\Api\Facade\Api;


class NotificationServices {
    public function all() {
        auth()->user()->notifications->markAsRead();
        $notifications = NotificationResource::collection(auth()->user()->notifications()->latest()->paginate());
        return Api::isOk(__("Notification list"))->setData($notifications);
    }

    public function destroy($notification = null) {
        !$notification
            ? auth()->user()->notifications()->delete()
            : auth()->user()->notifications()->where("id", $notification)->delete();
        return Api::isOk(__("Notification has been deleted"));
    }

    public function fcm() {
        return Notification::send(auth()->user(), new SendAdminMessagesNotification(...request()->only('title', 'body')));
    }

}
