<?php

namespace App\ContentModule\Resources\JoinRequestResource\Pages;

use App\ContentModule\Resources\JoinRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJoinRequest extends EditRecord
{
    protected static string $resource = JoinRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
