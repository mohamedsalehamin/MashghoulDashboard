<?php

namespace App\ContentModule\Resources\LevelResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use Filament\Actions\DeleteAction;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use App\ContentModule\Resources\LevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\ContentModule\Resources\PageResource;

class EditLevel extends EditRecord
{
    protected static string $resource = LevelResource::class;
    use Translatable;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            LocaleSwitcher::make(),

        ];
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}
