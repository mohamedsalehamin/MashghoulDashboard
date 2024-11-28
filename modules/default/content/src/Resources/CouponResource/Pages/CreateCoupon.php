<?php

namespace App\ContentModule\Resources\CouponResource\Pages;

use App\ContentModule\Resources\CouponResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupon extends CreateRecord {

    protected static string $resource = CouponResource::class;

    protected function getHeaderActions(): array {

        return [
        ];
    }


    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }

}

