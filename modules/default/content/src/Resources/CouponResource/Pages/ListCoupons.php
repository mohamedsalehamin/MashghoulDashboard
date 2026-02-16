<?php

namespace App\ContentModule\Resources\CouponResource\Pages;

use Filament\Actions\CreateAction;
use App\ContentModule\Resources\CouponResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListCoupons extends ListRecords
{
    protected static string $resource = CouponResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function getHeaderActions(): array {
        return [
            CreateAction::make(),
        ];
    }

}
