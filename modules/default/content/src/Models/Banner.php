<?php

namespace App\ContentModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use App\DefaultPanel\Traits\Publishable;

class Banner extends Model implements HasMedia {
    use  Publishable, HasTranslations,InteractsWithMedia, HasFactory;
    protected array $translatable = ['name','image'];
    protected $fillable = [
        'status',
    ];


}
