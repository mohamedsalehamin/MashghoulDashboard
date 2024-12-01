<?php

namespace App\Models;

use App\ContentModule\Models\Level;
use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointsExchange extends Model {
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'expired_at' => 'datetime',
    ];
    public function price(): Attribute {

        return Attribute::make(
            get: fn($value) => Money::parse($value)
        );
    }
    public function plan() {
        return $this->belongsTo(Level::class, 'level_id');
    }
}
