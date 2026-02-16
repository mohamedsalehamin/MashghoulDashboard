<?php

namespace App\ProviderPanel\Filament\Resources\ServiceResource\Pages;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use App\ProviderPanel\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord {
    use Translatable;

    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function afterCreate(): void {
        $data = $this->form->getState();
        $assignments = $data['seat_assignments'] ?? [];
        $sync = [];
        foreach ($assignments as $row) {
            if (!empty($row['seat_id'] ?? null)) {
                $sync[$row['seat_id']] = ['service_group_id' => $row['service_group_id'] ?? null];
            }
        }
        $this->record->seats()->sync($sync);
    }
}
