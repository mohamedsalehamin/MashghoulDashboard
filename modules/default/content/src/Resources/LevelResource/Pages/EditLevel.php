<?php

namespace App\ContentModule\Resources\LevelResource\Pages;

use App\ContentModule\Resources\LevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\ContentModule\Resources\PageResource;

class EditLevel extends EditRecord
{
    protected static string $resource = LevelResource::class;
    use EditRecord\Concerns\Translatable;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\LocaleSwitcher::make(),

        ];
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}
