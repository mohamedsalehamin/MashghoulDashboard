<?php

namespace App\ContentModule\Models;

use App\DefaultPanel\Traits\Publishable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Point extends Model implements HasMedia {
    use  Publishable, InteractsWithMedia;

    protected $guarded = ['id'];
    protected $casts = [
        'meta_data' => 'array'
    ];
    use HasFactory;
}
