<?php

namespace App\ContentModule\Models;

use App\DefaultPanel\Traits\Publishable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Level extends Model implements HasMedia {
    use HasTranslations, Publishable, InteractsWithMedia;

    protected $guarded = ['id'];
    public $translatable = ['title'];
    protected $table = 'points_levels';
    use HasFactory;


}
