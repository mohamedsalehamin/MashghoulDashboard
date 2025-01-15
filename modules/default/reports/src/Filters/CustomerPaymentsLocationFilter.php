<?php

namespace App\ReportsModule\Filters;

use App\ContentModule\Models\City;
use App\ContentModule\Models\Country;
use App\ContentModule\Models\State;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class CustomerPaymentsLocationFilter {
    public static function make() {
        return Filter::make('locations')
            ->form([
                Select::make('country_id')
                    ->searchable()
                    ->afterStateUpdated(function ($set) {
                        $set('state_id', null);
                        $set('city_id', null);
                    })
                    ->live()
                    ->options(fn(HasTable $livewire) => Country::pluck('name', 'id')),
                Select::make('state_id')
                    ->afterStateUpdated(function ($set) {
                        $set('city_id', null);
                    })
                    ->searchable()
                    ->live()
                    ->options(fn($get) => State::where('country_id', $get('country_id'))->pluck('name', 'id')),
                Select::make('city_id')
                    ->live()
                    ->searchable()
                    ->options(fn($get) => City::where('state_id', $get('state_id'))->pluck('name', 'id')),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when($data['country_id'] ?? '', fn(Builder $query, $country_id): Builder => $query->whereHas('transactionable.reservable.city.state', fn(Builder $query) => $query->where('country_id', $country_id)))
                    ->when($data['state_id'] ?? '', fn(Builder $query, $state_id): Builder => $query->whereHas('transactionable.reservable.city', fn(Builder $query) => $query->where('state_id', $state_id)))
                    ->when($data['city_id'] ?? '', fn(Builder $query, $city_id): Builder =>$query->whereHas('transactionable.reservable.city', fn(Builder $query) => $query->where('id', $city_id)));
            });
    }
}
