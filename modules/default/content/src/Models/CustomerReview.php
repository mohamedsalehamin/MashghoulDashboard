<?php

namespace App\ContentModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use App\DefaultPanel\Traits\Publishable;

class CustomerReview extends Model implements HasMedia {
    use HasFactory, HasTranslations, Publishable, InteractsWithMedia;

    public array $translatable = ['customer_name', 'review'];
    protected $fillable = [
        'customer_name',
        'review',
        'rate',
        'status'
    ];

}
