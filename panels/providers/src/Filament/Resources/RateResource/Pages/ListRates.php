<?php

namespace App\ProviderPanel\Filament\Resources\RateResource\Pages;

use App\ProviderPanel\Filament\Resources\RateResource;
use App\ProviderPanel\Filament\Resources\RateResource\Widgets\RateSummary;
use Filament\Resources\Pages\ListRecords;

class ListRates extends ListRecords {
    protected static string $resource = RateResource::class;
//    protected function getFooterWidgets(): array {
//        return array(
//            RateSummary::class
//        );
//    }

}
