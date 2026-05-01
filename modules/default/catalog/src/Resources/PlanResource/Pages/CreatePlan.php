<?php

namespace App\CatalogModule\Resources\PlanResource\Pages;

use App\CatalogModule\Models\PlanPrice;
use App\CatalogModule\Resources\PlanResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreatePlan extends CreateRecord
{
    // use Translatable;

    protected static string $resource = PlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // LocaleSwitcher::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['plan_prices_data']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $pricesData = $this->form->getState()['plan_prices_data']
            ?? data_get($this->form->getRawState(), 'plan_prices_data')
            ?? data_get($this->form->getRawState(), 'data.plan_prices_data')
            ?? [];

        foreach ($pricesData as $item) {
            $this->record->planPrices()->create([
                'period' => $item['period'] ?? PlanPrice::PERIOD_MONTHLY,
                'price' => (float) ($item['price'] ?? 0),
                'days_count' => (int) ($item['days_count'] ?? 30),
            ]);
        }
    }
}
