<?php

namespace App\Filament\Imports;

use App\CatalogModule\Models\Service;
use App\Models\Services;
use App\UsersModule\Models\Provider;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Select;

class ServicesImporter extends Importer {
    protected static ?string $model = Service::class;

    public static function getColumns(): array {
        return [
            ImportColumn::make('db_row_id')
                ->label(__("forms.fields.db_row_id")),
            ImportColumn::make('id')
                ->label(__("forms.fields.id"))
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('provider_id')
                ->label(__("forms.fields.provider_id"))
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('name_ar')
                ->label(__("forms.fields.name_ar"))
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('name_en')
                ->label(__("forms.fields.name_en"))
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('description_ar')
                ->label(__("forms.fields.description_ar"))
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('description_en')
                ->label(__("forms.fields.description_en"))
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('duration')
                ->label(__("forms.fields.duration"))
                ->requiredMapping(),
            ImportColumn::make('price')
                ->label(__("forms.fields.price"))
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('image')
                ->label(__("forms.fields.image"))
                ->requiredMapping()
                ->rules(['required']),
        ];
    }


    public function resolveRecord(): ?Service {

        return Service::firstOrNew(['id' => data_get($this->getData(), 'db_row_id', 0)], [
            'id' => data_get($this->getData(), 'db_row_id', 0),
            'provider_id' => data_get($this->getData(), 'provider_id', 0),
            'title' => [
                'ar' => data_get($this->getData(), 'name_ar'),
                'en' => data_get($this->getData(), 'name_en'),
            ],
            'description' => [
                'ar' => data_get($this->getData(), 'description_ar'),
                'en' => data_get($this->getData(), 'description_en'),
            ],
            'duration' => data_get($this->getData(), 'duration'),
            'price' => data_get($this->getData(), 'price', 0),
            'meta_data' => [
                'import_id' => data_get($this->getData(), 'id')
            ],
            'status' => 1,
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
        $this->record->offsetUnset('id');
        $this->record->offsetUnset('db_row_id');
        $this->record->offsetUnset('name_ar');
        $this->record->offsetUnset('description_ar');
        $this->record->offsetUnset('description_en');
        $this->record->offsetUnset('image');
        $this->record->offsetUnset('name_en');
        $this->record->save();

        if (isset($this->data['image'])) {
            try {
                $this->record->addMediaFromUrl($this->data['image'])->toMediaCollection();

            } catch (\Exception $exception) {

            }
        }
    }
}
