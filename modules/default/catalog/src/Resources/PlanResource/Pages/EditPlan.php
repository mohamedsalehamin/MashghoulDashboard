<?php

namespace App\CatalogModule\Resources\PlanResource\Pages;

use App\CatalogModule\Models\PlanPrice;
use App\CatalogModule\Resources\PlanResource;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditPlan extends EditRecord
{
    //use Translatable;

    protected static string $resource = PlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // LocaleSwitcher::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $prices = $this->record->planPrices()->orderBy('period')->get();
        $data['plan_prices_data'] = $prices->map(fn ($p) => [
            'period' => $p->period,
            'price' => $p->price->formatByDecimal(),
            'days_count' => $p->days_count,
        ])->toArray();
        if (count($data['plan_prices_data']) < 3) {
            $periods = [PlanPrice::PERIOD_MONTHLY, PlanPrice::PERIOD_QUARTERLY, PlanPrice::PERIOD_YEARLY];
            $days = [30, 90, 365];
            foreach ($periods as $i => $period) {
                if (!collect($data['plan_prices_data'])->contains('period', $period)) {
                    $data['plan_prices_data'][] = ['period' => $period, 'price' => 0, 'days_count' => $days[$i]];
                }
            }
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['plan_prices_data']);
        return $data;
    }

    protected function afterSave(): void
    {
        $pricesData = $this->form->getState()['plan_prices_data']
            ?? data_get($this->form->getRawState(), 'plan_prices_data')
            ?? data_get($this->form->getRawState(), 'data.plan_prices_data')
            ?? [];

        $existing = $this->record->planPrices()->orderBy('period')->get();

        foreach ($pricesData as $item) {
            $period = $item['period'] ?? PlanPrice::PERIOD_MONTHLY;
            $price = (float) ($item['price'] ?? 0);
            $daysCount = (int) ($item['days_count'] ?? 30);

            $existingPrice = $existing->firstWhere('period', $period);
            if ($existingPrice) {
                $existingPrice->update(['price' => $price, 'days_count' => $daysCount]);
            } else {
                $this->record->planPrices()->create([
                    'period' => $period,
                    'price' => $price,
                    'days_count' => $daysCount,
                ]);
            }
        }
    }
}
