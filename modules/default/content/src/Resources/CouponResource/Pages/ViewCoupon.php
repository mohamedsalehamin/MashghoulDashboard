<?php

namespace App\ContentModule\Resources\CouponResource\Pages;

use App\ContentModule\Resources\CouponResource;
use App\Models\CancellationReason;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Str;

class ViewCoupon extends ViewRecord
{
    protected static string $resource = CouponResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}
