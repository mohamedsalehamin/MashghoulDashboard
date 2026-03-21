<?php

namespace App\CatalogModule\Resources\SubscriptionResource\Pages;

use App\CatalogModule\Resources\SubscriptionResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListSubscriptionActivities extends ListActivities
{
    protected static string $resource = SubscriptionResource::class;
}
