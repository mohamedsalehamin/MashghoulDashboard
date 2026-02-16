<?php

namespace App\ContentModule\Resources\CouponResource\Pages;

use App\ContentModule\Resources\CouponResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function getHeaderActions(): array {

        return [
        ];
    }


    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }

}

