<?php

namespace App\CatalogModule\Models;

use App\DefaultPanel\Traits\Publishable;
use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Plan extends Model {

    use HasFactory, Publishable, HasTranslations;

    public array $translatable = ['name'];
    protected $guarded = ['id'];
    protected $casts = [
        'meta_data' => 'array'
    ];

    public function price(): Attribute {

        return Attribute::make(
            get: fn($value) => Money::parse($value)
        );
    }

    public function subscriptions() {
        return $this->hasMany(Subscription::class);
    }

    public function subscribe() {

        $subscription = $this->subscriptions()->create([
            'user_id' => doctor()->user->id,
            'status' => 'pending',
            'price' => $this->price->getAmount(),
            'start_date' => now(),
            'end_date' => now()->addDays($this->meta_data['days_count'] ?? 30),
        ]);
        return $subscription->pay()['meta_data']['invoiceURL'];
    }

}
