<?php

namespace App\ContentModule\Resources\JoinRequestResource\Pages;

use App\ContentModule\Resources\JoinRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJoinRequest extends CreateRecord
{
    protected static string $resource = JoinRequestResource::class;
}
