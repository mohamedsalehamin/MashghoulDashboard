<?php

namespace App\Filament\Imports;

use App\CatalogModule\Models\Product;
use App\CatalogModule\Models\Service;
use App\Models\Services;
use App\UsersModule\Models\Provider;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Select;

class ProductsImporter extends Importer {
    protected static ?string $model = Product::class;

    public static function getColumns(): array {
        return [
            ImportColumn::make('db_row_id')
                ->label(__("forms.fields.db_row_id")),
            ImportColumn::make('id')
                ->label(__("forms.fields.id"))
            ,
            ImportColumn::make('import_service_id')
                ->label(__("forms.fields.service_id"))
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

            ImportColumn::make('price')
                ->label(__("forms.fields.price"))
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('sale_price')
                ->label(__("forms.fields.sale_price")),
            ImportColumn::make('image')
                ->label(__("forms.fields.image"))
            ,
        ];
    }


    public function resolveRecord(): ?Product {
        return Product::firstOrNew([
            'id' => data_get($this->getData(), 'db_row_id', 0),
        ], [
            'id' => data_get($this->getData(), 'db_row_id', 0),
            'service_id' => Service::where('meta_data->import_id', data_get($this->getData(), 'import_service_id'))->get()->first()?->id,
            'title' => [
                'ar' => data_get($this->getData(), 'name_ar'),
                'en' => data_get($this->getData(), 'name_en'),
            ],
            'price' => data_get($this->getData(), 'price', 0),
            'sale_price' => data_get($this->getData(), 'sale_price', 0),

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
        $this->record->offsetUnset('import_service_id');
        $this->record->offsetUnset('image');
        $this->record->offsetUnset('name_ar');
        $this->record->offsetUnset('name_en');
        $this->record->save();

        if (isset($this->data['image'])) {

            try {
                $this->record?->addMediaFromUrl($this->data['image'])->toMediaCollection();
            } catch (\Exception $exception) {
            }
        }

    }
}
