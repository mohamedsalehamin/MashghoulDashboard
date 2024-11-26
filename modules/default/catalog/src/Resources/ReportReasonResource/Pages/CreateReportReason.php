<?php

namespace App\CatalogModule\Resources\ReportReasonResource\Pages;

use App\CatalogModule\Resources\ReportReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReportReason extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = ReportReasonResource::class;
    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}
