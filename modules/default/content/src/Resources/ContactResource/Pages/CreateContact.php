<?php

namespace App\ContentModule\Resources\ContactResource\Pages;

use App\ContentModule\Resources\ContactResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;
}
