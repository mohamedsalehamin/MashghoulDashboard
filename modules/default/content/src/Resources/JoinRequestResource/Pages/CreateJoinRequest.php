<?php

namespace App\ContentModule\Resources\JoinRequestResource\Pages;

use App\ContentModule\Resources\JoinRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateJoinRequest extends CreateRecord
{
    protected static string $resource = JoinRequestResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}
