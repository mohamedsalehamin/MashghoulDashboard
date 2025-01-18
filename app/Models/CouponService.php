<?php

namespace App\Models;

use App\CatalogModule\Models\Product;
use App\CatalogModule\Models\Service;
use App\UsersModule\Models\Provider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponService extends Model {
    protected $guarded = ['id'];
    use HasFactory;

    public function provider() {
        return $this->belongsTo(Provider::class);
}

    public function service() {
        return $this->belongsTo(Service::class);
}
    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsToMany {
        return $this->belongsToMany(Product::class, 'coupon_service_product', 'coupon_service_id', 'product_id');
    }
}
