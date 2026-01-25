<?php

namespace App\ContentModule\Resources\CustomerReviewResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Filament\Resources\Pages\EditRecord;
use App\ContentModule\Resources\CustomerReviewResource;

class EditCustomerReview extends EditRecord
{
    protected static string $resource = CustomerReviewResource::class;

    use Translatable;

    protected function getHeaderActions(): array {
        return [
            LocaleSwitcher::make(),
        ];
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}
