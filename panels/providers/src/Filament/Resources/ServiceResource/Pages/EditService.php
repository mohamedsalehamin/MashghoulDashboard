<?php

namespace App\ProviderPanel\Filament\Resources\ServiceResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use App\ProviderPanel\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord {
    use Translatable;

    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array {
        return [
            LocaleSwitcher::make(),
        ];
    }

    public function mutateFormDataBeforeFill(array $data): array {
        $data['seat_assignments'] = $this->record->seats()
            ->get()
            ->map(fn($seat) => [
                'seat_id' => $seat->id,
                'service_group_id' => $seat->pivot->service_group_id,
            ])
            ->values()
            ->toArray();
        return $data;
    }

    protected function afterSave(): void {
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
