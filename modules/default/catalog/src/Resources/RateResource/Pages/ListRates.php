<?php

namespace App\CatalogModule\Resources\RateResource\Pages;

use App\CatalogModule\Resources\RateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;

class ListRates extends ListRecords
{
    protected static string $resource = RateResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('panel.add_manual_rating')),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('panel.enums.all'))
                ->icon('heroicon-o-star'),

            'reservation' => Tab::make(__('panel.reservation_ratings'))
                ->icon('heroicon-o-clipboard-document-list')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('source', 'reservation')),

            'manual' => Tab::make(__('panel.manual_ratings'))
                ->icon('heroicon-o-pencil-square')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('source', 'manual')),

            'pending' => Tab::make(__('panel.pending_approval'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_approved', false))
                ->badge(fn() => \App\CatalogModule\Models\Reservation\Rate::where('is_approved', false)->count())
                ->badgeColor('warning'),

            
        ];
    }
}

