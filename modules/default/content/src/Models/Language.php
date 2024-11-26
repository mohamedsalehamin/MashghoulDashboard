<?php

namespace App\ContentModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use App\DefaultPanel\Traits\Publishable;

class Language extends Model {
    use  HasTranslations, HasFactory,Publishable;

    protected array $translatable = ['name'];
    protected $guarded = ['id'];


}
