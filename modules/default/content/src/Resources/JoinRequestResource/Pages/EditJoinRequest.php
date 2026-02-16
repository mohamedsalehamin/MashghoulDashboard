<?php

namespace App\ContentModule\Resources\JoinRequestResource\Pages;

use Filament\Actions\DeleteAction;
use App\ContentModule\Resources\JoinRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditJoinRequest extends EditRecord
{
    protected static string $resource = JoinRequestResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
