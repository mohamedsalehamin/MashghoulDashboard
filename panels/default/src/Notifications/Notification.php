<?php

namespace App\DefaultPanel\Notifications;

use Closure;
use Filament\Notifications\Notification as BaseNotification;

class Notification extends BaseNotification {

    protected string $view = 'filament.pages.notifications.notification';


    public function toArray(): array {
        return [

            ...parent::toArray(),
            'body' => $this->getBody(),
        ];
    }




    protected string|Closure|null $body = null;

    public function body(string|Closure|null $body): static {
        $this->body = $body;

        return $this;
    }

    public function getBody(): ?string {
        if (json_decode($this->body, true)) {

            return json_decode($this->body, true)[app()->getLocale()];
        }

        return $this->evaluate($this->body);
    }

    public function getTitle(): ?string {
        if (json_decode($this->title, true)) {

            return json_decode($this->title, true)[app()->getLocale()];
        }
        return $this->evaluate($this->title);
    }


}
