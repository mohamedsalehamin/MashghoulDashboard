<?php

namespace App\ProviderPanel\Filament\Resources\SeatResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use App\DefaultPanel\Settings\GeneralSettings;
use App\ProviderPanel\Filament\Resources\SeatResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSeat extends CreateRecord {
    use Translatable;

    protected static string $resource = SeatResource::class;

    /** Snapshot of serviceGroups from the submitted form, used in afterCreate */
    public ?array $serviceGroupsSnapshot = null;

    public function mount(): void
    {
        parent::mount();
        $defaults = GeneralSettings::defaultSeatDaysListFromProvider(provider());
        if ($defaults !== []) {
            $this->form->fill([
                'meta_data' => [
                    'days_list' => $defaults,
                ],
            ]);
        }
    }

    protected function getHeaderActions(): array {
        return [
            LocaleSwitcher::make(),
        ];
    }

    public function mutateFormDataBeforeCreate(array $data): array {
        // Capture from form state or $data; normalize structure
        $items = array_values($this->form->getState()['serviceGroups'] ?? $data['serviceGroups'] ?? []);
        $this->serviceGroupsSnapshot = array_map(function ($item) {
            $titleAr = $this->extractTitle($item, 'ar');
            $titleEn = $this->extractTitle($item, 'en');
            if (isset($item['title']) && is_string($item['title']) && trim($item['title']) !== '') {
                $single = trim($item['title']);
                if ($titleAr === '') $titleAr = $single;
                if ($titleEn === '') $titleEn = $single;
            }
            return [
                'title' => ['ar' => $titleAr, 'en' => $titleEn],
                'services' => $item['services'] ?? [],
            ];
        }, $items);
        return $data;
    }

    protected function afterCreate(): void {
        $stateGroups = $this->serviceGroupsSnapshot ?? [];
        $this->serviceGroupsSnapshot = null;
        $sync = [];

        foreach ($stateGroups as $i => $stateItem) {
            $titleAr = $stateItem['title']['ar'] ?? '';
            $titleEn = $stateItem['title']['en'] ?? '';

            $group = $this->record->serviceGroups()->create([
                'sort' => $i,
                'title' => ['ar' => $titleAr, 'en' => $titleEn],
            ]);

            foreach ($stateItem['services'] ?? [] as $sid) {
                $sync[$sid] = ['service_group_id' => $group->id];
            }
        }

        $this->record->services()->sync($sync);
    }

    private function extractTitle(array $item, string $locale): string {
        $key = 'title.' . $locale;
        if (array_key_exists($key, $item) && (string) $item[$key] !== '') {
            return (string) $item[$key];
        }
        if (isset($item['title']) && is_array($item['title']) && array_key_exists($locale, $item['title'])) {
            $v = $item['title'][$locale];
            return $v !== null && $v !== '' ? (string) $v : '';
        }
        if (isset($item['data']['title'][$locale])) {
            $v = $item['data']['title'][$locale];
            return $v !== null && $v !== '' ? (string) $v : '';
        }
        return '';
    }
}
