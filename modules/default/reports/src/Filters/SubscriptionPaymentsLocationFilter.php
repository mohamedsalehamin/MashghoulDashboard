<?php

namespace App\ReportsModule\Filters;

use App\ContentModule\Models\City;
use App\ContentModule\Models\Country;
use App\ContentModule\Models\State;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionPaymentsLocationFilter
{
    public static function make()
    {
        return Filter::make('locations')
            ->schema([
                Select::make('country_id')
                    ->searchable()
                    ->afterStateUpdated(function ($set) {
                        $set('state_id', null);
                        $set('city_id', null);
                    })
                    ->live()
                    ->options(fn () => Country::pluck('name', 'id')),
                Select::make('state_id')
                    ->afterStateUpdated(fn ($set) => $set('city_id', null))
                    ->searchable()
                    ->live()
                    ->options(fn ($get) => State::where('country_id', $get('country_id'))->pluck('name', 'id')),
                Select::make('city_id')
                    ->live()
                    ->searchable()
                    ->options(fn ($get) => City::where('state_id', $get('state_id'))->pluck('name', 'id')),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when($data['country_id'] ?? '', fn (Builder $q, $country_id): Builder => $q->whereHas('transactionable.subscriber.provider.city.state', fn (Builder $q) => $q->where('country_id', $country_id)))
                    ->when($data['state_id'] ?? '', fn (Builder $q, $state_id): Builder => $q->whereHas('transactionable.subscriber.provider.city', fn (Builder $q) => $q->where('state_id', $state_id)))
                    ->when($data['city_id'] ?? '', fn (Builder $q, $city_id): Builder => $q->whereHas('transactionable.subscriber.provider', fn (Builder $q) => $q->where('city_id', $city_id)));
            });
    }
}
