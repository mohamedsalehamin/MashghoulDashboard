<?php

namespace App\ContentModule\Resources\CouponResource\Pages;

use App\ContentModule\Resources\CouponResource;
use App\Models\CancellationReason;
use Filament\Resources\Pages\ViewRecord;
use Str;

class ViewCoupon extends ViewRecord {
    protected static string $resource = CouponResource::class;

}
