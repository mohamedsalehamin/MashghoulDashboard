<?php

namespace App\ContentModule\Resources\NotificationResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\ContentModule\Resources\NotificationResource;

class ListNotifications extends ListRecords {


    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array {
        return [

        ];
    }
}
