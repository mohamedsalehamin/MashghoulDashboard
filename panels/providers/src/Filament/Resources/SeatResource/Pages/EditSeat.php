<?php

namespace App\ProviderPanel\Filament\Resources\SeatResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use App\DefaultPanel\Settings\GeneralSettings;
use App\ProviderPanel\Filament\Resources\SeatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeat extends EditRecord {
    use Translatable;

    protected static string $resource = SeatResource::class;

    /** Snapshot of serviceGroups (with title + services) from the submitted form, used in afterSave */
    public ?array $serviceGroupsSnapshot = null;

    protected function getHeaderActions(): array {
        return [
            LocaleSwitcher::make(),
        ];
    }

    public function mutateFormDataBeforeFill(array $data): array {
        $data['meta_data'] = $data['meta_data'] ?? [];
        $data['meta_data']['days_list'] = GeneralSettings::filterSeatDaysListForProvider(
            $data['meta_data']['days_list'] ?? [],
            provider()
        );

        $data['serviceGroups'] = $this->record->serviceGroups()
            ->orderBy('sort')
            ->orderBy('id')
            ->get()
            ->map(function ($group) {
                $translations = $group->getTranslations('title');
                return [
                    'title' => [
                        'ar' => $translations['ar'] ?? '',
                        'en' => $translations['en'] ?? '',
                    ],
                    'services' => $this->record->services()
                        ->wherePivot('service_group_id', $group->id)
                        ->pluck('id')
                        ->toArray(),
                ];
            })
            ->toArray();
        return $data;
    }

    public function mutateFormDataBeforeSave(array $data): array {
        // Capture from form state or $data (one may have serviceGroups stripped); normalize structure
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

    protected function afterSave(): void {
        $stateGroups = $this->serviceGroupsSnapshot ?? [];
        $this->serviceGroupsSnapshot = null;

        $existing = $this->record->serviceGroups()->orderBy('sort')->orderBy('id')->get();
        $sync = [];

        foreach ($stateGroups as $i => $stateItem) {
            $titleAr = $stateItem['title']['ar'] ?? '';
            $titleEn = $stateItem['title']['en'] ?? '';

            if ($existing->has($i)) {
                $group = $existing[$i];
                $group->setTranslations('title', ['ar' => $titleAr, 'en' => $titleEn]);
                $group->sort = $i;
                $group->save();
            } else {
                $group = $this->record->serviceGroups()->create([
                    'sort' => $i,
                    'title' => ['ar' => $titleAr, 'en' => $titleEn],
                ]);
            }

            foreach ($stateItem['services'] ?? [] as $sid) {
                $sync[$sid] = ['service_group_id' => $group->id];
            }
        }

        foreach ($existing->slice(count($stateGroups)) as $group) {
            $group->delete();
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
        // Livewire/Filament sometimes nests under 'data'
        if (isset($item['data']['title'][$locale])) {
            $v = $item['data']['title'][$locale];
            return $v !== null && $v !== '' ? (string) $v : '';
        }
        return '';
    }
}
