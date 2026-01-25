<?php

namespace App\ContentModule\Resources\CustomerReviewResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;
use App\ContentModule\Resources\CustomerReviewResource;

class CreateCustomerReview extends CreateRecord
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
