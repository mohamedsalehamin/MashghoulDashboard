<?php

namespace App\Filament\Imports;

use App\CatalogModule\Models\Seat;
use App\CatalogModule\Models\Service;
use App\Models\Services;
use App\UsersModule\Models\Provider;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Select;
use Mpdf\Shaper\Sea;

class SeatsImporter extends Importer {
    protected static ?string $model = Seat::class;

    public static function getColumns(): array {
        return [
            ImportColumn::make('db_row_id')
                ->label(__("forms.fields.db_row_id")),
            ImportColumn::make('provider_id')
                ->label(__("forms.fields.provider_id")),
            ImportColumn::make('name_ar')
                ->label(__("forms.fields.name_ar"))
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('name_en')
                ->label(__("forms.fields.name_en"))
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('services')
                ->label(__("forms.fields.services"))
                ->requiredMapping(),

        ];
    }


    public function resolveRecord(): ?Seat {

        return Seat::updateOrCreate([
            'id' => data_get($this->getData(), 'db_row_id', 0),

        ], [
            'provider_id' => data_get($this->getData(), 'provider_id', 0),
            'title' => [
                'ar' => data_get($this->getData(), 'name_ar'),
                'en' => data_get($this->getData(), 'name_en'),
            ],

            'status' => 1

        ]);


    }

    public static function getCompletedNotificationBody(Import $import): string {
        $body = 'Your services import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    public function saveRecord(): void {
        $services = Service::whereIn('meta_data->import_id', explode(",", $this->getData()['services']))->pluck("id")->toArray();

        $this->record->offsetUnset('id');
        $this->record->offsetUnset('db_row_id');
        $this->record->offsetUnset('name_ar');
        $this->record->offsetUnset('name_en');
        $this->record->offsetUnset('services');
        $record = $this->record->save();
        $this->record->services()->sync($services);
    }
}
