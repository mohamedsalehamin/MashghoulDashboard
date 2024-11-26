<?php

namespace App\CatalogModule\Resources\ReportReasonResource\Pages;

use App\CatalogModule\Resources\ReportReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReportReason extends ViewRecord
{
    protected static string $resource = ReportReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
