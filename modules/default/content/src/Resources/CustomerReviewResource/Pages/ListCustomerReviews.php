<?php

namespace App\ContentModule\Resources\CustomerReviewResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\ContentModule\Resources\CustomerReviewResource;

class ListCustomerReviews extends ListRecords
{
    protected static string $resource = CustomerReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
