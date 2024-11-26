<?php

namespace App\UsersModule\Models;


use App\ContentModule\Models\City;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Theamostafa\Wallet\Traits\HasWallet;

class Provider extends Model implements HasMedia {
    use InteractsWithMedia,HasWallet;

    protected $guarded = ['id'];
    protected $casts = [
        'location' => Point::class,
        'meta_data'=>'array'
    ];

    public function city() {
        return $this->belongsTo(City::class);
    }
    protected function location(): Attribute {
        return Attribute::make(
            set: function ($coordinate) {
                return (new Point($coordinate['lat'], $coordinate['lng']))->toSqlExpression($this->getConnection());
            }
        );
    }
}
