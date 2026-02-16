<?php

namespace App\Filament\Imports;

use App\CatalogModule\Models\Seat;
use App\CatalogModule\Models\Service;
use App\UsersModule\Models\Provider;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

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
            ImportColumn::make('service_groups')
                ->label(__("forms.fields.service_groups"))
                ->helperText(__("forms.import_seats.service_groups")),
            ImportColumn::make('services')
                ->label(__("forms.fields.services"))
                ->requiredMapping()
                ->helperText(__("forms.import_seats.services")),
            ImportColumn::make('service_group_names')
                ->label(__("forms.fields.service_group_names"))
                ->helperText(__("forms.import_seats.service_group_names")),
        ];
    }


    public function resolveRecord(): ?Seat {

        return Seat::firstOrNew([
            'id' => data_get($this->getData(), 'db_row_id', 0),
        ], [
            'id' => data_get($this->getData(), 'db_row_id', 0),
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
        $data = $this->getData();
        $this->record->offsetUnset('db_row_id');
        $this->record->offsetUnset('name_ar');
        $this->record->offsetUnset('name_en');
        $this->record->offsetUnset('service_groups');
        $this->record->offsetUnset('services');
        $this->record->offsetUnset('service_group_names');
        $this->record->save();

        $serviceIds = $this->resolveServiceIds($data['services'] ?? '');

        $groupNamesStr = trim($data['service_groups'] ?? '');
        if ($groupNamesStr !== '') {
            $names = array_filter(array_map('trim', explode('|', $groupNamesStr)));
            $sort = 0;
            foreach ($names as $name) {
                $this->record->serviceGroups()->firstOrCreate(
                    ['seat_id' => $this->record->id, 'title->ar' => $name],
                    ['title' => ['ar' => $name, 'en' => $name], 'sort' => $sort++]
                );
            }
        } else {
            // Seat without groups: remove all groups so all services stay ungrouped
            $this->record->serviceGroups()->delete();
        }

        $sync = [];
        $groupNamesPerService = isset($data['service_group_names']) && $data['service_group_names'] !== ''
            ? array_map('trim', explode(',', $data['service_group_names']))
            : [];
        $seatGroups = $this->record->serviceGroups()->orderBy('sort')->orderBy('id')->get();

        foreach ($serviceIds as $index => $serviceId) {
            $groupId = null;
            if (isset($groupNamesPerService[$index]) && $groupNamesPerService[$index] !== '') {
                $name = $groupNamesPerService[$index];
                $group = $seatGroups->first(function ($g) use ($name) {
                    $t = $g->getTranslations('title');
                    return ($t['ar'] ?? '') === $name || ($t['en'] ?? '') === $name;
                });
                $groupId = $group?->id;
            }
            $sync[$serviceId] = ['service_group_id' => $groupId];
        }

        $this->record->services()->sync($sync);
    }

    /**
     * Resolve comma-separated service identifiers to internal service IDs.
     * Each value is matched by meta_data->import_id first, then by numeric id.
     */
    private function resolveServiceIds(string $servicesStr): array {
        $ids = [];
        $tokens = array_filter(array_map('trim', explode(',', $servicesStr)));
        foreach ($tokens as $token) {
            $byImportId = Service::where('meta_data->import_id', $token)->value('id');
            if ($byImportId) {
                $ids[] = $byImportId;
                continue;
            }
            if (is_numeric($token)) {
                $byId = Service::find((int) $token);
                if ($byId) {
                    $ids[] = $byId->id;
                }
            }
        }
        return $ids;
    }
}
