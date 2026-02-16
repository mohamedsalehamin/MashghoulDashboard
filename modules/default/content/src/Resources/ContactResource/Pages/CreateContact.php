<?php

namespace App\ContentModule\Resources\ContactResource\Pages;

use App\ContentModule\Resources\ContactResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}
