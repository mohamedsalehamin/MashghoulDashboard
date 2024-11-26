<?php

namespace App\CatalogModule\Resources\ReportReasonResource\Pages;

use App\CatalogModule\Resources\ReportReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReportReasons extends ListRecords
{
    protected static string $resource = ReportReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
