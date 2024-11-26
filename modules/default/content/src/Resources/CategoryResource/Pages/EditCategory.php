<?php

namespace App\ContentModule\Resources\CategoryResource\Pages;

use App\ContentModule\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord {
    use EditRecord\Concerns\Translatable;

    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\DeleteAction::make(),
            Actions\LocaleSwitcher::make(),

        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }

}
